<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $session = new Session();
        $session->getFlashBag()->add('errors','Erreur sur le formulaire ');

        // flashbag : peek = renvoi le message sans la supprimer 

        dump($session);
        // replace this example code with whatever you need
        return $this->render('homepage/index.html.twig');
    }
}
