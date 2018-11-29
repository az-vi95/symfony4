<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/http")
 */
class HttpController extends AbstractController
{
    /**
     *
     * @Route("/")
     */
    public function index()
    {
        return $this->render('http/index.html.twig.html.twig', [
            'controller_name' => 'HttpController',
        ]);
    }

    /**
     * @Route("/request")
     */
    public function request(Request $request)
    {
        /**
         * la fonction dump() vient du framework
         * elle fait un var_dump() dont le résultat
         * se trouve dans la barre de debug
         */
        var_dump($_GET);

        /*
         * $request->query est l'attribut de l'objet Request qui fait référence à $_GET
         * sa méthode all() retourne la totalité du tableau $_GET
         */
        dump($request->query->all());
        //var_dump($_GET['nom'])
        // renvoie null si 'nom' n'existe pas dans la query string
        dump($request->query->get('nom'));

        // renvoie 'anonyme' si 'nom' n'existe pas dans la query string
        dump($request->query->get('nom', 'anonyme'));

        //GET ou POST
        dump($request->getMethod());

        // équivaut à if(!empty($_POST))
        if($request->isMethod('POST')) {
            echo 'on a recu des données de formulaire ';
            // $request->request est l'attribut de l'objet Request qui fait référence à $_POST
            dump($request->request->all());
            // $request->request fonctionne de la meme maniere de $request->query
            dump($request->request->get('nom'));
        }

        if (!$request->isXmlHttpRequest()) {
            echo "La page n'est pas appelée en ajax";
        }

        return $this->render('http/request.html.twig');


    }

    /**
     * @param Request $request
     * @Route("/session")
     */
    public function session(Request $request)
    {
        // pour accéder à la session
        $session = $request->getSession();

        // ajoute un élément à la session
        // $_SESSION['nom'] = 'Teller';
        $session->set('nom', 'Teller');
        $session->set('prenom', 'Jax');
        dump($_SESSION);

        // accede à l'élément 'nom' de la session
        dump($session->get('nom'));

        // tous les éléments de la session
        dump($session->all());

        // supprime un élément de la session
        $session->remove('nom');
        dump($session->all());

        // vider la session
        $session->clear();
        dump($session->all());

        return $this->render('http/session.html.twig');
    }

    /**
     * @Route("/response")
     */
    public function response(Request $request)
    {
        // une méthode de controleur doit forcément
        // retourner un objet instance Response

        // une réponse qui contient du texte brut
        $response = new Response('Ma réponse');

        //if ($_GET['type'] == ' twig' )
        if ($request->query->get('type') == 'twig') {
            // $this->render() retourne un objet Response
            // dont le contenu est le HTML construit par le template
            $response = $this->render('http/response.html.twig');
            // http://127.0.0.1:8000/http/response?type=json
        }elseif ($request->query->get('type') == 'json') {
            $exemple = [
                'nom'       => 'Teller',
                'prenom'    => 'Jax'
            ];
            $response = new JsonResponse($exemple);
            // équivaut $response = new Response(json_encode($exemple));

            // http://127.0.0.1:8000/http/response?found=no
        }elseif ($request->query->get('found') == 'no') {
            // on jete cette exception pour retourner une 404
            throw new NotFoundHttpException();

            // http://127.0.0.1:8000/http/response?redirect=index
        } elseif ($request->query->get('redirect') == 'index'){
            // redirige vers la page dont la route a pour nom  app_index_index
            $response = $this->redirectToRoute('app_index_index');
            // http://127.0.0.1:8000/http/response?redirect=index

        } elseif ($request->query->get('redirect') == 'bonjour'){
            // redirige vers une route dont l'url contient une partie variable {qui}
            $response = $this->redirectToRoute(
            // http://127.0.0.1:8000/http/response?redirect=bonjour
                'app_index_bonjour',
                [
                    'qui' => 'moi'
                ]
            );
        }

        return $response;
    }

    /**
     * @Route("/flash")
     */
    public function flash()
    {
        // ajoute un message flash de type success
        $this->addFlash('success', 'Message de succès');

        return $this->redirectToRoute('app_http_flashed');
    }

    /**
     * @Route("/flashed")
     */
    public function flashed()
    {
        return $this->render('http/flashed.html.twig');
    }









}
