<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Recomended;
use AdminBundle\Form\RecomendedType;

/**
 * Recomended controller.
 *
 * @Route("admin/recomended")
 */
class RecomendedController extends Controller
{
    /**
     * Lists all Recomended entities.
     *
     * @Route("/", name="recomended_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $recomendeds = $em->getRepository('AdminBundle:Recomended')->findAll();

        return $this->render('back/recomended/index.html.twig', array(
            'recomendeds' => $recomendeds,
        ));
    }

    /**
     * Creates a new Recomended entity.
     *
     * @Route("/new", name="recomended_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $recomended = new Recomended();
        $form = $this->createForm('AdminBundle\Form\RecomendedType', $recomended);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recomended);
            $em->flush();

            return $this->redirectToRoute('recomended_index');
        }

        return $this->render('back/recomended/new.html.twig', array(
            'recomended' => $recomended,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Recomended entity.
     *
     * @Route("/{id}/edit", name="recomended_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Recomended $recomended)
    {
        $deleteForm = $this->createDeleteForm($recomended);
        $editForm = $this->createForm('AdminBundle\Form\RecomendedType', $recomended);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recomended);
            $em->flush();

            return $this->redirectToRoute('recomended_index');
        }

        return $this->render('back/recomended/edit.html.twig', array(
            'recomended' => $recomended,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Recomended entity.
     *
     * @Route("/{id}", name="recomended_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Recomended $recomended)
    {
        $form = $this->createDeleteForm($recomended);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recomended);
            $em->flush();
        }

        return $this->redirectToRoute('recomended_index');
    }

    /**
     * Creates a form to delete a Recomended entity.
     *
     * @param Recomended $recomended The Recomended entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recomended $recomended)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recomended_delete', array('id' => $recomended->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
