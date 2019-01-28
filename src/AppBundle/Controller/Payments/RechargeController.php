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

class RechargeController extends Controller
{
    /**
     * Payment on activate a code
     *
     * @Route("/user/recharge/{ammount}/{_locale}", name="prepare_recharge")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function prepareAction(Request $request, $ammount)
    {
        $em = $this->getDoctrine()->getManager();
        //
        if($this->getUser()==null){
            $this->redirectToRoute('404');
        }
        $percent = ($ammount * 3) / 100;
        $order = new Order($ammount + $percent);
        $em->persist($order);
        $em->flush();

        $config = [
            'paypal_express_checkout' => [
                'return_url' => $this->generateUrl('app_orders_recharge', [
                    'orderid' => $order->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_recharge_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
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

            return $this->redirect($this->generateUrl('app_orders_recharge', [
                'orderid' => $order->getId()
            ]));
        }
        return $this->render('front/payments/activate.html.twig', array(
            'order' => $order,
            'ammount' => $ammount,
            'form' => $form->createView()
        ));
    }

    /**
     * Payment create
     *
     * @Route("/user/recharge/order/{orderid}/create/{_locale}", name="app_orders_recharge")
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
            return $this->redirect($this->generateUrl('app_orders_recharge_done', [
                'orderid' => $order->getId()
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
        return $this->redirectToRoute('app_recharge_cancel');
    }

    /**
     * Payment complete
     *
     * @Route("/user/recharge/order/{orderid}/complete/{_locale}", name="app_orders_recharge_done")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCompleteAction($orderid, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        $userid = $this->getUser()->getId();
        $order = $em->getRepository('AdminBundle:Order')->find($orderid);
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $current = $userdata[0]->getMoney();
        $userdata[0]->setMoney($order->getAmount()+ $current);
        $em->persist($userdata[0]);
        $em->flush();
        //
        return $this->redirectToRoute('user_panel');
    }

    /**
     * Payment Cancel
     *
     * @Route("/user/recharge/payment/cancel/{_locale}", name="app_recharge_cancel")
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