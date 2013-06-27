<?php
namespace Ultimate\GameBundle\Command;

use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\HttpFoundation\Response
	;

class EmailCommand extends ContainerAwareCommand
{
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

	protected function configure()
	{
		$this
			->setName('game:reminders')
			->setDescription('Send Game Reminders')
			// ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
			// ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
			;
	}

	protected function formatPhoneNumber($phoneNumber)
	{
		$phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

		// if(strlen($phoneNumber) > 10) {
		// 	$countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
		// 	$areaCode = substr($phoneNumber, -10, 3);
		// 	$nextThree = substr($phoneNumber, -7, 3);
		// 	$lastFour = substr($phoneNumber, -4, 4);

		// 	$phoneNumber = $countryCode.$areaCode.$nextThree.$lastFour;
		// } else
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

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		
		date_default_timezone_set ('America/New_York');

		// Set up some variables
		$this->container = $this->getApplication()->getKernel()->getContainer();
		$this->doctrine = $this->container->get('doctrine');
		$this->em = $this->doctrine->getManager();
		$this->mailer = $this->container->get('mailer');
		$this->spool = $this->mailer->getTransport()->getSpool();
		$this->transport = $this->container->get('swiftmailer.transport.real');

		// Get all the games in the next hour
		$games = $this
			->doctrine
			->getRepository('UltimateGameBundle:Game')
			->createQueryBuilder('g')
			->where("g.id IS NOT NULL")
			->andWhere("g.date >= ?1")
			->andWhere("g.date <= ?2")
			->setParameters(
				array(
					1 => date('Y-m-d H:00:00', mktime(date("H"), 0, 0, date("m")  , date("d"), date("Y"))),
					2 => date('Y-m-d H:31:00', mktime(date("H")+1, 0, 0, date("m")  , date("d"), date("Y")))
				)
			)
			->getQuery()
			->getResult()
			;

		foreach ($games as $game) {

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
									'UltimateGameBundle:Games:reminder.txt.twig',
									array(
										'game' => $game,
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
							->setSubject('Ultimatefris.be Game Reminder')
							->setFrom(array('noreply@ultimatefris.be' => 'Ultimatefris.be'))
							->setSender('noreply@ultimatefris.be')
							->setTo($playerEmail)
							->setContentType('text/html')
							->setBody(
								$this->container->get('templating')->render(
									'UltimateGameBundle:Games:reminder.html.twig',
									array(
										'game' => $game,
									)
								)
							)
						;

						// Spool the current message
						$this->mailer->send($message);
					}

				}
			}
		}

		// Send the spooled messages
		$this->spool->flushQueue($this->transport);
	}
}