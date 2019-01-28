<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Settings;
use AdminBundle\Form\SettingsType;

/**
 * Settings controller.
 *
 * @Route("admin/settings")
 */
class SettingsController extends Controller
{
    /**
     * Lists all Settings entities.
     *
     * @Route("/", name="settings_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $settings = $em->getRepository('AdminBundle:Settings')->findAll();

        return $this->render('back/settings/index.html.twig', array(
            'settings' => $settings,
        ));
    }

    /**
     * Displays a form to edit an existing Settings entity.
     *
     * @Route("/{id}/edit", name="settings_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Settings $setting)
    {
        $editForm = $this->createForm('AdminBundle\Form\SettingsType', $setting);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirectToRoute('settings_index');
        }

        return $this->render('back/settings/edit.html.twig', array(
            'setting' => $setting,
            'edit_form' => $editForm->createView()
        ));
    }
}
