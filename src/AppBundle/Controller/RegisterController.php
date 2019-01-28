<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Order;
use AdminBundle\Entity\Userdata;
use AdminBundle\Entity\Usermailcodes;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\User;
use AdminBundle\Entity\Photos;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\Result;

class RegisterController extends Controller
{
    /**
     * Terms and Conditions page
     *
     * @Route("/terms_and_conditions/{_locale}", name="terms")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function termsAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $terms = $em->getRepository('AdminBundle:Settings')->find(1);
        return $this->render('front/terms.html.twig', array('terms'=> $terms, 'locale'=>$_locale));
    }

    /**
     * Register Page
     *
     * @Route("/join/{_locale}", name="register_test")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        //Clean temporal users.
        $this->get('app.clean')->users();

        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // checking the username again
            $username = $user->getUsername();
            $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.username = ?1 ');
            $query->setParameter(1, $username);
            $user_check = $query->getResult();
            if(count($user_check)>0)
            {
                return $this->redirectToRoute('user_error');
            }
            // checking the paypal user again
            $pay = $user->getPaypal();
            $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.paypal = ?1 ');
            $query->setParameter(1, $pay);
            $user_check = $query->getResult();
            if(count($user_check)>0)
            {
                return $this->redirectToRoute('paypal_error');
            }
            $code_raw = $request->request->get('user_code');
            $code_raw = trim($code_raw);
            if(strtolower($code_raw)=="free"){
                $this->registerfree2($user);
                return $this->redirectToRoute('homepage');
            }
            // Checking free codes NEW!!!!
            if(strlen($code_raw)<=21){
                $code = $this->getFreecode($code_raw);
                if(count($code)==0)
                {
                    return $this->redirectToRoute('code_error');
                }
                else{
                    $this->registerfree($user,$code_raw);
                    return $this->redirectToRoute('homepage');
                }
            }
            else{
            $code = $this->getCode($code_raw);
            if(count($code)==0)
            {
                return $this->redirectToRoute('code_error');
            }
            }
            // get the file
            $file = $user->getFile();
            $photos = new Photos();
            if($file!=null)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
                $file->move($imagesdir, $fileName);
                $photos->setPath('uploads/profiles/'.$fileName);
                $photos->setUserid($user);
            }
            else{
                $photos->setPath('images/user.png');
                $photos->setUserid($user);
            }
            // Encrypt the password
            $p = $user->getPassword();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($p, $user->getSalt());
            $user->setPassword($password);
            $user->setUsername($user->getEmail());
            $user->setRole('ROLE_USER');
            $user->setActive(0);
            $user->setTemp(1);
            $em->persist($user);
            $em->persist($photos);
            $em->flush();
            $userid= $user->getId();
            $user2 = $em->getRepository('AdminBundle:User')->find($userid);
            // Filling Userdata Entity.
            $userdata = new Userdata();
            //$userdata->setMoney($code[0]->getPlanid()->getPrecio());
            $userdata->setMoney(0);
            $userdata->setUserid($user2);
            $userdata->setPlanid($this->getCheap());
            $userdata->setLevel(1);
            $userdata->setStats1(0);
            $userdata->setStats2(0);
            $userdata->setStats3(0);
            $userdata->setFirst($code[0]->getPlanid()->getPrecio());
            $userdata->setInvitationcode($code_raw);
            $userdata->setPlandate(date('Y-m-d'));
            // Quien es el padre en el arbol.
            $userdata->setDad($code[0]->getUserid()->getUsername());
            $em->persist($userdata);
            $em->flush();
            return $this->redirectToRoute('prepare_payment', array('userid'=>$userid));
        }

        return $this->render('front/register.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
    /**
     * Payment on register
     *
     * @Route("/payment/user/{userid}/{_locale}", name="prepare_payment")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function prepareAction(Request $request, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        //$user = $em->getRepository('AdminBundle:User')->find($userid);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        // get the first payment of the user.
        $amount = $userdata[0]->getFirst();
        $order = new Order($amount);
        $em->persist($order);
        $em->flush();

        $config = [
            'paypal_express_checkout' => [
                'return_url' => $this->generateUrl('app_orders_paymentcreate', [
                    'orderid' => $order->getId(),
                    'userid' => $userid
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_payment_cancel', ['orderid' => $order->getId(), 'userid' => $userid],UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount'   => $order->getAmount(),
            'currency' => 'USD',
            'predefined_data' => $config
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ppc = $this->get('payment.plugin_controller');
            $ppc->createPaymentInstruction($instruction = $form->getData());

            $order->setPaymentInstruction($instruction);

            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('app_orders_paymentcreate', [
                'userid' => $userid,
                'orderid' => $order->getId()
            ]));
        }

        return $this->render('front/payments/register.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
            'userid'=> $userid
        ));
    }
    /**
     * Payment create
     *
     * @Route("/payment/user/{userid}/order/{orderid}/create/{_locale}", name="app_orders_paymentcreate")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCreateAction($userid, $orderid)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('AdminBundle:Order')->find($orderid);
        $payment = $this->createPayment($order);
        $ppc = $this->get('payment.plugin_controller');
        //$result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        //$result->getStatus() === Result::STATUS_SUCCESS
        if (1==1) {
            return $this->redirect($this->generateUrl('app_orders_paymentcomplete', [
                'userid'=> $userid
            ]));
        }
//        if ($result->getStatus() === Result::STATUS_PENDING) {
//            $ex = $result->getPluginException();
//
//            if ($ex instanceof ActionRequiredException) {
//                $action = $ex->getAction();
//
//                if ($action instanceof VisitUrl) {
//                    return $this->redirect($action->getUrl());
//                }
//            }
//        }
         return $this->redirectToRoute('app_payment_cancel', array('userid'=> $userid, 'orderid'=>$orderid));
    }
    /**
     * Payment complete
     *
     * @Route("/payment/user/{userid}/complete/{_locale}", name="app_orders_paymentcomplete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCompleteAction($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($userid);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $code_raw = $userdata[0]->getInvitationcode();
        $code=$this->getCode($code_raw);
        //
        if($code==null){
            return $this->render('front/error/codenotfound.html.twig', array('code_raw'=>$code_raw));
        }
        $owner_id = $code[0]->getUserid()->getId();

        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        // Generate the code depends of the invitation.
        $this->get('app.generateCodes')->generatefirstCodes($code[0]->getPlanid(), $userid, $encoder);
        //
        $this->Markcode($code_raw);
        //
        $used_codes = $this->get('app.generateCodes')->getremaining($code[0]->getCodesfamilyid()->getId(), $owner_id);
        if($used_codes==3){
            $cost= $code[0]->getPlanid()->getPrecio();
            $percent= $code[0]->getPlanid()->getGanancia();
            $tres = ((($cost*$percent)/100)*3);
            $amount = $cost + $tres;
            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
            // fix
            $query->setParameter(1, $owner_id);
            $sender = $query->getResult();
            $current_money = $sender[0]->getMoney();
            $money= $current_money+$amount;
            $sender[0]->setMoney($money);
            $em->persist($sender[0]);
            $em->flush();
            // Send the data to the service.
            $logs = 'You have your money back plus $'.$tres.' for using three codes.';
            $this->get('app.setLogs')->setuserlogs($logs, $owner_id);
            // Get the admin id.
            $admin_id = $this->get('app.sendMoney')->getadminid();
            $logs = 'El usuario '.$owner_id.' ha consumido sus 3 códigos y ha obtenido una ganancia de $'.$tres.'';
            $this->get('app.setLogs')->setuserlogs($logs, $admin_id);
            // Delete the codes
            $this->get('app.clean')->codes($code[0]->getCodesfamilyid()->getId());
            // send email to admin
            $this->get('app.mailsender')->send('Tres codigos consumidos', 'El usuario '.$owner_id.' ha consumido sus 3 códigos y ha obtenido una ganancia de $'.$tres.'');
        }
        // Send the message to the new user.
        // Send the data to the service.
        $logs = 'Welcome to 3Swings, Enjoy!!!.';
        $this->get('app.setLogs')->setuserlogs($logs, $user->getId());
        // Activate the user
        $user->setActive(1);
        $user->setTemp(0);
        $em->persist($user);
        $em->flush();
        // send email to admin
        $this->get('app.mailsender')->send('Usuario registrado en el 3swings.com', 'El usuario '.$user->getUsername().' se ha registrado correctamente en el sistema.');
        //
        return $this->redirectToRoute('homepage');
    }
    /**
     * Payment Cancel
     *
     * @Route("/payment/user/{userid}/order/{orderid}/cancel/{_locale}", name="app_payment_cancel")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paymentCancelAction($userid, $orderid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($userid);
        $order = $em->getRepository('AdminBundle:Order')->find($orderid);
        $em->remove($user);
        $em->flush();
        $em->remove($order);
        $em->flush();
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

    /**
     * Jquery Load
     *
     * @Route("/dynamic_code_check/{_locale}", name="dynamic_code")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function checkAction(Request $request)
    {
        $code_raw = $request->request->get('user_code');
        $code = $this->getCode($code_raw);
        if(count($code)>0)
        {
            $correct = true;
        }
        else $correct = false;
        //fix
        if($code!=null){
            $the_code= $code[0];
        }
        else $the_code=null;
        return $this->render('front/dynamic/message.html.twig', array('correct' => $correct, 'code'=> $the_code));
    }

    /**
     * Jquery Load for users
     *
     * @Route("/dynamic_user_check/{_locale}", name="dynamic_user")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */

    public function check2Action(Request $request)
    {
        $user = $request->request->get('user');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.email = ?1 ');
        $query->setParameter(1, $user);
        $user = $query->getResult();
        if(count($user)>0)
        {
            $found = true;
        }
        else $found = false;
        return $this->render('front/dynamic/message2.html.twig', array('found' => $found));
    }

    /**
     * Check paypal
     *
     * @Route("/dynamic_paypal_check/{_locale}", name="dynamic_paypal")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */

    public function paypalcheckAction(Request $request)
    {
        $pay = $request->request->get('pay');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.paypal = ?1 ');
        $query->setParameter(1, $pay);
        $user = $query->getResult();
        if(count($user)>0)
        {
            $found = true;
        }
        else $found = false;
        return $this->render('front/dynamic/message3.html.twig', array('found' => $found));
    }

    /**
     * Code Error
     *
     * @Route("/join/code_error/{_locale}", name="code_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */

    public function errorAction(Request $request)
    {
        return $this->render('front/error/error.html.twig');
    }

    /**
     * User Error
     *
     * @Route("/join/user_error/{_locale}", name="user_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function usererrorAction(Request $request)
    {
        return $this->render('front/error/user.html.twig');
    }
    /**
     * Paypal Error
     *
     * @Route("/join/paypal/{_locale}", name="paypal_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paypalAction(Request $request)
    {
        return $this->render('front/error/paypal.html.twig');
    }
    // Check if the code is correct.
    function getCode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query5 = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1 and p.active = 0');
        $query5->setParameter(1, $code);
        $result = $query5->getResult();
        return $result;
    }
    // Get the last used code.
    function getRemaining($userid, $plan){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.userid = ?1 and p.active = 1 and p.planid = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $plan);
        $result = $query->getResult();
        return $result;
    }
    // Get the code Id.
    function Markcode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1 and p.active = 0');
        $query->setParameter(1, $code);
        $codeobject = $query->getResult();
        $codeobject[0]->setActive(1);
        $em->persist($codeobject[0]);
        $em->flush();

        // Send the statistic 1 to the Code Owner
        $userid= $codeobject[0]->getUserid()->getId();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $stats1 = $userdata[0]->getStats1();
        $userdata[0]->setStats1($stats1+1);
        $em->persist($userdata[0]);
        $em->flush();
    }
    function getCheap(){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.level = 1');
        $cheap = $query->getResult();
        return $cheap[0];
    }
    // Register a free user NEW!!!!
    function registerfree(User $user, $code_raw){
        $em = $this->getDoctrine()->getManager();
        // get the file
        $file = $user->getFile();
        $photos = new Photos();
        if($file!=null)
        {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
            $file->move($imagesdir, $fileName);
            $photos->setPath('uploads/profiles/'.$fileName);
            $photos->setUserid($user);
        }
        else{
            $photos->setPath('images/user.png');
            $photos->setUserid($user);
        }
        // Encrypt the password
        $p = $user->getPassword();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($p, $user->getSalt());
        $user->setPassword($password);
        $user->setUsername($user->getEmail());
        // Role temp porque no ha insertado el numero todavia.
        $user->setRole('ROLE_TEMP');
        //
        $user->setActive(1);
        $user->setTemp(0);
        $em->persist($user);
        $em->persist($photos);
        $em->flush();
        $userid= $user->getId();
        $user2 = $em->getRepository('AdminBundle:User')->find($userid);
        // Filling Userdata Entity.
        $userdata = new Userdata();
        $userdata->setMoney(0);
        $userdata->setUserid($user2);
        // Metiendole el plan gratis
        $query = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.level=0');
        $free = $query->getResult();
        // Get the code object
        $code=$this->getFreecode($code_raw);
        $userdata->setPlanid($free[0]);
        $userdata->setLevel(0);
        $userdata->setStats1(0);
        $userdata->setStats2(0);
        $userdata->setStats3(0);
        $userdata->setFirst(0);
        $userdata->setPlandate(date('Y-m-d'));
        //El padre en el arbol.
        $userdata->setDad($code[0]->getUserid()->getId());
        $em->persist($userdata);
        $em->flush();
        //
        $owner_id = $code[0]->getUserid()->getId();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        // Generate the code depends of the invitation.
        $this->get('app.generateCodes')->generatefreefirstCodes($userid, $encoder);
        //
        $this->Markfreecode($code_raw);
        //
        $used_codes = $this->get('app.generateCodes')->getfreeremaining($code[0]->getCodesfamilyid()->getId(), $owner_id);
        if($used_codes==3){
            $ownername= $code[0]->getUserid()->getUsername().'-FREE';
            // Checking for free codes.
            $query = $em->createQuery('SELECT p FROM AdminBundle:Codesfamily p WHERE p.name = ?1');
            $query->setParameter(1, $ownername);
            $found = $query->getResult();
            if(count($found)>0){
                $logs = 'You use 3 free codes';
                $this->get('app.setLogs')->setuserlogs($logs, $owner_id);
                // Get the admin id.
                $admin_id = $this->get('app.sendMoney')->getadminid();
                $logs = 'El usuario '.$owner_id.' ha consumido sus 3 códigos gratis pero no gana nada.';
                $this->get('app.setLogs')->setuserlogs($logs, $admin_id);
                // Delete the codes
                $this->get('app.clean')->codes($code[0]->getCodesfamilyid()->getId());
                // send email to admin
                $this->get('app.mailsender')->send('Tres codigos consumidos', 'El usuario '.$owner_id.' ha consumido sus 3 códigos gratis pero no gana nada.');
            }
            else{
                $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
                $query->setParameter(1, $owner_id);
                $ownerdata = $query->getResult();
                //
                $query = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.level = 1');
                $theplan = $query->getResult();

                if($ownerdata[0]->getPlanid()->getLevel()>0){
                    $theplan= $ownerdata[0]->getPlanid();
                }
                else{
                    $theplan=$theplan[0];
                }
                $this->get('app.generateCodes')->generateTenCodes($theplan,$owner_id,$encoder);
                // Send the data to the service.
                $logs = 'You use 3 free codes and you gain 3 codes: '.$theplan->getName();
                $this->get('app.setLogs')->setuserlogs($logs, $owner_id);
                // Get the admin id.
                $admin_id = $this->get('app.sendMoney')->getadminid();
                $logs = 'El usuario '.$owner_id.' ha consumido sus 3 códigos gratis y ha ganado 3 codigos '.$theplan->getName();
                $this->get('app.setLogs')->setuserlogs($logs, $admin_id);
                // Delete the codes
                $this->get('app.clean')->codes($code[0]->getCodesfamilyid()->getId());
                // send email to admin
                $this->get('app.mailsender')->send('Tres codigos consumidos', 'El usuario '.$owner_id.' ha consumido sus 3 códigos gratis y ha ganado 3 codigos del Nivel '.$theplan->getName());
            }
        }
        // Send the message to the new user.
        // Send the data to the service.
        $logs = 'Welcome to 3Swings as a free user.';
        $this->get('app.setLogs')->setuserlogs($logs, $user->getId());
        // send email to admin
        $this->get('app.mailsender')->send('Usuario gratis registrado en el 3swings.com', 'El usuario '.$user->getUsername().' se ha registrado correctamente en el sistema como usuario gratis con Nivel 0.');
        //
        return $this->redirectToRoute('homepage');

    }
    function Markfreecode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Freecodes p WHERE p.code = ?1 and p.active = 0');
        $query->setParameter(1, $code);
        $codeobject = $query->getResult();
        $codeobject[0]->setActive(1);
        $em->persist($codeobject[0]);
        $em->flush();
        // Send the statistic 1 to the Code Owner
        $userid= $codeobject[0]->getUserid()->getId();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $stats1 = $userdata[0]->getStats1();
        $userdata[0]->setStats1($stats1+1);
        $em->persist($userdata[0]);
        $em->flush();
    }
    function getFreecode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query5 = $em->createQuery('SELECT p FROM AdminBundle:Freecodes p WHERE p.code = ?1 and p.active = 0');
        $query5->setParameter(1, $code);
        $result = $query5->getResult();
        return $result;
    }
    // Register free without code
    function registerfree2(User $user){
        $em = $this->getDoctrine()->getManager();
        // get the file
        $file = $user->getFile();
        $photos = new Photos();
        if($file!=null)
        {
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
            $file->move($imagesdir, $fileName);
            $photos->setPath('uploads/profiles/'.$fileName);
            $photos->setUserid($user);
        }
        else{
            $photos->setPath('images/user.png');
            $photos->setUserid($user);
        }
        // Encrypt the password
        $p = $user->getPassword();
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($p, $user->getSalt());
        $user->setPassword($password);
        $user->setUsername($user->getEmail());
        // ROLE_TEMP para que no entren a ningun lado.
        $user->setRole('ROLE_TEMP');
        // Aun no ha entrado el codigo. NEW!!!!
        $user->setActive(2);
        //
        $user->setTemp(0);
        $em->persist($user);
        $em->persist($photos);
        $em->flush();
        // Security Email Send. NEW!!!!
        $this->mailcode($user);
        //
        $userid= $user->getId();
        $user2 = $em->getRepository('AdminBundle:User')->find($userid);
        // Filling Userdata Entity.
        $userdata = new Userdata();
        $userdata->setMoney(0);
        $userdata->setUserid($user2);
        // Metiendole el plan gratis
        $query = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.level=0');
        $free = $query->getResult();
        // Get the code object
        $userdata->setPlanid($free[0]);
        $userdata->setLevel(0);
        $userdata->setStats1(0);
        $userdata->setStats2(0);
        $userdata->setStats3(0);
        $userdata->setFirst(0);
        $userdata->setPlandate(date('Y-m-d'));
        //El padre en el arbol.
        $admin = $this->get('app.sendMoney')->getadminid();
        $userdata->setDad($admin);
        $em->persist($userdata);
        $em->flush();
        //
        //$owner_id = $admin;
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        // Generate the code depends of the invitation.
        $this->get('app.generateCodes')->generatefreefirstCodes($userid, $encoder);
        // Send the message to the new user.
        // Send the data to the service.
        $logs = 'Welcome to 3Swings as a free user.';
        $this->get('app.setLogs')->setuserlogs($logs, $user->getId());
        // send email to admin
        $this->get('app.mailsender')->send('Usuario gratis registrado en el 3Swings.com', 'El usuario '.$user->getUsername().' se ha registrado correctamente en el sistema como usuario gratis con Nivel 0.');
        //
        return $this->redirectToRoute('homepage');
    }
    /* This part is for the registration security codes sent by email to the user.*/
    function mailcode($user){
        $thecode = $this->get('app.generateCodes')->mailcode($user->getId());
        $em = $this->getDoctrine()->getManager();
        $umc = new Usermailcodes();
        $umc->setCode($thecode);
        $umc->setUserid($user);
        $em->persist($umc);
        $em->flush();
        // Send the email.
        $this->get('app.mailsender')->send_mail($user->getUsername(),'3Swings Security Code', "This is your security code: ".$thecode." , you must login first on our website, and put this code to successfully create your user. Use this link to login: https://3swings.com/web/login/en . If you own a business or service, feel free to promote it in the follow link: https://3swings.com/web/user/services/en . Visit our frequent asked question (FAQ) in this link https://3swings.com/web/faq/en if you have some issues.","noreply@3Swings.com");

    }
}