<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LogsController extends Controller
{
    /**
     * Get the user logs, ver que no accedan desde otro id.
     *
     * @Route("/user/logs/{_locale}", name="user_logs")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userlogs p WHERE p.userid = ?1 ORDER BY p.id DESC');
        $query->setParameter(1, $userid);
        $logs = $query->getResult();
        // Update all the codes.
        $query_up = $em->createQuery('UPDATE AdminBundle:Userlogs p SET p.newlog = 0 WHERE p.userid = ?1');
        $query_up->setParameter(1, $userid);
        $query_up->getResult();

        return $this->render('front/user/logs/logs.html.twig', array(
            'userlogs' => $logs
        ));
    }

    /**
     * Deletes a log.
     * @Route("/user/logs/delete/{id}/{_locale}", name="logs_delete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        if($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        // Security check
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userlogs p WHERE p.userid = ?1 and p.id = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $id);
        $found = $query->getResult();
        if(count($found)==0){
            $this->redirectToRoute('404');
        }
        $log = $em->getRepository('AdminBundle:Userlogs')->find($id);
        $em->remove($log);
        $em->flush();
        return $this->redirectToRoute('user_logs');
    }

    /**
     * Deletes alls logs.
     * @Route("/user/logs/all/{_locale}", name="logs_delete_all")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function deleteallAction()
    {
        $em = $this->getDoctrine()->getManager();
        if($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $query = $em->createQuery('DELETE FROM AdminBundle:Userlogs p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $query->getResult();
        //
        return $this->redirectToRoute('user_logs');
    }
}