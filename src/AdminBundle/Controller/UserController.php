<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Photos;
use AdminBundle\Entity\Userdata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\User;

/**
 * User controller.
 *
 * @Route("admin/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //Clean temporal users.
        $this->get('app.clean')->users();

        $users = $em->getRepository('AdminBundle:User')->findAll();
        $photos = $em->getRepository('AdminBundle:Photos')->findAll();

        return $this->render('back/user/index.html.twig', array(
            'users' => $users,
            'photos' => $photos
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AdminBundle\Form\UserType', $user);
        $form->handleRequest($request);
        $em= $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            // checking the username again
            $username = $user->getUsername();
            $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.username = ?1 ');
            $query->setParameter(1, $username);
            $user_check = $query->getResult();
            if(count($user_check)>0)
            {
                return $this->redirectToRoute('admin_user_error');
            }
            // checking the paypal user again
            $pay = $user->getPaypal();
            $query = $em->createQuery('SELECT p FROM AdminBundle:User p WHERE p.paypal = ?1 ');
            $query->setParameter(1, $pay);
            $user_check = $query->getResult();
            if(count($user_check)>0)
            {
                return $this->redirectToRoute('admin_paypal_error');
            }
            //
            $file = $user->getFile();
            $photos = new Photos();
            if($file!=null)
            {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
                $file->move($imagesdir, $fileName);
                $photos->setPath('uploads/profiles/'.$fileName);
                $photos->setUserid($user);
            }
            else{
                $photos->setPath('images/user.png');
                $photos->setUserid($user);
            }

            $p = $user->getPassword();
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($p, $user->getSalt());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $user->setTemp(0);
            $user->setUsername($user->getEmail());
            $em->persist($user);
            $em->persist($photos);
            $em->flush();
            // Filling Userdata Entity.
            $userdata = new Userdata();
            $userdata->setMoney(0);
            $userdata->setUserid($user);
            $userdata->setStats1(0);
            $userdata->setStats2(0);
            $userdata->setStats3(0);
            // Temp Values, change later.
            $userdata->setLevel(1);
            $userdata->setPlandate(date('Y-m-d'));
            $userdata->setFirst(10);
            $userdata->setInvitationcode('created_by_admin');
            $userdata->setDad($this->getUser()->getId());
            // set yes en 0
            $userdata->setYes(0);
            $userdata->setNo(0);
            // Set the plan
            $plan = $em->createQuery('SELECT p FROM AdminBundle:Plan p WHERE p.level = 1');
            $level=$plan->getResult();
            $userdata->setPlanid($level[0]);
            $em->persist($userdata);
            $em->flush();
            //

            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AdminBundle\Form\UserEditType', $user);
        $editForm->handleRequest($request);
        $em= $this->getDoctrine()->getManager();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $file = $user->getFile();
            $em = $this->getDoctrine()->getManager();
            if($file != null)
            {
                $id = $user->getId();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $imagesdir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/profiles';
                $file->move($imagesdir, $fileName);

                $query = $em->createQuery('SELECT p FROM AdminBundle:Photos p WHERE p.userid = ?1');
                $query->setParameter(1, $id);
                $photos = $query->getResult();

                $photos[0]->setPath('uploads/profiles/'.$fileName);
                $photos[0]->setUserid($user);
                $em->persist($photos[0]);
            }
            $user->setUsername($user->getEmail());
            // get a password
            if($request->request->get('_pass')){
                $pass = $request->request->get('_pass');
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($pass, $user->getSalt());
                $user->setPassword($password);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('back/user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Remove from code family
            $query = $em->createQuery('DELETE FROM AdminBundle:Codesfamily p WHERE p.name = ?1');
            $query->setParameter(1, $user->getUsername());
            $query->getResult();
            //
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * User Error
     *
     * @Route("/join/user_error/{_locale}", name="admin_user_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function usererrorAction(Request $request)
    {
        return $this->render('back/error/user.html.twig');
    }
    /**
     * Paypal Error
     *
     * @Route("/join/paypal/{_locale}", name="admin_paypal_error")
     * defaults={"_locale"="en"}, requirements={"_locale"="(es|en)"}
     * @Method({"GET", "POST"})
     */
    public function paypalAction(Request $request)
    {
        return $this->render('back/error/paypal.html.twig');
    }
}
