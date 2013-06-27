<?php

namespace Ultimate\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UltimateGameBundle:Default:index.html.twig', array());
    }
}
