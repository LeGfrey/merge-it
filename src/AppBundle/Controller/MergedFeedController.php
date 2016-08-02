<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\MergedFeed;
use AppBundle\Form\MergedFeedType;
use Symfony\Component\HttpFoundation\Response;

/**
 * MergedFeed controller.
 */
class MergedFeedController extends Controller
{
    /**
     * Lists all MergedFeed entities.
     *
     * @Route("/user/merged/", name="user_merged_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mergedFeeds = $em->getRepository('AppBundle:MergedFeed')->findByAuthor($this->getUser());

        return $this->render('mergedfeed/index.html.twig', array(
            'mergedFeeds' => $mergedFeeds,
        ));
    }

    /**
     * Creates a new MergedFeed entity.
     *
     * @Route("/user/merged/new", name="user_merged_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mergedFeed = new MergedFeed();
        $feeds = $em->getRepository('AppBundle:Feed')->findByAuthor($this->getUser());

        $form = $this->createForm('AppBundle\Form\MergedFeedType', $mergedFeed, ['choices' => $feeds]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // set the author
            $mergedFeed->setAuthor($this->getUser());
            $em->persist($mergedFeed);
            $em->flush();
            $request->getSession()->getFlashBag()->set('success', 'Merged feed successfully added');

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
     * @Route("/user/merged/{id}", name="user_merged_show")
     * @Method("GET")
     */
    public function showAction(MergedFeed $mergedFeed)
    {
        if($mergedFeed->getAuthor()->getId() != $this->getUser()->getId()) {
            return $this->render('default/forbidden.html.twig');
        }

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
     * @Route("/user/merged/{id}/edit", name="user_merged_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MergedFeed $mergedFeed)
    {
        if($mergedFeed->getAuthor()->getId() != $this->getUser()->getId()) {
            return $this->render('default/forbidden.html.twig');
        }

        $em = $this->getDoctrine()->getManager();

        $feeds = $em->getRepository('AppBundle:Feed')->findByAuthor($this->getUser());

        $deleteForm = $this->createDeleteForm($mergedFeed);
        $editForm = $this->createForm('AppBundle\Form\MergedFeedType', $mergedFeed, ['choices' => $feeds]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($mergedFeed);
            $em->flush();
            $request->getSession()->getFlashBag()->set('success', 'Merged feed successfully edited');

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
     * @Route("/user/merged/{id}", name="user_merged_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MergedFeed $mergedFeed)
    {
        if($mergedFeed->getAuthor()->getId() != $this->getUser()->getId()) {
            return $this->render('default/forbidden.html.twig');
        }

        $form = $this->createDeleteForm($mergedFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mergedFeed);
            $em->flush();
            $request->getSession()->getFlashBag()->set('success', 'Merged feed successfully removed');
        }

        return $this->redirectToRoute('user_merged_index');
    }

    /**
     * Get the merged feed
     *
     * @Route("/{ownerId}/feeds/{id}/feed.xml", name="user_merged_merge")
     * @Method("GET")
     */
    public function mergeAction(Request $request, MergedFeed $mergedFeed, $ownerId) {
        if($mergedFeed->getAuthor()->getId() != $ownerId) {
            return $this->render('default/forbidden.html.twig');
        }

        $rssHelper = $this->get('rss_helper');
        $feeds = $mergedFeed->getFeeds()->toArray();
        $feeds = array_map(function($feed) {
            return $feed->getUrl();
        }, $feeds);

        $mergedFeedString = $rssHelper->mergeFeeds($feeds, $mergedFeed->getName(), $request->getUri(), 'Rss feed generated by MergeIt service');
        
        $response = new Response();
        
        // if we have an error merging rss
        if(!$mergedFeedString) {
            $response->setStatusCode(500);
            $response->setContent('Error parsing feeds. Please contact the administrator :)');
            return $response;
        }
        
        $response->setContent($mergedFeedString);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/rss+xml');

        return $response;
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
