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

class GetCodesController extends Controller
{
    /**
     * Payment on register
     *
     * @Route("/user/codes/{planid}/payment/user/{userid}/{_locale}", name="prepare_codes_payment")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function prepareAction(Request $request, $userid, $planid)
    {
        $em = $this->getDoctrine()->getManager();
        $plan = $em->getRepository('AdminBundle:Plan')->find($planid);
        $order = new Order($plan->getPrecio());
        $em->persist($order);
        $em->flush();

        $config = [
            'paypal_express_checkout' => [
                'return_url' => $this->generateUrl('app_orders_codes_paymentcreate', [
                    'orderid' => $order->getId(),
                    'userid' => $userid,
                    'planid' => $planid
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_codes_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
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

            return $this->redirect($this->generateUrl('app_orders_codes_paymentcreate', [
                'orderid' => $order->getId(),
                'userid' => $userid,
                'planid' => $planid
            ]));
        }

        return $this->render('front/payments/register.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'userid' => $userid,
            'planid' => $planid
        ));
    }

    /**
     * Payment create
     *
     * @Route("/user/codes/{planid}/payment/user/{userid}/order/{orderid}/create/{_locale}", name="app_orders_codes_paymentcreate")
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
            return $this->redirect($this->generateUrl('app_orders_codes_paymentcomplete', [
                'orderid' => $order->getId(),
                'userid' => $userid,
                'planid' => $planid
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
     * @Route("/user/codes/{planid}/payment/user/{userid}/order/{orderid}/complete/{_locale}", name="app_orders_codes_paymentcomplete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCompleteAction($orderid, $userid, $planid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($userid);
        $plan = $em->getRepository('AdminBundle:Plan')->find($planid);
        //FIX CODES FAMILY
        $family = new Codesfamily();
        $family->setName($this->getUser()->getUsername());
        $em->persist($family);
        $em->flush();
        //
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
            $codes->setPlanid($plan);
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
     * @Route("/user/codes/payment/cancel/{_locale}", name="app_codes_payment_cancel")
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
}
