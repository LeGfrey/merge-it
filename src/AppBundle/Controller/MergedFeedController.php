<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\MergedFeed;
use AppBundle\Form\MergedFeedType;

/**
 * MergedFeed controller.
 *
 * @Route("/user/merged")
 */
class MergedFeedController extends Controller
{
    /**
     * Lists all MergedFeed entities.
     *
     * @Route("/", name="user_merged_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mergedFeeds = $em->getRepository('AppBundle:MergedFeed')->findAll();

        return $this->render('mergedfeed/index.html.twig', array(
            'mergedFeeds' => $mergedFeeds,
        ));
    }

    /**
     * Creates a new MergedFeed entity.
     *
     * @Route("/new", name="user_merged_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $mergedFeed = new MergedFeed();
        $form = $this->createForm('AppBundle\Form\MergedFeedType', $mergedFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mergedFeed);
            $em->flush();

            return $this->redirectToRoute('user_merged_show', array('id' => $mergedFeed->getId()));
        }

        return $this->render('mergedfeed/new.html.twig', array(
            'mergedFeed' => $mergedFeed,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MergedFeed entity.
     *
     * @Route("/{id}", name="user_merged_show")
     * @Method("GET")
     */
    public function showAction(MergedFeed $mergedFeed)
    {
        $deleteForm = $this->createDeleteForm($mergedFeed);
        $feeds = $mergedFeed->getFeeds()->getValues();

        return $this->render('mergedfeed/show.html.twig', array(
            'mergedFeed' => $mergedFeed,
            'feeds' => $feeds,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MergedFeed entity.
     *
     * @Route("/{id}/edit", name="user_merged_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MergedFeed $mergedFeed)
    {
        $deleteForm = $this->createDeleteForm($mergedFeed);
        $editForm = $this->createForm('AppBundle\Form\MergedFeedType', $mergedFeed);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mergedFeed);
            $em->flush();

            return $this->redirectToRoute('user_merged_edit', array('id' => $mergedFeed->getId()));
        }

        return $this->render('mergedfeed/edit.html.twig', array(
            'mergedFeed' => $mergedFeed,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a MergedFeed entity.
     *
     * @Route("/{id}", name="user_merged_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MergedFeed $mergedFeed)
    {
        $form = $this->createDeleteForm($mergedFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mergedFeed);
            $em->flush();
        }

        return $this->redirectToRoute('user_merged_index');
    }

    /**
     * Creates a form to delete a MergedFeed entity.
     *
     * @param MergedFeed $mergedFeed The MergedFeed entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MergedFeed $mergedFeed)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_merged_delete', array('id' => $mergedFeed->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
