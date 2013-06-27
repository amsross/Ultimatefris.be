<?php

namespace Ultimate\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Ultimate\DefaultBundle\Form\Type\ContactType,
	Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response
	;

class DefaultController extends Controller
{
	public function indexAction()
	{

		// return $this->redirect($this->generateUrl('get_games'), 301);

		return $this->render('UltimateDefaultBundle:Default:index.html.twig', array());
	}

	public function aboutAction()
	{
		return $this->render('UltimateDefaultBundle:Default:about.html.twig', array());
	}

	public function contactAction(Request $request)
	{
		$form = $this->createForm(new ContactType());

		if ($request->isMethod('POST')) {
			$form->bind($request);

			if ($form->isValid()) {
				$message = \Swift_Message::newInstance()
					->setSubject('Ultimatefris.be Contact Form Submission')
					->setFrom('noreply@ultimatefris.be')
					->setTo('matt@ultimatefris.be')
					->setBody(
						$this->renderView(
							'UltimateDefaultBundle:Default:email.html.twig',
							array(
								'ip' => $request->getClientIp(),
								'email' => $form->get('email')->getData(),
								'name' => $form->get('name')->getData(),
								'message' => $form->get('message')->getData()
							)
						)
					);

				$this->get('mailer')->send($message);

				$request->getSession()->getFlashBag()->add('success', 'Your email has been sent! Thanks!');

				return $this->render('UltimateDefaultBundle:Default:contact.html.twig', array('form' => $form->createView()));
			}
		}

		return $this->render('UltimateDefaultBundle:Default:contact.html.twig', array('form' => $form->createView()));
	}
}
