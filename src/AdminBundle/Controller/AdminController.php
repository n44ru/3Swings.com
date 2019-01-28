<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Admin controller.
 *
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     *
     * @Route("/dashboard", name="dashboard")
     * @Method("GET")
     */
    public function dashAction()
    {
        //Clean temporal users.
        $this->get('app.clean')->users();
        $this->get('app.backend')->automatic_degrade();

        return $this->render('back/admin.html.twig');
    }
}
