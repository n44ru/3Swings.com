<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CategoryController extends Controller
{
    /**
     * Category Controller
     *
     * @Route("/categories/all/{_locale}", name="category_all")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function catAction($_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('AdminBundle:Category')->findAll();
        return $this->render('front/categories/all.html.twig', array('cat'=>$cat, '_locale'=>$_locale));
    }

    /**
     * View all Services by Category
     *
     * @Route("/categories/view/{id}/{_locale}", name="category_view")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function viewAction($_locale, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT p FROM AdminBundle:Bussines p WHERE p.categoryid = ?1 ORDER BY p.country ASC');
        $query->setParameter(1, $id);
        $all = $query->getResult();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();

        return $this->render('front/categories/services.html.twig', array('services'=>$all, '_locale'=>$_locale, 'cat_id'=>$id, 'photos'=>$photos));
    }
}