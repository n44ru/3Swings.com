<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * Security Code check
     *
     * @Route("/security/check/{_locale}", name="security_check")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function termsAction(Request $request)
    {
        if($request->request->count()>0){
            $em = $this->getDoctrine()->getManager();
            $code = $request->request->get('code_code');
            $user = $this->getUser();
            //
            $query = $em->createQuery('SELECT p FROM AdminBundle:Usermailcodes p WHERE p.userid = ?1 and p.code = ?2');
            $query->setParameter(1, $user->getId());
            $query->setParameter(2, $code);
            $found = $query->getResult();
            if($found!=null){
                // poner activo y el rol de usuario.
                $user->setActive(1);
                $user->setRole('ROLE_USER');
                $em->persist($user);
                $em->flush();
                //
                return $this->redirectToRoute('homepage');
            }
            else{
                return $this->redirectToRoute('security_check');
            }
        }
        return $this->render('front/security/security.html.twig');
    }
}