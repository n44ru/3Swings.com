<?php
/**
 * Created by PhpStorm.
 * User: Carlos
 * Date: 28/10/2017
 * Time: 12:09 PM
 */

namespace AppBundle\Controller;

use AdminBundle\Entity\Deudas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RequestController extends Controller
{
    /**
     * Users Requests
     *
     * @Route("/user/request/view/{_locale}", name="request_view")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function viewAction()
    {
        if ($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $users = $this->get('app.request')->myrequests($id);

        return $this->render('front/user/request/request.html.twig', array('todos' => $users));

    }

    /**
     * Accept Requests
     *
     * @Route("/user/request/accept/{id}/{_locale}", name="request_ac")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function acAction($id)
    {
        if ($this->getUser())
            $boss = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $em = $this->getDoctrine()->getManager();
        $linked = $em->getRepository('AdminBundle:LinkedUsers')->find($id);
        // si no es el dueño.
        if ($boss != $linked->getBussinesid()->getUserid()->getId()) {
            return $this->redirectToRoute('404');
        }
        //
        $userid = $linked->getUserid()->getId();
        //
        $serv = $linked->getBussinesid();
        // Send the statistic 3 to the User
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        $stats3 = $userdata[0]->getStats3();
        $userdata[0]->setStats3($stats3 + 1);
        // Ponerle asistencia al servicio.
        $asist = $userdata[0]->getYes();
        $userdata[0]->setYes($asist+1);
        //
        $em->persist($userdata[0]);
        $em->flush();
        // function to send money to fathers of the link users.
        $this->Tree($userdata, $serv, $em);
        // Eliminar pues todo esta ok.
        $em->remove($linked);
        $em->flush();
        //Cuando mandas el $1 al referee.
        $log2 = 'The Referee of ' . $linked->getUserid()->getUsername() . ' gets $' . $serv->getPago() . ' by Services Politics of 3Swings.';
        $this->get('app.setLogs')->setuserlogs($log2, $serv->getUserid()->getId());
        //NEW Eliminar para que pueda volver a reservar.
        //$this->deleteAction($id);
        return $this->redirectToRoute('request_view');

    }

    /**
     * Delete Requests
     *
     * @Route("/user/request/delete/{id}/{_locale}", name="request_delete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function deleteAction($id)
    {
        if ($this->getUser())
            $boss = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $em = $this->getDoctrine()->getManager();
        $linked = $em->getRepository('AdminBundle:LinkedUsers')->find($id);
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $linked->getUserid());
        $userdata = $query->getResult();
        // Quitarle asistencia al servicio.
        $asist = $userdata[0]->getNo();
        $userdata[0]->setNo($asist+1);
        //
        $em->persist($userdata[0]);
        $em->flush();
        // si no es el dueño.
        if ($boss != $linked->getBussinesid()->getUserid()->getId()) {
            return $this->redirectToRoute('404');
        }
        // Quitarle el rating
        $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.userid = ?1');
        $query->setParameter(1, $boss);
        $negocio = $query->getResult();
        //
        $no = $negocio[0]->getNo();
        $rating= $negocio[0]->getRating();
        if($no < 5){
            $negocio[0]->setNo($no+1);
        }
        else{
            if($rating>1){
                $negocio[0]->setRating($rating-1);
            }
            $negocio[0]->setNo(0);
        }
        $em->persist($negocio[0]);
        $em->remove($linked);
        $em->flush();
        //
        return $this->redirectToRoute('request_view');

    }

    public function Tree($userdata, $serv, $em)
    {
        $father = $userdata[0]->getDad();

        $owner = $serv->getUserid();
        $pago = $serv->getPago();
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $owner);
        $owner_data = $query->getResult();
        //
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $father);
        $father_data = $query->getResult();
        $current = $father_data[0]->getMoney();
        $father_data[0]->setMoney($current + $pago);
        $em->persist($father_data[0]);
        $em->flush();
        // faltan los logs
        $saldo = $owner_data[0]->getMoney();
        if ($saldo >= $pago) {
            $owner_data[0]->setMoney($saldo - $pago);
            //
            $em->persist($owner_data[0]);
            $em->flush();
        } else {
            $query = $em->createQuery('SELECT p FROM AdminBundle:Deudas p WHERE p.userid = ?1');
            $query->setParameter(1, $owner);
            $tiene = $query->getResult();
            //
            if ($tiene == null) {
                $deuda = new Deudas();
                $deuda->setDate(date("d/m/y"));
                $deuda->setCantidad($pago);
                $deuda->setUserid($owner);
                $em->persist($deuda);
                $em->flush();
                //
            } else {
                $current = $tiene[0]->getCantidad();
                $new = $current + $pago;
                $tiene[0]->setCantidad($new);
                $em->persist($tiene[0]);
                $em->flush();
                //
                $logs = 'You have a pending amount of $' . $new . ' with 3Swings, please recharge your user money or may be get suspended.';
                $this->get('app.setLogs')->setuserlogs($logs, $owner->getId());
                // Send the email.
                //mail($serv->getUserid()->getUsername(), '3Swings.com Pending Pays', $logs);
                //
                $this->get('app.mailsender')->send_mail($serv->getUserid()->getUsername(),'3Swings.com Pending Pays',$logs,'noreply@3swings.com');
                //

            }
        }
    }
}