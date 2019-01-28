<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Codes;
use AdminBundle\Entity\Codesfamily;
use AdminBundle\Entity\Photos;
use AdminBundle\Entity\User;
use AdminBundle\Entity\Userextractions;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\PropertyAccess\Exception\AccessException;


class ProfileControler extends Controller
{
    /**
     * User Panel
     * @Route("/user/panel/{_locale}", name="user_panel")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function panelAction(Request $request)
    {
        if($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        // Get the Avatar.
        $query = $em->createQuery('SELECT p FROM AdminBundle:Photos p WHERE p.userid = ?1 ');
        $query->setParameter(1, $id);
        $photos = $query->getResult();
        // Get the Codes.
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.userid = ?1 ');
        $query->setParameter(1, $id);
        $codes = $query->getResult();
        // Get the Free Codes NEW!!!
        $query = $em->createQuery('SELECT p FROM AdminBundle:Freecodes p WHERE p.userid = ?1 ');
        $query->setParameter(1, $id);
        $freecodes = $query->getResult();
        // Get Userdata
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
        $query->setParameter(1, $id);
        $userdata = $query->getResult();
        // Not used codes.
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.userid = ?1 AND p.active = 0');
        $query->setParameter(1, $id);
        $notused = $query->getResult();
        // Get the plans.
        $plans = $em->getRepository('AdminBundle:Plan')->findAll();
        // Requests
        if ($request->request->count() >= 1) {
            if ($request->request->get('userid_code')) {
                $user_id = $request->request->get('userid_code');
                $plan_id = $request->request->get('plan');
                $metodo = $request->request->get('metodo');
                if($metodo=='2')
                return $this->redirectToRoute('prepare_codes_payment', array('planid'=>$plan_id, 'userid'=>$user_id));
                else{
                    $user = $em->getRepository('AdminBundle:User')->find($user_id);
                    $plan = $em->getRepository('AdminBundle:Plan')->find($plan_id);
                    // Get the Userdata
                    $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
                    $query->setParameter(1, $id);
                    $userdata = $query->getResult();
                    //
                    if($userdata[0]->getMoney() >=$plan->getPrecio())
                    {
                        //FIX CODES FAMILY
                        $family = new Codesfamily();
                        $family->setName($this->getUser()->getUsername());
                        $em->persist($family);
                        $em->flush();
                        //
                        for ($i = 1; $i <= 3; $i++) {
                            // Preparing the code.
                            $time = date("d:m:y h:i:s A");
                            $code_raw = $time . ";userid=" . $user_id .','.$i;
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
                        // Rest the money from the user.
                        $currency=$userdata[0]->getMoney();
                        $userdata[0]->setMoney($currency-$plan->getPrecio());
                        $em->persist($userdata[0]);
                        $em->flush();
                        return $this->redirectToRoute('user_panel',array('id'=> $user_id));
                    }
                    else{
                        return $this->redirectToRoute('money_error');
                    }
                }

            }
            if($request->request->get('amount')){
                $amount = $request->request->get('amount');
                $userid = $this->getUser()->getId();
                // Get the Userdata
                $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
                $query->setParameter(1, $userid);
                $userdata = $query->getResult();

                $today= date("d-m-y");
                //
                if($userdata[0]->getExtractiondate()!= $today)
                {
                    if($userdata[0]->getMoney() >= $amount)
                    {
                        $user = $em->getRepository('AdminBundle:User')->find($userid);
                        $userextractions = new Userextractions();
                        $userextractions->setAmount($amount);
                        $userextractions->setUserid($user);
                        $userextractions->setDate(date("d-m-y"));
                        $em->persist($userextractions);
                        $em->flush();
                        $userdata[0]->setExtractiondate($today);
                        $current = $userdata[0]->getMoney();
                        $userdata[0]->setMoney($current-$amount);
                        $em->persist($userdata[0]);
                        $em->flush();
                        // Send the email to the admin.
                        $message = "
<html>
<head>
</head>
<body>
<table border=\"1\">
    <thead style=\"background-color:#2e6da4;color:whitesmoke\">
    <tr>
        <th>ID Usuario</th>
        <th>Nombre</th>
        <th>Cantidad</th>
        <th>Fecha</th>
    </tr>
    </thead>
    <tbody style=\"text-align: center\">
    <tr>
        <td>" .$userid."</td>
        <td>".$user->getUsername()."</td>
        <td>". $amount ."</td>
        <td>".$today."</td>
    </tr>
    </tbody>
</table>
<br>
<a href=\"http://www.paypal.com\" target=\"_blank\"><button>PayPal</button></a>
</body>
</html>";
                        $this->get('app.mailsender')->send('Extraccion solicitada', $message);
                    }
                    else{
                        return $this->redirectToRoute('money_error');
                    }
                }
                else{
                    return $this->render('front/error/oneperday.html.twig');
                }
                return $this->render('front/messages/extractions.html.twig');
            }
            if ($request->request->get('code_activate')) {
                $code_raw=$request->request->get('code_activate');
                $metodo = $request->request->get('metodo');
                // checking the code again.
                $code = $this->getCode($code_raw);
                if(count($code)==0)
                {
                    return $this->render('front/error/error2.html.twig');
                }
                $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
                $query->setParameter(1, $id);
                $userdata = $query->getResult();
                $userdata[0]->setActivationcode($code_raw);
                $em->persist($userdata[0]);
                $em->flush();
                // Go to the process.
                $query2 = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1');
                $query2->setParameter(1, $code_raw);
                $the_code = $query2->getResult();
                //
                $precio = $the_code[0]->getPlanid()->getPrecio();
                // The user cant input their own codes.
                if($the_code[0]->getUserid()->getId()== $id){
                    return $this->render('front/error/error3.html.twig');
                }
                //
                if($metodo=='2'){
                    return $this->redirectToRoute('prepare_codes_activate', array('userid'=>$id));
                }
                else{
                    if($userdata[0]->getMoney() >=$precio)
                    {
                        $this->Markcode($code_raw);
                        //
                        $query2 = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1');
                        $query2->setParameter(1, $code_raw);
                        $the_code = $query2->getResult();
                        $owner_id = $the_code[0]->getUserid()->getId();
                        //
                        $used_codes = $this->get('app.generateCodes')->getremaining($the_code[0]->getCodesfamilyid()->getId(), $owner_id);
                        if($used_codes==3){
                            $cost= $the_code[0]->getPlanid()->getPrecio();
                            $percent= $the_code[0]->getPlanid()->getGanancia();
                            //
                            $tres = ((($cost*$percent)/100)*3);
                            $amount = $cost + $tres;
                            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
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
                        }
                        // Rest the money from the user.
                        $currency=$userdata[0]->getMoney();
                        $userdata[0]->setMoney($currency-$precio);
                        $em->persist($userdata[0]);
                        $em->flush();
                        // generarle codigos al usuario por activar 1.
                        $user = $em->getRepository('AdminBundle:User')->find($id);
                        //FIX CODES FAMILY
                        $family = new Codesfamily();
                        $family->setName($this->getUser()->getUsername());
                        $em->persist($family);
                        $em->flush();
                        //
                        for ($i = 1; $i <= 3; $i++) {
                            // Preparing the code.
                            $time = date("d:m:y h:i:s A");
                            $code_raw = $time . ";userid=" . $id .','.$i;
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
                    else{
                        return $this->redirectToRoute('money_error');
                    }
                }


            }
            // New!!! Recharge Paypal
            if ($request->request->get('amount_recharge')) {
                $ammount = $request->request->get('amount_recharge');
                return $this->redirectToRoute('prepare_recharge', array('ammount'=>$ammount));
            }
            // New!!! Level up user.
            if ($request->request->get('levelup')) {
                $id = $request->request->get('levelup');
                $plan = $em->getRepository('AdminBundle:Plan')->find($id);
                //
                $userdata[0]->setLevelup($id);
                $em->persist($userdata[0]);
                $em->flush();
                //
                return $this->redirectToRoute('prepare_levelup', array('ammount'=>$plan->getPrecio()));
            }
        }
        return $this->render('front/user/panel.html.twig', array('photos' => $photos[0], 'codes' => $codes, 'notused'=>$notused, 'userdata'=> $userdata[0], 'plans'=> $plans, 'freecodes' => $freecodes));
    }

    /**
     * User Profile Edit.
     *
     * @Route("/user/profile/edit/{_locale}", name="profile_edit")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        if($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($id);

        $editForm = $this->createForm('AppBundle\Form\ProfileType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $file = $user->getFile();
            $em = $this->getDoctrine()->getManager();
            $id = $user->getId();
            if($file != null)
            {
                //Delete the old photo.
                $delete = $em->createQuery('DELETE FROM AdminBundle:Photos p WHERE p.userid = ?1');
                $delete->setParameter(1, $user->getId());
                $delete->getResult();
                //
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
                $file->move($imagesdir, $fileName);
                $photos = new Photos();
                $path = trim('uploads/profiles/'.$fileName);
                $photos->setPath($path);
                $photos->setUserid($user);
                $em->persist($photos);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_panel');
        }

        return $this->render('front/user/profile.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }
    /**
     * User Password Edit.
     *
     * @Route("/user/profile/password/{_locale}", name="password_edit")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function passwordAction(Request $request)
    {
        if($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($id);

        $errors=array();
        if($request->request->count()>0){
            $current= $request->get('current_password');
            $new1= $request->get('new_password1');
            $new2= $request->get('new_password2');
            //
//            $p = $user->getPassword();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
//            $c_password = $encoder->encodePassword($current, $user->getSalt());
//            var_dump($c_password);
//            var_dump($p);
//            die;
//            if($c_password===$p)
//            {
                if($new1===$new2)
                {
                    $password = $encoder->encodePassword($new1, $user->getSalt());
                    $user->setPassword($password);
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('user_panel');
                }
                else{
                    $errors='New password mismatch';
                }
//            }
//            else{
//                $errors='Current password mismatch.';
//            }
        }
        return $this->render('front/user/password.html.twig', array('errors'=>$errors));
    }

    /**
     * User Delete
     *
     * @Route("/user/profile/delete/{_locale}", name="profile_delete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function deleteAction()
    {
        if($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($id);
        $user->setActive(0);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('logout');
    }

    /**
     * Money Error
     *
     * @Route("/user/profile/money_error/{_locale}", name="money_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */

    public function errorAction()
    {
        return $this->render('front/error/money.html.twig');
    }

    // Check if the code is correct.
    function getCode($code){
        $em = $this->getDoctrine()->getManager();
        $code = trim($code);
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.code = ?1 and p.active = 0');
        $query->setParameter(1, $code);
        $result = $query->getResult();
        return $result;
    }
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
    // Get the last used code.
    function getRemaining($userid, $plan){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Codes p WHERE p.userid = ?1 and p.active = 1 and p.planid = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $plan);
        $result = $query->getResult();
        return $result;
    }
}