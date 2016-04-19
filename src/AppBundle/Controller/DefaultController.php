<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $divisions = $this->getDoctrine()->getRepository('AppBundle:Division')->findAll();
        
        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:home.html.twig', array(
            'divisions' => $divisions,
        ));
    }
}
