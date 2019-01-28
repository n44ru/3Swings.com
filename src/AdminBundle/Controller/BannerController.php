<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Banner;
use AdminBundle\Form\BannerType;

/**
 * Banner controller.
 *
 * @Route("admin/banner")
 */
class BannerController extends Controller
{
    /**
     * Lists all Banner entities.
     *
     * @Route("/", name="banner_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $banners = $em->getRepository('AdminBundle:Banner')->findAll();

        return $this->render('back/banner/index.html.twig', array(
            'banners' => $banners,
        ));
    }

    /**
     * Creates a new Banner entity.
     *
     * @Route("/new", name="banner_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $banner = new Banner();
        $form = $this->createForm('AdminBundle\Form\BannerType', $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $banner->getFile();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/banner';
            $file->move($imagesdir, $fileName);
            $banner->setRuta('uploads/banner/'.$fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($banner);
            $em->flush();

            return $this->redirectToRoute('banner_index');
        }

        return $this->render('back/banner/new.html.twig', array(
            'banner' => $banner,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Banner entity.
     *
     * @Route("/{id}/edit", name="banner_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Banner $banner)
    {
        $deleteForm = $this->createDeleteForm($banner);
        $editForm = $this->createForm('AdminBundle\Form\BannerType', $banner);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $banner->getFile();
            if($file!= null)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/banner';
                $file->move($imagesdir, $fileName);

                $banner->setRuta('uploads/banner/'.$fileName);
            }
            $em->persist($banner);
            $em->flush();

            return $this->redirectToRoute('banner_index');
        }

        return $this->render('back/banner/edit.html.twig', array(
            'banner' => $banner,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Banner entity.
     *
     * @Route("/{id}", name="banner_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Banner $banner)
    {
        $form = $this->createDeleteForm($banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($banner);
            $em->flush();
        }

        return $this->redirectToRoute('banner_index');
    }

    /**
     * Creates a form to delete a Banner entity.
     *
     * @param Banner $banner The Banner entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Banner $banner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('banner_delete', array('id' => $banner->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
