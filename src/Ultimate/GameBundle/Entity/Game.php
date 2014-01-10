<?php

namespace Ultimate\GameBundle\Entity;

use
	Doctrine\ORM\Mapping as ORM,
	Doctrine\Common\Collections\ArrayCollection
	;

/**
 * Ultimate\GameBundle\Entity\Game
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Game")
 */
class Game
{

	/**
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer", unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
}