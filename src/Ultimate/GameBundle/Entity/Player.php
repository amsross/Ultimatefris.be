<?php

namespace Ultimate\GameBundle\Entity;

use
	Doctrine\ORM\Mapping as ORM,
	Doctrine\Common\Collections\ArrayCollection
	;

/**
 * Ultimate\GameBundle\Entity\Player
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="Player")
 */
class Player
{

	/**
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer", unique=true)
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $phone;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $carrier;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $lastIp;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $noSMS;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $noEmail;
	
    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="players")
     */
	protected $games;

	function __construct() {
		$this->games = new ArrayCollection();
	}
	

	protected function formatPhoneNumber()
	{
		$phoneNumber = preg_replace('/[^0-9]/','',$this->phone);

		if(strlen($phoneNumber) == 10) {

			$areaCode = substr($phoneNumber, 0, 3);
			$nextThree = substr($phoneNumber, 3, 3);
			$lastFour = substr($phoneNumber, 6, 4);

			$phoneNumber = '1'.$areaCode.$nextThree.$lastFour;

		} else {

			// We'll definitely need the area code, so break here
			return false;
			// throw new \Exception('Invalid phone number');

		}

		return $phoneNumber;
	}

	/**
	 * @ORM\PrePersist()
	 */
	public function prePersist()
	{
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
     * Set phone
     *
     * @param string $phone
     * @return Player
     */
    public function setPhone($phone)
    {
        // $this->phone = $this->formatPhoneNumber($phone);
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
    	return $this->formatPhoneNumber($this->phone);
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Player
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add games
     *
     * @param \Ultimate\GameBundle\Entity\Game $games
     * @return Player
     */
    public function addGame(\Ultimate\GameBundle\Entity\Game $games)
    {
        $this->games[] = $games;

        return $this;
    }

    /**
     * Remove games
     *
     * @param \Ultimate\GameBundle\Entity\Game $games
     */
    public function removeGame(\Ultimate\GameBundle\Entity\Game $games)
    {
        $this->games->removeElement($games);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Player
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set carrier
     *
     * @param string $carrier
     * @return Player
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * Get carrier
     *
     * @return string 
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Set lastIp
     *
     * @param string $lastIp
     * @return Player
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;

        return $this;
    }

    /**
     * Get lastIp
     *
     * @return string 
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * Set noSMS
     *
     * @param boolean $noSMS
     * @return Player
     */
    public function setNoSMS($noSMS)
    {
        $this->noSMS = $noSMS;

        return $this;
    }

    /**
     * Get noSMS
     *
     * @return boolean 
     */
    public function getNoSMS()
    {
        return $this->noSMS;
    }

    /**
     * Set noEmail
     *
     * @param boolean $noEmail
     * @return Player
     */
    public function setNoEmail($noEmail)
    {
        $this->noEmail = $noEmail;

        return $this;
    }

    /**
     * Get noEmail
     *
     * @return boolean 
     */
    public function getNoEmail()
    {
        return $this->noEmail;
    }
}
