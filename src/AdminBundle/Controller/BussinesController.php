<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Photos;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Bussines;
use AdminBundle\Form\BussinesType;

/**
 * Bussines controller.
 *
 * @Route("/admin/bussines")
 */
class BussinesController extends Controller
{
    /**
     * Lists all Bussines entities.
     *
     * @Route("/", name="bussines_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bussines = $em->getRepository('AdminBundle:Bussines')->findAll();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();
        return $this->render('back/bussines/index.html.twig', array(
            'bussines' => $bussines,
            'photos' => $photos
        ));
    }

    /**
     * Creates a new Bussines entity.
     *
     * @Route("/new", name="bussines_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bussine = new Bussines();
        $form = $this->createForm('AdminBundle\Form\BussinesType', $bussine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $bussine->getFile();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/images';
            $file->move($imagesdir, $fileName);
            $photos = new Photos();
            $photos->setPath('uploads/images/'.$fileName);
            $photos->setBussinesid($bussine);

            $em = $this->getDoctrine()->getManager();
            // Website fix.
            $website = $bussine->getWebsite();
            if(!strchr($website,'http')){
                $website='http://'.$website;
            }
            $bussine->setWebsite($website);
            $bussine->setRating(0);
            // clean estates & country
            $estate = $bussine->getEstate();
            $estate= strtolower(trim($estate));
            $country = $bussine->getCountry();
            $country= strtolower(trim($country));
            //
            $bussine->setCountry($country);
            $bussine->setEstate($estate);
            // set yes en 0
            $bussine->setNo(0);
            //
            $em->persist($bussine);
            $em->persist($photos);
            $em->flush();

            return $this->redirectToRoute('bussines_index');
        }

        return $this->render('back/bussines/new.html.twig', array(
            'bussine' => $bussine,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing Bussines entity.
     *
     * @Route("/{id}/edit", name="bussines_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bussines $bussine)
    {
        $deleteForm = $this->createDeleteForm($bussine);
        $editForm = $this->createForm('AdminBundle\Form\BussinesType', $bussine);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var UploadedFile $file */
            $file = $bussine->getFile();
            if($file!= null)
            {
                $id = $bussine->getId();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/images';
                $file->move($imagesdir, $fileName);

                $query = $em->createQuery('SELECT p FROM AdminBundle:Photos p WHERE p.bussinesid = ?1');
                $query->setParameter(1, $id);
                $photos = $query->getResult();

                $photos[0]->setPath('uploads/images/'.$fileName);
                $photos[0]->setBussinesid($bussine);
                $em->persist($photos[0]);
            }
            // Website fix.
            $website = $bussine->getWebsite();
            if(!strchr($website,'http')){
                $website='http://'.$website;
            }
            $bussine->setWebsite($website);
            // clean estates & country
            $estate = $bussine->getEstate();
            $estate= strtolower(trim($estate));
            $country = $bussine->getCountry();
            $country= strtolower(trim($country));
            //
            $bussine->setCountry($country);
            $bussine->setEstate($estate);
            //
            $em->persist($bussine);
            $em->flush();

            return $this->redirectToRoute('bussines_index');

        }

        return $this->render('back/bussines/edit.html.twig', array(
            'bussine' => $bussine,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Bussines entity.
     *
     * @Route("/{id}", name="bussines_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bussines $bussine)
    {
        $form = $this->createDeleteForm($bussine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bussine);
            $em->flush();
        }

        return $this->redirectToRoute('bussines_index');
    }

    /**
     * Creates a form to delete a Bussines entity.
     *
     * @param Bussines $bussine The Bussines entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bussines $bussine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bussines_delete', array('id' => $bussine->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
