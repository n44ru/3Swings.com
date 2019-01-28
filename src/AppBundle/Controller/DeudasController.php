<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DeudasController extends Controller
{
    /**
     * Get the user deudas, ver que no accedan desde otro id.
     *
     * @Route("/user/pending/pays/{_locale}", name="user_deudas")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $query = $em->createQuery('SELECT p FROM AdminBundle:Deudas p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $deudas = $query->getResult();

        return $this->render('front/user/deudas/deudas.html.twig', array(
            'lasdeudas' => $deudas[0]
        ));
    }
}