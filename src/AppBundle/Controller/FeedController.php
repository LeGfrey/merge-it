<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Feed;
use AppBundle\Form\FeedType;

/**
 * Feed controller.
 *
 * @Route("/user/feed")
 */
class FeedController extends Controller
{
    /**
     * Lists all Feed entities.
     *
     * @Route("/", name="user_feed_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $feeds = $em->getRepository('AppBundle:Feed')->findAll();

        return $this->render('feed/index.html.twig', array(
            'feeds' => $feeds,
        ));
    }

    /**
     * Creates a new Feed entity.
     *
     * @Route("/new", name="user_feed_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $feed = new Feed();
        $form = $this->createForm('AppBundle\Form\FeedType', $feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // set the author
            $feed->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($feed);
            $em->flush();

            return $this->redirectToRoute('user_feed_show', array('id' => $feed->getId()));
        }

        return $this->render('feed/new.html.twig', array(
            'feed' => $feed,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Feed entity.
     *
     * @Route("/{id}", name="user_feed_show")
     * @Method("GET")
     */
    public function showAction(Feed $feed)
    {
        $deleteForm = $this->createDeleteForm($feed);

        return $this->render('feed/show.html.twig', array(
            'feed' => $feed,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Feed entity.
     *
     * @Route("/{id}/edit", name="user_feed_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Feed $feed)
    {
        $deleteForm = $this->createDeleteForm($feed);
        $editForm = $this->createForm('AppBundle\Form\FeedType', $feed);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feed);
            $em->flush();

            return $this->redirectToRoute('user_feed_edit', array('id' => $feed->getId()));
        }

        return $this->render('feed/edit.html.twig', array(
            'feed' => $feed,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Feed entity.
     *
     * @Route("/{id}", name="user_feed_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Feed $feed)
    {
        $form = $this->createDeleteForm($feed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($feed);
            $em->flush();
        }

        return $this->redirectToRoute('user_feed_index');
    }

    /**
     * Creates a form to delete a Feed entity.
     *
     * @param Feed $feed The Feed entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Feed $feed)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_feed_delete', array('id' => $feed->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
