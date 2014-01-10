<?php

namespace Ultimate\GameBundle\Controller;

use
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
	Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\HttpFoundation\RedirectResponse
	;

class GameController extends Controller
{

	/**
	 * @Route("/{id}", name="get_game")
	 * @Template()
	 */
	public function getGameAction($id)
	{
		return array('id' => $id);
	}

	/**
	 * @Route("/", name="get_games")
	 * @Template()
	 */
	public function getGamesAction()
	{
		return array();
	}
}
