<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Deudas;
use AdminBundle\Entity\LinkedUsers;
use AdminBundle\Entity\Photos;
use AdminBundle\Entity\Bussines;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class BusinessController extends Controller
{
    /**
     * Business Dynamic Search
     *
     * @Route("/search/dynamic/{_locale}", name="search_dynamic")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function searchAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $desc = $request->request->get('desc');
        $min = $request->request->get('min');
        $max = $request->request->get('max');
        $country = $request->request->get('country');
        $state = $request->request->get('state');
        $code = $request->request->get('code');
        $cat_id = $request->request->get('cat');
        //
        $business = null;
        if ($_locale == 'es') {
            if ($cat_id == 0) {
                $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.name LIKE ?1 and p.description LIKE ?2 and p.price >= ?3 and p.price <= ?4 and p.country LIKE ?5 and p.estate LIKE ?6 and p.postalcode LIKE ?7 and p.categoryid > ?8 ORDER  BY p.rating DESC');
                $query->setParameter(1, '%' . $name . '%');
                $query->setParameter(2, '%' . $desc . '%');
                $query->setParameter(3, $min);
                $query->setParameter(4, $max);
                $query->setParameter(5, '%' . $country . '%');
                $query->setParameter(6, '%' . $state . '%');
                $query->setParameter(7, '%' . $code . '%');
                $query->setParameter(8, $cat_id);
                $business = $query->getResult();
            } else if ($cat_id > 0) {
                $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.name LIKE ?1 and p.description LIKE ?2 and p.price >= ?3 and p.price <= ?4 and p.country LIKE ?5 and p.estate LIKE ?6 and p.postalcode LIKE ?7 and p.categoryid = ?8 ORDER  BY p.rating DESC');
                $query->setParameter(1, '%' . $name . '%');
                $query->setParameter(2, '%' . $desc . '%');
                $query->setParameter(3, $min);
                $query->setParameter(4, $max);
                $query->setParameter(5, '%' . $country . '%');
                $query->setParameter(6, '%' . $state . '%');
                $query->setParameter(7, '%' . $code . '%');
                $query->setParameter(8, $cat_id);
                $business = $query->getResult();
            }
        } else {
            if ($cat_id == 0) {
                $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.name LIKE ?1 and p.descriptionen LIKE ?2 and p.price >= ?3 and p.price <= ?4 and p.country LIKE ?5 and p.estate LIKE ?6 and p.postalcode LIKE ?7 and p.categoryid > ?8 ORDER  BY p.rating DESC');
                $query->setParameter(1, '%' . $name . '%');
                $query->setParameter(2, '%' . $desc . '%');
                $query->setParameter(3, $min);
                $query->setParameter(4, $max);
                $query->setParameter(5, '%' . $country . '%');
                $query->setParameter(6, '%' . $state . '%');
                $query->setParameter(7, '%' . $code . '%');
                $query->setParameter(8, $cat_id);
                $business = $query->getResult();
            } else if ($cat_id > 0) {
                $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.name LIKE ?1 and p.descriptionen LIKE ?2 and p.price >= ?3 and p.price <= ?4 and p.country LIKE ?5 and p.estate LIKE ?6 and p.postalcode LIKE ?7 and p.categoryid = ?8 ORDER  BY p.rating DESC');
                $query->setParameter(1, '%' . $name . '%');
                $query->setParameter(2, '%' . $desc . '%');
                $query->setParameter(3, $min);
                $query->setParameter(4, $max);
                $query->setParameter(5, '%' . $country . '%');
                $query->setParameter(6, '%' . $state . '%');
                $query->setParameter(7, '%' . $code . '%');
                $query->setParameter(8, $cat_id);
                $business = $query->getResult();
            }
        }


        $photos = $em->getRepository('AdminBundle:Photos')->findAll();
        //
        return $this->render('front/dynamic/search.html.twig', array(
            'business' => $business,
            'photos' => $photos
        ));
    }

    /**
     * User business
     *
     * @Route("/user/services/{_locale}", name="user_serv")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function servicesAction(Request $request)
    {
        if ($this->getUser())
            $id = $this->getUser()->getId();
        else return $this->redirectToRoute('404');

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.userid = ?1');
        $query->setParameter(1, $id);
        $userserv = $query->getResult();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();
        // User Linked Services
        $query = $em->createQuery('SELECT p FROM AdminBundle:LinkedUsers p WHERE p.userid = ?1 and p.linked=1');
        $query->setParameter(1, $id);
        $linked = $query->getResult();
        //
        return $this->render('front/user/user_business.html.twig', array(
            'services' => $userserv,
            'photos' => $photos,
            'linked' => $linked
        ));
    }

    /**
     * Creates a new User Service.
     *
     * @Route("/user/services/new/{_locale}", name="servicio_nuevo")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $_locale)
    {
        //Super Fix.
        $bussine = new Bussines();
        $form = $this->createForm('AppBundle\Form\BussinesType', $bussine);
        $form->handleRequest($request);

        // All Categories
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('AdminBundle:Category')->findAll();

        if ($form->isSubmitted() && $form->isValid() and $this->getUser()) {
            //Category post
            $category_post = $request->request->get('category_post');
            if ($category_post == null) {
                return $this->render('front/error/empty.html.twig');
            }
            $category = $em->getRepository('AdminBundle:Category')->find($category_post);
            /** @var UploadedFile $file */
            $file = $bussine->getFile();
            if ($file != null) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/images';
                $file->move($imagesdir, $fileName);
                $photos = new Photos();
                $photos->setPath('uploads/images/' . $fileName);
                $photos->setBussinesid($bussine);
            }
            // Website fix.
            $website = $bussine->getWebsite();
            if (!strchr($website, 'http')) {
                $website = 'http://' . $website;
            }
            // Facebook fix.
            $fb = $bussine->getFacebook();
            if (!strchr($fb, 'http')) {
                $fb = 'http://' . $fb;
            }
            // Instagram fix.
            $ins = $bussine->getInstagram();
            if (!strchr($ins, 'http')) {
                $ins = 'http://' . $ins;
            }
            // Get the current user.
            $id = $this->getUser()->getId();
            $owner = $em->getRepository('AdminBundle:User')->find($id);
            $bussine->setWebsite($website);
            $bussine->setFacebook($fb);
            $bussine->setInstagram($ins);
            $bussine->setUserid($owner);
            $bussine->setCategoryid($category);
            // clean estates & country
            $estate = $bussine->getEstate();
            $estate = strtolower(trim($estate));
            $country = $bussine->getCountry();
            $country = strtolower(trim($country));
            //
            $bussine->setCountry($country);
            $bussine->setEstate($estate);
            $bussine->setRating(0);
            //
            $em->persist($bussine);
            if ($file != null) {
                $em->persist($photos);
            }
            $em->flush();

            // Send the statistic 2 to the User
            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
            $query->setParameter(1, $id);
            $userdata = $query->getResult();
            $stats2 = $userdata[0]->getStats2();
            $userdata[0]->setStats2($stats2 + 1);
            $em->persist($userdata[0]);
            $em->flush();

            return $this->redirectToRoute('user_serv');
        }

        return $this->render('front/user/new.html.twig', array(
            'form' => $form->createView(),
            'cat' => $cat,
            '_locale' => $_locale
        ));
    }

    /**
     * Displays a form to edit an existing Bussines entity.
     *
     * @Route("/user/services/edit/{id}/{_locale}", name="serv_edit")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bussines $bussine, $id, $_locale)
    {
        if ($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        // Lang Fix.
        $this->get('twig')->addGlobal('route_id', $id);

        $deleteForm = $this->createDeleteForm($bussine);
        $editForm = $this->createForm('AppBundle\Form\BussinesEditType', $bussine);
        $editForm->handleRequest($request);

        // All Categories
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('AdminBundle:Category')->findAll();
        // Service
        $serv = $em->getRepository('AdminBundle:Bussines')->find($id);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $bussine->getFile();
            if ($file != null) {
                $id = $bussine->getId();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir') . '/../web/uploads/images';
                $file->move($imagesdir, $fileName);

                $query = $em->createQuery('SELECT p FROM AdminBundle:Photos p WHERE p.bussinesid = ?1');
                $query->setParameter(1, $id);
                $photos = $query->getResult();

                $photos[0]->setPath('uploads/images/' . $fileName);
                $photos[0]->setBussinesid($bussine);
                $em->persist($photos[0]);
            }
            //Category post
            $category_post = $request->request->get('category_post');
            if ($category_post == null) {
                return $this->render('front/error/empty.html.twig');
            }
            $category = $em->getRepository('AdminBundle:Category')->find($category_post);
            $bussine->setCategoryid($category);
            // Website fix.
            $website = $bussine->getWebsite();
            if (!strchr($website, 'http')) {
                $website = 'http://' . $website;
            }
            // Facebook fix.
            $fb = $bussine->getFacebook();
            if (!strchr($fb, 'http')) {
                $fb = 'http://' . $fb;
            }
            // Instagram fix.
            $ins = $bussine->getInstagram();
            if (!strchr($ins, 'http')) {
                $ins = 'http://' . $ins;
            }
            $bussine->setWebsite($website);
            $bussine->setFacebook($fb);
            $bussine->setInstagram($ins);
            // clean estates & country
            $estate = $bussine->getEstate();
            $estate = strtolower(trim($estate));
            $country = $bussine->getCountry();
            $country = strtolower(trim($country));
            //
            $bussine->setCountry($country);
            $bussine->setEstate($estate);
            //
            $em->persist($bussine);
            $em->flush();

            return $this->redirectToRoute('user_serv');

        }

        return $this->render('front/user/edit.html.twig', array(
            'bussine' => $bussine,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cat' => $cat,
            'serv' => $serv,
            '_locale' => $_locale
        ));
    }

    /**
     * Deletes a Bussines entity.
     *
     * @Route("/user/delete/services/{id}/{_locale}", name="serv_delete")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bussines $bussine, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($bussine);
        $form->handleRequest($request);
        $userid = $this->getUser()->getId();
        // Security check
        $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.userid = ?1 and p.id = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $id);
        $found = $query->getResult();
        if (count($found) == 0) {
            $this->redirectToRoute('404');
        }
        //
        if ($form->isSubmitted() && $form->isValid()) {
            // Remove one from statistics 2
            $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
            $query->setParameter(1, $userid);
            $userdata = $query->getResult();
            $stats2 = $userdata[0]->getStats2();
            $userdata[0]->setStats2($stats2 - 1);
            $em->persist($userdata[0]);
            $em->flush();
            //
            $em->remove($bussine);
            $em->flush();
            return $this->redirectToRoute('user_serv');
        }
        return $this->redirectToRoute('user_serv');
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
            ->setAction($this->generateUrl('serv_delete', array('id' => $bussine->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Business Link User
     *
     * @Route("/user/services/{id}/link/{_locale}", name="services_link")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function linkAction($id)
    {
        if ($this->getUser())
            $userid = $this->getUser()->getId();
        else return $this->redirectToRoute('404');
        $em = $this->getDoctrine()->getManager();
        $serv = $em->getRepository('AdminBundle:Bussines')->find($id);
        $user = $em->getRepository('AdminBundle:User')->find($userid);

        // Check for Cheater
        $query = $em->createQuery('SELECT p FROM AdminBundle:LinkedUsers p WHERE p.userid = ?1 and p.bussinesid = ?2');
        $query->setParameter(1, $userid);
        $query->setParameter(2, $serv->getId());
        $linked = $query->getResult();

        if ($linked == null) {
            $lu = new LinkedUsers();
            $lu->setBussinesid($serv);
            $lu->setUserid($user);
            $lu->setJoindate(date('d-m-y'));
            // 2 porq no esta aprobado.
            $lu->setLinked(2);
            $em->persist($lu);
            $em->flush();
        } else {
            $linked[0]->setLinked(2);
            $em->persist($linked[0]);
            $em->flush();
        }
        // Mandar nivel y descuento.
        $query = $em->createQuery('SELECT p FROM AdminBundle:Userdata p WHERE p.userid = ?1');
        $query->setParameter(1, $userid);
        $userdata = $query->getResult();
        //
        $nivel = $userdata[0]->getLevel();
        $descuento = $userdata[0]->getPlanid()->getDescuento();
        //
        $totalphp = ($serv->getDescuento() * $descuento) / 100;
        $dinerophp = ($serv->getPrice() * $totalphp) / 100;
        //
        // Send the log the the owner
        $logs = 'The User ' . $this->getUser()->getUsername() . ' reserved ' . $serv->getName() . '. Contact this user via e-mail: 
        ' . $this->getUser()->getEmail() . ' or via phone: ' . $this->getUser()->getPhonenumber();
        //'. This user has level '.$nivel.' and get '.$totalphp.'% ($'.$dinerophp.') discount from your service if you accept the request.';
        //Send the mail to the service owner.
        //mail($serv->getUserid()->getUsername(),'3Swings.com Reservation Request',$logs);
        //
        $this->get('app.mailsender')->send_mail($serv->getUserid()->getUsername(), '3Swings.com Reservation Request', $logs, 'noreply@3swings.com');
        $this->get('app.setLogs')->setuserlogs($logs, $serv->getUserid()->getId());
        return $this->redirectToRoute('business_details', array('id' => $serv->getId()));
    }

    /**
     * Terms and Conditions page
     *
     * @Route("/services_terms_and_conditions/{_locale}", name="termstwo")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function servtermsAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $terms = $em->getRepository('AdminBundle:Settings')->find(1);
        return $this->render('front/termstwo.html.twig', array('terms' => $terms, 'locale' => $_locale));
    }
}