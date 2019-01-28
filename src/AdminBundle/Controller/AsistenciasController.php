<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Faq;
use AdminBundle\Form\FaqType;

/**
 * Faq controller.
 *
 * @Route("/admin/asistencias")
 */
class AsistenciasController extends Controller
{
    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="asist_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.yes > 0 or p.no > 0');
        $asist = $query->getResult();

        return $this->render('back/asistencias/index.html.twig', array(
            'asist' => $asist,
        ));
    }
}
