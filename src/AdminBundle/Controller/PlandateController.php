<?php

namespace AdminBundle\Controller;


use AdminBundle\Entity\Userlogs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Plan Date controller.
 *
 * @Route("admin/plan/dates")
 */

class PlandateController extends Controller
{
    /**
     *
     * @Route("/", name="plandate_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.level > 0');
        $data = $query->getResult();
        $query2 = $em->createQuery('SELECT p FROM AdminBundle:Plan p ORDER BY p.level ASC');
        $plans = $query2->getResult();
        $today = date('ymd');
        if($request->request->count()>1){
            if($request->request->get('titulo_mail')){
                $titulo = $request->request->get('titulo_mail');
                $mensaje = $request->request->get('texto_mail');
                $to = $request->request->get('user_mail');
                //
                $email_admin = 'info3swings@gmail.com';
                //
                $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $cabeceras .= 'From:'.$email_admin. "\r\n";
                //
                mail($to,$titulo,$mensaje,$cabeceras);
                return $this->redirectToRoute('plandate_index');
            }
            // Degradar un Usuario.
            if($request->request->get('select_level')){
                $level = $request->request->get('select_level');
                $userid = $request->request->get('userid');
                $the_plan = $em->getRepository('AdminBundle:Plan')->find($level);
                //
                $mensaje = "You dont renew your 1 year membership, the site automatically degrade your user to the level: ".$the_plan->getLevel().", Plan: ".$the_plan->getName().". Contact the Admin or buy your membership again.";

                if($request->request->get('send_email')){
                    $titulo = "3Swings.com: Your User loose some privileges.";
                    $to = $request->request->get('user_mail');
                    //
                    $email_admin = 'info3swings@gmail.com';
                    //
                    $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                    $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $cabeceras .= 'From:'.$email_admin. "\r\n";
                    //
                    mail($to,$titulo,$mensaje,$cabeceras);
                }
                //
                if($request->request->get('send_log')){
                    $user = $em->getRepository('AdminBundle:User')->find($userid);
                    $today = date("d/m/y");
                    $userlogs = new Userlogs();
                    $userlogs->setLog($mensaje);
                    $userlogs->setUserid($user);
                    $userlogs->setFecha($today);
                    $userlogs->setNewlog(1);
                    $em->persist($userlogs);
                    $em->flush();
                }
                //
                $query3 = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
                $query3->setParameter(1, $userid);
                $userdata = $query3->getResult();
                //
                $userdata[0]->setPlanid($the_plan);
                $userdata[0]->setLevel($the_plan->getLevel());
                $userdata[0]->setStats1($the_plan->getXp());
                $userdata[0]->setPlandate($today);
                $em->persist($userdata[0]);
                $em->flush();
                //
                return $this->redirectToRoute('plandate_index');
            }
        }
        //
        return $this->render('back/plandate/index.html.twig', array(
            'data' => $data,
            'today'=> $today,
            'plans'=> $plans
        ));
    }
    /**
     *
     * @Route("/disable/{userid}", name="disable_index")
     * @Method("GET")
     */
    public function disableAction(Request $request, $userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AdminBundle:User')->find($userid);
        $user->setActive(0);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('plandate_index');
    }
}