<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Deudas;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Deuda controller.
 *
 * @Route("admin/deudas")
 */
class DeudasController extends Controller
{
    /**
     * Lists all deuda entities.
     *
     * @Route("/", name="deudas_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $deudas = $em->getRepository('AdminBundle:Deudas')->findAll();

        return $this->render('back/deudas/index.html.twig', array(
            'deudas' => $deudas,
        ));
    }

    /**
     * Creates a new deuda entity.
     *
     * @Route("/new", name="deudas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $deuda = new Deudas();
        $form = $this->createForm('AdminBundle\Form\DeudasType', $deuda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deuda);
            $em->flush($deuda);

            return $this->redirectToRoute('deudas_index');
        }

        return $this->render('back/deudas/new.html.twig', array(
            'deuda' => $deuda,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing deuda entity.
     *
     * @Route("/{id}/edit", name="deudas_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Deudas $deuda)
    {
        $deleteForm = $this->createDeleteForm($deuda);
        $editForm = $this->createForm('AdminBundle\Form\DeudasType', $deuda);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deudas_index');
        }

        return $this->render('back/deudas/edit.html.twig', array(
            'deuda' => $deuda,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a deuda entity.
     *
     * @Route("/{id}", name="deudas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Deudas $deuda)
    {
        $form = $this->createDeleteForm($deuda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($deuda);
            $em->flush($deuda);
        }

        return $this->redirectToRoute('deudas_index');
    }

    /**
     * Creates a form to delete a deuda entity.
     *
     * @param Deudas $deuda The deuda entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Deudas $deuda)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('deudas_delete', array('id' => $deuda->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
