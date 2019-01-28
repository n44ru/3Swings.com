<?php

namespace AdminBundle\Controller\Extractions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Admin controller.
 *
 * @Route("/admin")
 */

class ExtractionsController extends Controller
{
    /**
     *
     * @Route("/extractions", name="extractions")
     */
    public function verAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ext = $em->getRepository('AdminBundle:Userextractions')->findAll();

        return $this->render('back/extractions/index.html.twig', array(
            'ext' => $ext
        ));
    }
    /**
     * Deletes a Userextraction entity.
     *
     * @Route("/extractions/delete/{id}", name="extractions_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $ex = $em->getRepository('AdminBundle:Userextractions')->find($id);
        $em->remove($ex);
        $em->flush();
        return $this->redirectToRoute('extractions');
    }

    /**
     * Deletes alls Userextraction records
     *
     * @Route("/extractions/all/delete/", name="all_delete")
     * @Method("GET")
     */
    public function deleteallAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('DELETE FROM AdminBundle:Userextractions p WHERE p.id > 0');
        $query->getResult();
        return $this->redirectToRoute('extractions');
    }

}