<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Préfixe de route accolé à chaque url définie dans les routes
 * des méthodes du controleur
 *
 * @Route("/twig")
 */
class TwigController extends AbstractController
{
    /**
     * L'url de la route est /twig/ ou /twig parce qu'il y a le préfixe de route défini
     * au dessus de la classe
     * @Route("/")
     */
    public function index()
    {
        return $this->render(
            'twig/index.html.twig',
            [
                'auj' => new\DateTime()
            ]

        );
    }
}
