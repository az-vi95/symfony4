<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * Annotation de routing:
     * définit l'url de la page qui exécute le contenu
     * de la méthode qui suit
     * @Route("/hello")
     */
    public function hello()
    {
        // le che min du template utilisé pour l'affichage
        // à partir de la racine du répertoire templates
        return $this->render('index/hello.html.twig');
    }

    /**
     * Une route avec une partie variable,le {qui}
     * Pour accéder à sa valeur dans la méthode,
     * il faut qu'elle prenne en parametre une variable
     * du meme nom, ici $qui
     *
     * @Route("/bonjour/{qui}")
     */
    public function bonjour($qui)
    {
        return $this->render(
            'index/bonjour.html.twig',
            // tableau des variables disponibles dans le template.Les clés dans ce tableau
            // sont les noms des variables dans le template
            [
                'nom'   => $qui
            ]
        );
    }

    /**
     * Le parametre devient optionnel en lui donnant une valeur par défaut
     * grace au parametre defaults que l'on passe à la route au format json
     * La route match /salut/unNom et /salut
     *
     * @Route("/salut/{nom}", defaults={"nom": "à toi"}) // defaults permet d'avoir une valeur par défaut
     */
    public function salut($nom)
    {
        return $this->render(
            'index/salut.html.twig',
            [
                'nom'   => $nom
            ]
        );
    }

    /**
     * Une route avec 2 parties variable dont une est optionnelle
     * - Match /coucou/Jax et /coucou/Jax-Teller
     * - Ne match pas /coucou car {prenom} est obligatoire( pas de valeur par défaut)
     * @Route("/coucou/{prenom}{nom}", defaults={"nom":""})
     */
    public function coucou($prenom, $nom)
    {
        $nomComplet = rtrim($prenom . ' ' . $nom);

        return $this->render(
            'index/coucou.html.twig',
            [
                'nom'   => $nomComplet
            ]
        );

    }

    /**
     * @Route("/categorie/{id}", requirements={"id": "\d+"})
     */
    public function categorie($id)
    {
        return $this->render(
            'index/categorie.html.twig',
            [
                'id'    => $id
            ]
        );
    }
}
