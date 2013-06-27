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

	/**
	 * @ORM\ManyToMany(targetEntity="Ultimate\GameBundle\Entity\Player", inversedBy="games")
	 * @ORM\JoinTable(name="Games_Players")
	 */
    protected $players;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
	protected $date;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
	protected $dateCreated;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	protected $originIp;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	protected $title;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	protected $location;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
	protected $info;

	function __construct() {
		$this->setDate();
		$this->players = new ArrayCollection();
	}

	/**
	 * @ORM\PrePersist()
	 */
	public function prePersist()
	{
		$this->setDateCreated(new \DateTime());
	}

	/**
	 * @ORM\PreUpdate()
	 */
	public function preUpdate()
	{
	}

    /**
     * Set id
     *
     * @return Game
     */
    public function setId($id)
    {
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Game
     */
    public function setDate($date=null)
    {
    	$date = $date ? new \DateTime($date) : new \DateTime();
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Game
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Game
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Game
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Add players
     *
     * @param \Ultimate\GameBundle\Entity\Player $players
     * @return Game
     */
    public function addPlayer($players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \Ultimate\GameBundle\Entity\Player $players
     */
    public function removePlayer($players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set originIp
     *
     * @param string $originIp
     * @return Game
     */
    public function setOriginIp($originIp)
    {
        $this->originIp = $originIp;

        return $this;
    }

    /**
     * Get originIp
     *
     * @return string 
     */
    public function getOriginIp()
    {
        return $this->originIp;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return Game
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }
}
