<?php

namespace AppBundle\Controller\Payments;

use AdminBundle\Entity\Codes;
use AdminBundle\Entity\Codesfamily;
use AdminBundle\Entity\Order;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\Result;

class ActivateCodeController extends Controller
{
    /**
     * Payment on activate a code
     *
     * @Route("/user/codes/activate/user/{userid}/{_locale}", name="prepare_codes_activate")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function prepareAction(Request $request, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $current = $userdata[0]->getActivationcode();
        //
        $query2 = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1');
        $query2->setParameter(1, $current);
        $the_code = $query2->getResult();
        //
        $precio = $the_code[0]->getPlanid()->getPrecio();
        $order = new Order($precio);
        $em->persist($order);
        $em->flush();

        $config = [
            'paypal_express_checkout' => [
                'return_url' => $this->generateUrl('app_orders_codes_activate', [
                    'orderid' => $order->getId(),
                    'userid' => $userid
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_codes_activate_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount' => $order->getAmount(),
            'currency' => 'USD',
            'predefined_data' => $config
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ppc = $this->get('payment.plugin_controller');
            $ppc->createPaymentInstruction($instruction = $form->getData());

            $order->setPaymentInstruction($instruction);

            $em->persist($order);
            $em->flush($order);

            return $this->redirect($this->generateUrl('app_orders_codes_activate', [
                'orderid' => $order->getId(),
                'userid' => $userid
            ]));
        }

        return $this->render('front/payments/activate.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'userid' => $userid
        ));
    }

    /**
     * Payment create
     *
     * @Route("/user/codes/activate/user/{userid}/order/{orderid}/create/{_locale}", name="app_orders_codes_activate")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCreateAction($userid, $orderid, $planid)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('AdminBundle:Order')->find($orderid);
        $payment = $this->createPayment($order);
        $ppc = $this->get('payment.plugin_controller');
        $result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        if ($result->getStatus() === Result::STATUS_SUCCESS) {
            return $this->redirect($this->generateUrl('app_orders_codes_activatecomplete', [
                'orderid' => $order->getId(),
                'userid' => $userid
            ]));
        }
        if ($result->getStatus() === Result::STATUS_PENDING) {
            $ex = $result->getPluginException();

            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();

                if ($action instanceof VisitUrl) {
                    return $this->redirect($action->getUrl());
                }
            }
        }
        return $this->redirectToRoute('app_payment_cancel');
    }

    /**
     * Payment complete
     *
     * @Route("/user/codes/activate/user/{userid}/order/{orderid}/complete/{_locale}", name="app_orders_codes_activatecomplete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCompleteAction($orderid, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($userid);
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $current = $userdata[0]->getActivationcode();
        //
        $this->Markcode($current);
        //
        $query2 = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1');
        $query2->setParameter(1, $current);
        $the_code = $query2->getResult();
        $owner_id = $the_code[0]->getUserid()->getId();
        //
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($this->getUser()->getUsername());
        $em->persist($family);
        $em->flush();
        //
        $used_codes = $this->get('app.generateCodes')->getremaining($the_code[0]->getCodesfamilyid()->getId(), $owner_id);
        if ($used_codes == 3) {
            $cost = $the_code[0]->getPlanid()->getPrecio();
            $percent = $the_code[0]->getPlanid()->getGanancia();
            //
            $tres = ((($cost * $percent) / 100) * 3);
            $amount = $cost + $tres;
            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
            $query->setParameter(1, $owner_id);
            $sender = $query->getResult();
            $current_money = $sender[0]->getMoney();
            $money = $current_money + $amount;
            $sender[0]->setMoney($money);
            $em->persist($sender[0]);
            $em->flush();
            // Send the data to the service.
            $logs = 'You have your money back plus $' . $tres . ' for using three codes.';
            $this->get('app.setLogs')->setuserlogs($logs, $owner_id);
            // Send the rest to the admin.
            $admin_id = $this->get('app.sendMoney')->getadminid();
            $logs = 'El usuario ' . $owner_id . ' ha consumido sus 3 códigos y ha obtenido una ganancia de $' . $tres . '';
            $this->get('app.setLogs')->setuserlogs($logs, $admin_id);
            // Delete the codes
            $this->get('app.clean')->codes($the_code[0]->getCodesfamilyid()->getId());
            // send email to admin
            $this->get('app.mailsender')->send('Tres codigos consumidos', 'El usuario '.$owner_id.' ha consumido sus 3 códigos y ha obtenido una ganancia de $'.$tres.'');
        }
        // Generarle 3 codigos por activar 1
        for ($i = 1; $i <= 3; $i++) {
            // Preparing the code.
            $time = date("d:m:y h:i:s A");
            $code_raw = $time . ";userid=" . $userid . ',' . $i;
            // Encrypt the code.
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $code_bcrypt = $encoder->encodePassword($code_raw, null);
            $code_bcrypt = substr($code_bcrypt,6,30);
            $codes = new Codes();
            $codes->setCode($code_bcrypt);
            $codes->setUserid($user);
            $codes->setActive(0);
            $codes->setPlanid($the_code[0]->getPlanid());
            // Set the code family
            $codes->setCodesfamilyid($family);
            //
            $em->persist($codes);
            $em->flush();
        }
        return $this->redirectToRoute('user_panel');
    }

    /**
     * Payment Cancel
     *
     * @Route("/user/codes/payment/cancel/{_locale}", name="app_codes_activate_cancel")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCancelAction()
    {
        return $this->render('front/error/cancel.html.twig');
    }

    // Creating a Payment instance
    private function createPayment($order)
    {
        $instruction = $order->getPaymentInstruction();
        $pendingTransaction = $instruction->getPendingTransaction();

        if ($pendingTransaction !== null) {
            return $pendingTransaction->getPayment();
        }

        $ppc = $this->get('payment.plugin_controller');
        $amount = $instruction->getAmount() - $instruction->getDepositedAmount();

        return $ppc->createPayment($instruction->getId(), $amount);
    }

    function Markcode($code)
    {
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1 and p.active = 0');
        $query->setParameter(1, $code);
        $codeobject = $query->getResult();
        $codeobject[0]->setActive(1);
        $em->persist($codeobject[0]);
        $em->flush();

        // Send the statistic 1 to the Code Owner
        $userid = $codeobject[0]->getUserid()->getId();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $stats1 = $userdata[0]->getStats1();
        $userdata[0]->setStats1($stats1 + 1);
        $em->persist($userdata[0]);
        $em->flush();
    }

    // Get the last used code.
    function getRemaining($userid, $plan)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.userid = ?1 and p.active = 1 and p.planid = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $plan);
        $result = $query->getResult();
        return $result;
    }
}
