<?php

namespace Ultimate\GameBundle\Controller;

use
	Doctrine\Common\Collections\ArrayCollection,
	Doctrine\ORM,
	FOS\Rest\Util\Codes,
	FOS\RestBundle\Controller\FOSRestController,
	FOS\RestBundle\Controller\Annotations as Rest,
	FOS\RestBundle\View\View,
	Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
	Ultimate\GameBundle\Form\Type\GameType,
	Ultimate\GameBundle\Entity\Game,
	Ultimate\GameBundle\Form\Type\PlayerType,
	Ultimate\GameBundle\Entity\Player
	;

class GamesController extends FOSRestController
{

    /**
     * Get entity instance
     * @var Integer $gId Id of the entity
     * @return Game
    */
    private function getEntity($gId = null)
    {

        $request = Request::createFromGlobals();

        $paramCounter = 1;

        $entity = $this
            ->getDoctrine()
            ->getRepository('UltimateGameBundle:Game')
            ->createQueryBuilder('g')
            ->orderBy('g.date', 'DESC')
            ->where("g.id IS NOT NULL");

        if ($request->query->get('s') !== null) {

            $entity = $entity
                ->andWhere("g.location LIKE '%".$request->query->get('s')."%' OR g.title LIKE '%".$request->query->get('s')."%' OR g.info LIKE '%".$request->query->get('s')."%'");
            $paramCounter++;
        }

        // Limit result by id
        if ($gId !== null) {

            $entity = $entity
                ->andWhere("g.id = ?$paramCounter")
                ->setParameter($paramCounter, $gId)
                ->setMaxResults( 1 );
            $paramCounter++;

            $entity = $entity
                ->getQuery()
                ->getSingleResult();

            if (!$entity) {
                $entity = Array();

                // $view = $this->routeRedirectView('ultimate_default_homepage', array(), 301);
                // return $this->handleView($view);

                // throw $this->createNotFoundException('Unable to find game');
            }

        } else {

            $entity = $entity
                ->getQuery()
                ->getResult();

            if (!$entity) {
                $entity = Array();

                // $view = $this->routeRedirectView('ultimate_default_homepage', array(), 301);
                // return $this->handleView($view);

                // throw $this->createNotFoundException('Unable to find games');
            }

        }

		// print_r(array(
		// 	'sql'        => $entity->getSQL(),
		// 	'parameters' => $entity->getParameters(),
		// ));

        return $entity;
    }

    private function processForm($entity, $statusCode)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find game');
        }

        $request = $this->getRequest();

        $form = $this->createForm(new GameType(), $entity);
        
        if ($request->isMethod('POST')) {

	        $form->bind($request);

	        if ($form->isValid()) {
	            
	            $em = $this->getDoctrine()->getManager();

				$entity->setOriginIp($request->getClientIp());

	            $em->persist($entity);
	            $em->flush();

	            $response = new Response();
	            $response->setStatusCode($statusCode);

	            // set the 'Location' header only when creating new resources
	            if (201 === $statusCode) {
	            	return $this->redirect(
	                    $this->generateUrl(
	                        'get_game',
	                        array('gId' => $entity->getId()),
	                        true // absolute
	                    )
	        		);
	            }

	            return $response;
	        }
        }

        // return View::create($form, 200);
        return View::create($form, 400);
    }
	
	private $carriers = Array(
			'alltel' => 'sms.alltelwireless.com',
			'att' => 'txt.att.net',
			'bellsouth' => 'bellsouth.cl',
			'cricket' => 'sms.mycricket.com',
			'metropcs' => 'mymetropcs.com',
			'sprint' => 'messaging.sprintpcs.com',
			'tmobile' => 'tmomail.net',
			'verizon' => 'vtext.com',
		);

    private function sendNotifications($game, $newPlayer)
    {

		// Set up some variables
		$this->doctrine = $this->get('doctrine');
		$this->em = $this->doctrine->getManager();
		$this->mailer = $this->get('mailer');
		$this->spool = $this->mailer->getTransport()->getSpool();
		$this->transport = $this->get('swiftmailer.transport.real');

		$players = $game->getPlayers();
		
		foreach ($players as $player) {

			if ($player !== null) {

				$playerSMS = $player->getPhone() . '@' . $this->carriers[$player->getCarrier()];

				if (filter_var($playerSMS, FILTER_VALIDATE_EMAIL)) {

					// Compose the SMS message
					$message = \Swift_Message::newInstance()
						->setFrom(array('noreply@ultimatefris.be' => 'Ultimatefris.be'))
						->setSender('noreply@ultimatefris.be')
						->setTo($playerSMS)
						->setContentType('text/plain')
						->setBody(
							$this->container->get('templating')->render(
								'UltimateGameBundle:Games:notification.txt.twig',
								array(
									'game' => $game,
									'player' => $newPlayer,
								)
							)
						)
					;

					// Spool the current message
					$this->mailer->send($message);
				}

				$playerEmail = $player->getEmail();

				if (filter_var($playerEmail, FILTER_VALIDATE_EMAIL)) {
					
					// Compose the email message
					$message = \Swift_Message::newInstance()
						->setSubject('Ultimatefris.be New Player Notification')
						->setFrom(array('noreply@ultimatefris.be' => 'Ultimatefris.be'))
						->setSender('noreply@ultimatefris.be')
						->setTo($playerEmail)
						->setContentType('text/html')
						->setBody(
							$this->renderView(
								'UltimateGameBundle:Games:notification.html.twig',
								array(
									'game' => $game,
									'player' => $newPlayer,
								)
							)
						)
					;

					// Spool the current message
					$this->mailer->send($message);
				}

			}
		}

		// Send the spooled messages
		$this->spool->flushQueue($this->transport);
    }

	public function optionsGamesAction()
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "options_games" [OPTIONS] /games

    /**
     * Collection get action
     * @return array
     *
     * @Rest\Route("/games", name="get_games", options={"expose"=true})
     * @Rest\View
     */
	public function getGamesAction()
	{
        $entities = $this->getEntity();
        
        return array(
            'games' => $entities,
            );
    } // "get_games"	 [GET] /games

    /**
     * @Rest\Route("/games/new", name="new_games", options={"expose"=true})
     * @Rest\View
     */
	public function newGamesAction()
	{
		return $this->processForm(new Game(), 200);
    } // "new_games"	 [GET] /games/new

    /**
     * @Rest\Route("/games", name="post_games", options={"expose"=true})
     * @Rest\View
     */
	public function postGamesAction()
	{
        return $this->processForm(new Game(), 201);
    } // "post_games"	[POST] /games

	public function patchGamesAction()
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "patch_games"   [PATCH] /games

    /**
     * @Rest\Route("/games/{gId}", name="get_game", options={"expose"=true})
     * @Rest\View
     */
	public function getGameAction($gId)
	{
        $entity = $this->getEntity($gId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find game');
        }

        return array(
            'games' => array($entity),
            );

        // return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "get_game"	  [GET] /games/{gId}

    /**
     * @Rest\Route("/games/{gId}/edit", name="edit_game", options={"expose"=true})
     * @Rest\View
     */
	public function editGameAction($gId)
	{
        $entity = $this->getEntity($gId);

		return $this->processForm($entity, 201 );
    } // "edit_game"	 [GET] /games/{gId}/edit

    /**
     * @Rest\Route("/games/{gId}", name="put_game", options={"expose"=true})
     * @Rest\View
     */
	public function putGameAction($gId)
	{
        return $this->processForm($entity = $this->getEntity($gId), 204);
    } // "put_game"	  [PUT] /games/{gId}

    /**
     * @Rest\Route("/games/{gId}", name="patch_game", options={"expose"=true})
     * @Rest\View
     */
	public function patchGameAction($gId)
	{
        return $this->processForm($entity = $this->getEntity($gId), 204);
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "patch_game"	[PATCH] /games/{gId}

	public function lockGameAction($gId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "lock_game"	 [PATCH] /games/{gId}/lock

    /**
     * @Rest\Route("/games/{gId}/remove", name="remove_game", options={"expose"=true})
     * @Rest\View(statusCode=204)
     */
    public function removeGameAction($gId)
    {
        $entity = $this->getEntity($gId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find game');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_NO_CONTENT );
    } // "remove_game"   [GET] /games/{gId}/remove

    /**
     * @Rest\Route("/games/{gId}", name="delete_game", options={"expose"=true})
     * @Rest\View(statusCode=204)
     */
	public function deleteGameAction($gId)
	{
        $entity = $this->getEntity($gId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find game');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_NO_CONTENT );
    } // "delete_game"   [DELETE] /games/{gId}

    /**
     * @Rest\Route("/games/{gId}/players", name="get_game_players", options={"expose"=true})
     * @Rest\View(statusCode=204)
     */
	public function getGamePlayersAction($gId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "get_game_players"	[GET] /games/{gId}/players

	public function newGamePlayersAction($gId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "new_game_players"	[GET] /games/{gId}/players/new

    /**
     * @Rest\Route("/games/{gId}/players", name="post_game_players", options={"expose"=true})
     * @Rest\View(statusCode=204)
     */
	public function postGamePlayersAction($gId, Request $request)
	{

        $game = $this->getEntity($gId);
        
        $requestPlayer = $request->request->get('player');

		$em = $this->getDoctrine()->getManager();
		$players = $em->createQueryBuilder()
			->select('p')
			->from('UltimateGameBundle:Player p')
			->where('p.phone = :phone OR p.email = :email OR p.lastIp = :ip')
			->setParameter('phone', $requestPlayer['phone'])
			->setParameter('email', $requestPlayer['email'])
			->setParameter('ip', $request->getClientIp())
			->getQuery()
			->getResult()
			;

		if (count($players) < 1) {
			$player = new Player();
		} else {
			$player = $players[0];
		}

		if (!empty($requestPlayer['name'])) {
			$player->setName($requestPlayer['name']);
		}
		if (!empty($requestPlayer['phone'])) {
			$player->setPhone($requestPlayer['phone']);
		}
		if (!empty($requestPlayer['carrier'])) {
			$player->setCarrier($requestPlayer['carrier']);
		}
		if (!empty($requestPlayer['email'])) {
			$player->setEmail($requestPlayer['email']);
		}
		$player->setLastIp($request->getClientIp());

		if ($game->getPlayers()->indexOf($player) === false) {
			$this->sendNotifications($game, $player);
			$game->addPlayer($player);
		}

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->persist($game);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_NO_CONTENT);

        return $response;
    } // "post_game_players"   [POST] /games/{gId}/players

	public function getGamePlayerAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "get_game_player"	 [GET] /games/{gId}/players/{cId}

	public function editGamePlayerAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "edit_game_player"	[GET] /games/{gId}/players/{cId}/edit

	public function putGamePlayerAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "put_game_player"	 [PUT] /games/{gId}/players/{cId}

	public function postGamePlayerVoteAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "post_game_player_vote" [POST] /games/{gId}/players/{cId}/vote

	public function removeGamePlayerAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "remove_game_player"  [GET] /games/{gId}/players/{cId}/remove

	public function deleteGamePlayerAction($gId, $cId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "delete_game_player"  [DELETE] /games/{gId}/players/{cId}

	public function linkGameAction($gId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "link_game_friend"	 [LINK] /games/{gId}

	public function unlinkGameAction($gId)
	{
        return $this->redirectView($this->generateUrl('get_games'), Codes::HTTP_TEMPORARY_REDIRECT );
    } // "link_game_friend"	 [UNLINK] /games/{gId}
}