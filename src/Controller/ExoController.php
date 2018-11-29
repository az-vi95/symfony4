<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExoController
 * @package App\Controller
 *
 * @Route("/exo")
 */
class ExoController extends AbstractController
{
    /**
     * faire une page avec un formulaire en POST avec :
     * - email(champ text)
     * - message (textarea)
     *
     * Si le formulaire est envoyé,vérifier que les 2 champs sont remplis
     * Si non, afficher un message d'erreur
     * Si oui, enregistrer les 2 valeurs en session et redériger vers une
     * nouvelle page qui affiche l'email et le message en prenant en compte
     * les sauts de lignes dans le message et vider la session.Si la session est vide
     * rediriger vers le formulaire
     *
     * @Route("/")
     */
    public function index()
    {
        return $this->render('exo/index.html.twig', [
            'controller_name' => 'ExoController',
        ]);
    }


    public function  request(Request $request)
    {

    }










}
