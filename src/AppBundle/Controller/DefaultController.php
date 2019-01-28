<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Codes;
use AdminBundle\Entity\Comments;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DefaultController extends Controller
{
    /**
     * Home Page
     * @Route("/{_locale}", name="homepage")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */

    public function indexAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        // Check if the user is correct.
        $user = $this->getUser();
        if ($user != null) {
            if ($user->getActive() == 0) {
                return $this->redirectToRoute('logout');
            }
            else if($user->getActive() == 2){
                return $this->redirectToRoute('security_check');
            }
        }
        //Level up Method.
        if($this->getUser()){
            $user_object = $this->getUser();
            $user_id = $user_object->getId();
            // Get Userdata
            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
            $query->setParameter(1, $user_id);
            $userdata = $query->getResult();
            $userdata = $userdata[0];
            //
            $query = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.xp <= ?1 ');
            $query->setParameter(1, $userdata->getStats1());
            $found = $query->getResult();
            for($i=0;$i<count($found);$i++){
                if($found[$i]->getLevel()>$userdata->getLevel()){
                    //Level up.
                    $userdata->setLevel($found[$i]->getLevel());
                    $userdata->setPlanid($found[$i]);
                    $em->persist($userdata);
                    $em->flush();
                    // Send the log.
                    $logs = 'You Level Up!!! Now you have access to the '.$found[$i]->getName().' Plan.';
                    $this->get('app.setLogs')->setuserlogs($logs, $user_id);
                }
            }
            // Ver si tiene Deudas y Restarle el $$$
            $query = $em->createQuery('SELECT p FROM AdminBundle:Deudas p WHERE p.userid = ?1 ');
            $query->setParameter(1, $user_id);
            $deuda = $query->getResult();
            if($deuda!=null){
                if($userdata->getMoney()>=$deuda[0]->getCantidad()){
                    $current = $userdata->getMoney();
                    $userdata->setMoney($current-$deuda[0]->getCantidad());
                    $em->persist($userdata);
                    $em->remove($deuda[0]);
                    $em->flush();
                    // Send the log
                    $logs = 'We discount your pending money from your account money.';
                    $this->get('app.setLogs')->setuserlogs($logs, $user_id);
                }
            }
        }
        //Clean temporal users.
        $this->get('app.clean')->users();

        //Obtener todos los servicios recomendados
        $query = $em->createQuery('SELECT p FROM AdminBundle:Recomended p ORDER BY p.weigth ASC ');
        $recom = $query->getResult();
        //
        //Get the banner
        $banner = $em->getRepository('AdminBundle:Banner')->findAll();
        // get services and photos
        $bussines = $em->getRepository('AdminBundle:Bussines')->findAll();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();
        // send some data for statistics.
        $users = $em->getRepository('AdminBundle:User')->findAll();
        return $this->render('front/home.html.twig', array(
            'bussines' => $bussines,
            'photos' => $photos,
            'users' => $users,
            'banner' => $banner,
            'recom' => $recom,
            '_locale' => $_locale
        ));
    }

    /**
     * All business
     *
     * @Route("/business/{_locale}", name="business")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function allAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $bussines = $em->getRepository('AdminBundle:Bussines')->findAll();
        $cat = $em->getRepository('AdminBundle:Category')->findAll();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();
        // recomendados
        $recom = $em->getRepository('AdminBundle:Recomended')->findAll();

        $user = $this->getUser();
        $near = null;
        if ($user != null) {
            $country = $user->getCountry();
            $estate = $user->getEstate();
            // Get the nearest bussiness if a user is logged in.
            if ($this->getUser())
                $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.country LIKE ?1 and p.estate LIKE ?2');
            $query->setParameter(1, '%' . $country . '%');
            $query->setParameter(2, '%' . $estate . '%');
            $near = $query->getResult();
        }
        // Ver las que mas rating tienen.
        $query_star = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.rating > 0 ORDER BY p.rating DESC');
        $most = $query_star->getResult();
        // Ver todos los estados.
        $query_estates = $em->createQuery('SELECT DISTINCT(p.estate) FROM AdminBundle:Bussines p');
        $estates_all = $query_estates->getResult();
        // Ver todos los paises.
        $query_c = $em->createQuery('SELECT DISTINCT(p.country) FROM AdminBundle:Bussines p');
        $country_all = $query_c->getResult();
        return $this->render('front/business.html.twig', array(
            'business' => $bussines,
            'photos' => $photos,
            'near' => $near,
            'categories' => $cat,
            'most' => $most,
            'recom' => $recom,
            '_locale'=>$_locale,
            'estates'=>$estates_all,
            'country'=>$country_all
        ));
    }

    /**
     * Business Details
     *
     * @Route("/services/details/{id}/{_locale}", name="business_details")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function detailsAction(Request $request, $id, $_locale)
    {
        // Lang Fix.
        $this->get('twig')->addGlobal('route_id', $id);

        $em = $this->getDoctrine()->getManager();
        // Get all plans.
        $queryex = $em->createQuery('SELECT p FROM AdminBundle:Plan p ORDER BY p.descuento ASC');
        $plans = $queryex->getResult();
        //
        $bussines = $em->getRepository('AdminBundle:Bussines')->find($id);
        //
        $query0 = $em->createQuery('SELECT p FROM AdminBundle:LinkedUsers p WHERE p.bussinesid = ?1 and p.linked=1');
        $query0->setParameter(1, $id);
        $linked = $query0->getResult();
        // Select the Userdata if user exist
        $userdata=null;
        if($this->getUser())
        {
            $query0 = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1 ');
            $query0->setParameter(1, $this->getUser()->getId());
            $userdata = $query0->getResult();
        }
        //
        $query1 = $em->createQuery('SELECT p FROM AdminBundle:Comments p WHERE p.active = 1 and p.bussinesid = ?1');
        $query1->setParameter(1, $id);
        $comments = $query1->getResult();

        $avatars = $em->getRepository('AdminBundle:Photos')->findAll();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Photos p WHERE p.bussinesid = ?1 ');
        $query->setParameter(1, $id);
        $photos = $query->getResult();
         //Check if the user is linked.
        $check = 0;
        if ($this->getUser() != null) {
            $userid = $this->getUser()->getId();
            $query = $em->createQuery('SELECT p FROM AdminBundle:LinkedUsers p WHERE p.userid = ?1 AND p.bussinesid = ?2');
            $query->setParameter(1, $userid);
            $query->setParameter(2, $id);
            $result = $query->getResult();
            if(count($result)>0){
                $check=$result[0]->getLinked();
            }
        }
        if ($request->request->count() > 1) {
            if ($request->request->get('input-1')) {
                // Ver si ya hizo una valoracion en el negocio.
                $query = $em->createQuery('SELECT p FROM AdminBundle:Comments p WHERE p.bussinesid = ?1 and p.userid = ?2');
                $query->setParameter(1, $id);
                $query->setParameter(2, $this->getUser()->getId());
                $comm = $query->getResult();
                //
                if (count($comm) == 0) {
                    $stars = $request->request->get('input-1');
                    $message = $request->request->get('input-2');
                    $com = new Comments();
                    $com->setStars($stars);
                    $com->setMessage($message);
                    $com->setActive(1);
                    $com->setBussinesid($bussines);
                    $com->setUserid($this->getUser());
                    $em->persist($com);
                    $em->flush();
                    // Save the rating in the rating row.
                    if($bussines->getRating() != 0){
                        $old = $bussines->getRating();
                        $new = ($old + $stars)/2;
                        $bussines->setRating($new);
                    }
                    else{
                        $bussines->setRating($stars);
                    }
                    $em->persist($bussines);
                    $em->flush();
                    // Send the log to the admin.
                    $admin_id = $this->get('app.sendMoney')->getadminid();
                    $logs = 'El usuario ' . $this->getUser()->getId() . ' ha echo un comentario en el Negocio: ' . $bussines->getName();
                    $this->get('app.setLogs')->setuserlogs($logs, $admin_id);
                    return $this->redirectToRoute('business_details', array('id' => $bussines->getId()));
                } else {
                    return $this->render('front/error/comment.html.twig', array(
                        'serv_id' => $id));
                }
            }
            if($request->request->get('title-det')){
                $title = $request->request->get('title-det');
                $email = $request->request->get('email');
                $message = $request->request->get('message');
                //
                if($title==null || $email==null || $message==null){
                    return $this->redirectToRoute('404');
                }
                //mail($bussines->getUserid()->getUsername(),$title,$message,"From:".$email);
                // Servicio de Mail.
                $this->get('app.mailsender')->send_mail($bussines->getUserid()->getUsername(),$title,$message,$email);
                //
                $this->redirectToRoute('business_details',array('id'=>$bussines->getId()));
            }
        }
        return $this->render('front/business_details.html.twig', array(
            'business' => $bussines,
            'check'=> $check,
            'details_photos' => $photos,
            'comm' => $comments,
            'avatars' => $avatars,
            'linked' => $linked,
            'userdata'=> $userdata[0],
            'plans' => $plans,
            '_locale'=>$_locale,
        ));
    }

    /**
     * Contact us
     *
     * @Route("/contact/{_locale}", name="contact")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function contactAction(Request $request)
    {
        if($request->request->count()>1){
            $title = $request->request->get('title');
            $email = $request->request->get('email');
            $message = $request->request->get('message');
            if($request->request->get('g-recaptcha-response'))
            {
                $response = $request->request->get('g-recaptcha-response');
                //your site secret key
                $secret = '6Lc2eCwUAAAAADeq2DzBG_39u757O0WYAFIEx72x';
                //get verify response data
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response);
                $responseData = json_decode($verifyResponse);
                if(!$responseData->success){
                    return $this->redirectToRoute('404');
                }
            }
            else{
                return $this->redirectToRoute('404');
            }
            if($title==null || $email==null || $message==null){
                return $this->redirectToRoute('404');
            }
            //mail("info3swings@gmail.com",$title,$message,"From:".$email);
            //
            $this->get('app.mailsender')->send_mail("info3swings@gmail.com",$title,$message,$email);
            //
            return $this->redirectToRoute('homepage');

        }
        return $this->render('front/contact/contact.html.twig');
    }

    /**
     * FAQ
     *
     * @Route("/faq/{_locale}", name="faqs")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function faqAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $faq = $em->getRepository('AdminBundle:Faq')->findAll();
        $topic = $em->getRepository('AdminBundle:Topic')->findAll();
        return $this->render('front/faq.html.twig', array(
            'faq' => $faq,
            'topic' => $topic,
            'locale' => $_locale
        ));
    }

    /**
     * FAQ Dynamic
     *
     * @Route("/faq/dynamic/{_locale}", name="faq_dynamic")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function dynAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $faq= $em->getRepository('AdminBundle:Faq')->find($id);
        return $this->render('front/dynamic/faq.html.twig', array(
            'respuesta' => $faq,
            'locale' => $_locale
        ));
    }

    /**
     * 404
     *
     * @Route("/notfound/{_locale}", name="404")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function notfoundAction()
    {
        return $this->render('front/404/404.html.twig');
    }
}