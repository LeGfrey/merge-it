<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request) {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject($form->get('subject')->getData())
                ->setFrom($form->get('email')->getData())
                ->setTo('geoffrey@rssmergeit.com')
                ->setBody('FROM: '.$form->get('name')->getData().' ('.$request->getClientIp().")\n".$form->get('message')->getData());
            $this->get('mailer')->send($message);
            $request->getSession()->getFlashBag()->add('success', 'Your email has been sent! Check your inbox for an answer.');
        }

        return $this->render('default/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
