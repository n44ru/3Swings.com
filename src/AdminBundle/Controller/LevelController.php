<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\Faq;
use AdminBundle\Form\FaqType;

/**
 * Banner controller.
 *
 * @Route("admin/level")
 */

class LevelController extends Controller
{
    /**
     * Lists all level.
     *
     * @Route("/", name="level_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $plan = $em->getRepository('AdminBundle:Plan')->findAll();

        return $this->render('back/level/index.html.twig', array(
            'plan' => $plan,
        ));
    }
    /**
     * edit a level.
     *
     * @Route("/edit/{id}", name="level_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $level = $em->getRepository('AdminBundle:Plan')->find($id);

        if($request->request->count()>0){
            $xp = $request->request->get('level_edit');
            $level->setXp($xp);
            $em->persist($level);
            $em->flush();
            return $this->redirectToRoute('level_index');
        }
        return $this->render('back/level/edit.html.twig', array(
            'level' => $level,
        ));
    }
}