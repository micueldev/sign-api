<?php

namespace ControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ControllerBundle:Default:index.html.twig');
    }
}
