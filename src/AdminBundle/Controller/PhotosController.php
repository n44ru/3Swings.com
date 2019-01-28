<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Photos;
use AdminBundle\Form\PhotosType;

/**
 * Photos controller.
 *
 * @Route("admin/photos")
 */
class PhotosController extends Controller
{
    /**
     * Lists all Photos entities.
     *
     * @Route("/", name="photos_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $photos = $em->getRepository('AdminBundle:Photos')->findAll();

        return $this->render('back/photos/index.html.twig', array(
            'photos' => $photos,
        ));
    }

    /**
     * Deletes a Photos entity.
     *
     * @Route("/{id}", name="photos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Photos $photo)
    {
        $form = $this->createDeleteForm($photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();
        }

        return $this->redirectToRoute('photos_index');
    }

    /**
     * Creates a form to delete a Photos entity.
     *
     * @param Photos $photo The Photos entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Photos $photo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('photos_delete', array('id' => $photo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
