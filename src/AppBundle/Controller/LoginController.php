<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Login controller.
 *
 * @Route("/")
 */
class LoginController extends Controller
{
    /**
     * Login an User into the system.
     *
     * @Route("/login/{_locale}", name="login")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        //Clean temporal users.
        $this->get('app.clean')->users();

            $authenticationUtils = $this->get('security.authentication_utils');
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render(
                'front/login.html.twig',
                array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                )
            );
    }
    /**
     * @Route("/logout/{_locale}", name="logout")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * Reset Password via E-Mail
     *
     * @Route("/reset/password/{_locale}", name="reset")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function resetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $msg="";
        //
        if($request->request->count() >= 1){
            $username = $request->request->get('userna');
            // check if exist.
            $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.username = ?1');
            $query->setParameter(1, $username);
            $user = $query->getResult();
            if($user!=null){
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user[0]);
                $time = date("d:m:y h:i:s A");
                $code_bcrypt = $encoder->encodePassword($time, null);
                $code_bcrypt = substr($code_bcrypt,7,15);
                // Encode the encoded password :-)
                $password = $encoder->encodePassword($code_bcrypt, null);
                // set the password and send via email.
                $user[0]->setPassword($password);
                $em->persist($user[0]);
                $em->flush();
                //
                $msg="Password successfully reset and sent to your E-Mail.";
                mail($username,"Your 3Swings password has been reset.","This is your new 3Swings password: ".$code_bcrypt,"From:noreply@3swings.com");
                //

            }
            else{
                $msg="That user dont exist.";
            }
        }
        return $this->render(
            'front/reset.html.twig', array('msg'=>$msg));
    }
}