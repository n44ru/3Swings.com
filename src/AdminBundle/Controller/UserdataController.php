<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Userdata;
use AdminBundle\Form\UserdataType;

/**
 * Userdata controller.
 *
 * @Route("admin/userdata")
 */
class UserdataController extends Controller
{
    /**
     * Lists all Userdata entities.
     *
     * @Route("/", name="userdata_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userdatas = $em->getRepository('AdminBundle:Userdata')->findAll();

        return $this->render('back/userdata/index.html.twig', array(
            'userdatas' => $userdatas,
        ));
    }

    /**
     * Creates a new Userdata entity.
     *
     * @Route("/new", name="userdata_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $userdatum = new Userdata();
        $form = $this->createForm('AdminBundle\Form\UserdataType', $userdatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userdatum);
            $em->flush();

            return $this->redirectToRoute('userdata_index');
        }

        return $this->render('back/userdata/new.html.twig', array(
            'userdatum' => $userdatum,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Userdata entity.
     *
     * @Route("/{id}/edit", name="userdata_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Userdata $userdatum)
    {
        $deleteForm = $this->createDeleteForm($userdatum);
        $editForm = $this->createForm('AdminBundle\Form\UserdataType', $userdatum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userdatum);
            $em->flush();

            return $this->redirectToRoute('userdata_index');
        }

        return $this->render('back/userdata/edit.html.twig', array(
            'userdatum' => $userdatum,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Userdata entity.
     *
     * @Route("/{id}", name="userdata_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Userdata $userdatum)
    {
        $form = $this->createDeleteForm($userdatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userdatum);
            $em->flush();
        }

        return $this->redirectToRoute('userdata_index');
    }

    /**
     * Creates a form to delete a Userdata entity.
     *
     * @param Userdata $userdatum The Userdata entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Userdata $userdatum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('userdata_delete', array('id' => $userdatum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
