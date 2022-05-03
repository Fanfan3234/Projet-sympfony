<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     */
    public function index(): Response
    {
        $nomRoute = "test";
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            "route" => $nomRoute
        ]);
    }

    /**
     * @Route("/route-param/{prenom}-{nom}", name="route_param")
     */
    public function routeParam(String $prenom, String $nom): Response
    {
        return $this->render('test/route-param.html.twig', [

            "prenom" => $prenom,
            "nom" => $nom
        ]);
    }
}
