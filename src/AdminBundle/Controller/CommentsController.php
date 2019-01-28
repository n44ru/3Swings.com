<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Comments;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 * @Route("admin/comments")
 */
class CommentsController extends Controller
{
    /**
     * Lists all comment entities.
     *
     * @Route("/", name="comments_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('AdminBundle:Comments')->findAll();

        return $this->render('back/comments/index.html.twig', array(
            'comments' => $comments,
        ));
    }

    /**
     * Creates a new comment entity.
     *
     * @Route("/new", name="comments_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $comment = new Comments();
        $form = $this->createForm('AdminBundle\Form\CommentsType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            //
            $bussines = $em->getRepository('AdminBundle:Bussines')->find($comment->getBussinesid()->getId());
            // Save the rating in the rating row.
            if($bussines->getRating() != 0){
                $old = $bussines->getRating();
                $new = ($old + $comment->getStars())/2;
                $bussines->setRating($new);
            }
            else{
                $bussines->setRating($comment->getStars());
            }
            $em->persist($bussines);
            $em->flush();
            //
            return $this->redirectToRoute('comments_index');
        }

        return $this->render('back/comments/new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     * @Route("/{id}/edit", name="comments_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comments $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);
        $editForm = $this->createForm('AdminBundle\Form\CommentsType', $comment);
        $editForm->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($comment);
            $em->flush($comment);

            return $this->redirectToRoute('comments_index');
        }

        return $this->render('back/comments/edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comment entity.
     *
     * @Route("/{id}", name="comments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Comments $comment)
    {
        $form = $this->createDeleteForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush($comment);
        }

        return $this->redirectToRoute('comments_index');
    }

    /**
     * Creates a form to delete a comment entity.
     *
     * @param Comments $comment The comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comments $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comments_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
