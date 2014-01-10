<?php

namespace Ultimate\GameBundle\Controller;

use
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
	Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
	Symfony\Bundle\FrameworkBundle\Controller\Controller,
	Symfony\Component\HttpFoundation\RedirectResponse
	;

class PlayerController extends Controller
{

	/**
	 * @Route("/{id}", name="get_player")
	 * @Template()
	 */
	public function getPlayerAction($id)
	{
		return array('id' => $id);
	}

	/**
	 * @Route("/", name="get_players")
	 * @Template()
	 */
	public function getPlayersAction()
	{
		return array();
	}
}
