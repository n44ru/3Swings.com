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
 * @Route("/admin/faq")
 */
class FaqController extends Controller
{
    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="faq_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $faqs = $em->getRepository('AdminBundle:Faq')->findAll();

        return $this->render('back/faq/index.html.twig', array(
            'faqs' => $faqs,
        ));
    }

    /**
     * Creates a new Faq entity.
     *
     * @Route("/new", name="faq_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $faq = new Faq();
        $form = $this->createForm('AdminBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->redirectToRoute('faq_index');
        }

        return $this->render('back/faq/new.html.twig', array(
            'faq' => $faq,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     * @Route("/{id}/edit", name="faq_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Faq $faq)
    {
        $deleteForm = $this->createDeleteForm($faq);
        $editForm = $this->createForm('AdminBundle\Form\FaqType', $faq);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->redirectToRoute('faq_index');
        }

        return $this->render('back/faq/edit.html.twig', array(
            'faq' => $faq,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Faq entity.
     *
     * @Route("/{id}", name="faq_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Faq $faq)
    {
        $form = $this->createDeleteForm($faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();
        }

        return $this->redirectToRoute('faq_index');
    }

    /**
     * Creates a form to delete a Faq entity.
     *
     * @param Faq $faq The Faq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Faq $faq)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('faq_delete', array('id' => $faq->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
