<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Team;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DoctrineController
 * @package App\Controller
 *
 * @Route("/doctrine")
 */
class DoctrineController extends AbstractController
{
    /**
     * @Route("/user/{id}", requirements={"id": "\d+"})
     */
    public function index($id)
    {
        // gestionnaire d'entités de Doctrine
        $em = $this->getDoctrine()->getManager();

        /*
         * User::class == 'App\Entity\User'
         * Retourne un objet User dont les attributs sont settés à partir de la bdd
         * User avec l'id $id
         */
        $user = $em->find(User::class, $id);
        /*
         * en version longue :
        $repository = $em->getRepository(User::class);
        $repository->find($id);
         */

        dump($user);

        /*
         * s'il n'y a pas de user en bdd avec l'id
         * passé à la méthode find(), elle retourne null
         */
        if (is_null($user)) {
            // 404
            throw new NotFoundHttpException();
        }

        return $this->render(
            'doctrine/index.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/list-users")
     */
    public function listUsers()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(User::class);
        // retourne tous les utilisateur de la table user en bdd
        // sous la forme d'un tableau d'objets User
        $users = $repository->findAll();

        dump($users);

        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/search-email/{email}")
     */
    public function searchEmail($email)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        // raccourci pour :
        //$repository = $this->getDoctrine()->getManager()->getRepository(User::class);

        // findOneBy() quand on est sûr qu'il n'y aura pas plus d'un résultat
        // Retourne un objet User ou null si pas de résultat
        $user = $repository->findOneBy([
            'email' => $email
        ]);

        if (is_null($user)) {
            // 404
            throw new NotFoundHttpException();
        }

        return $this->render(
            'doctrine/index.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/search-firstname/{firstname}")
     */
    public function searchFirstname($firstname)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        // retourne un tableau d'objets User filtrés sur le prénom
        // s'il n'y a aucun résultat ,retourne un tableau vide
        $users = $repository->findBy([
           'firstname' => $firstname

        ]);
        // SELECT * FROM USERS WHERE firstname

        /*
         * équivalent d'un findAll() avec un order by firstname desc
         *  $users = $repository->findBy([], ['firstname' => 'DESC']);
         */

        // SELECT * FROM USERS ORDER BY firstname

        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $users
            ]
        );
    }

    /**
     * @Route("/create-user")
     */
    public function createUser(Request $request)
    {
        if($request->isMethod('POST')) {
            $data = $request->request->all();
            dump($data); // éq. $_POST

            // on instancie un User
            $user = new User();
            // et on sette ses attributs  avec les données du formulaire
            $user
                ->setLastname($data['lastname'])
                ->setFirstname($data['firstname'])
                ->setEmail($data['email'])
                // le setter de birthdate attend un objet DateTime
                ->setBirthdate(new \DateTime($data['birthdate']))
                ;
            dump($user);

            $em = $this->getDoctrine()->getManager();

            //dit qu'il faudra enregistrer le User en bdd
            // au prochain appel de la méthode flush()
            $em->persist($user);

            // enregistrement effectif
            $em->flush();
        }

        return $this->render('doctrine/create_user.html.twig');
    }

    /**
     * @Route("/update-user/{id}")
     */
    public function updateUser(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        //objet User dont l'id en bdd est celui reçu dans l'url

        $user = $repository->find($id);

        if($request->isMethod('POST')) {
            $user->setEmail($request->request->get('email'));

            //$emailDuForm = $request->request->get('email');

            $em->persist($user);
            $em->flush();
        }
        dump($user);


        return $this->render(
          'doctrine/update_user.html.twig'
        );
    }

    /**
     * @Route("/delete-user/{id}")
     */
    public function deleteUser(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class);
        //objet User dont l'id en bdd est celui reçu dans l'url
        $user = $repository->find($id);

        // si l'id existe en bdd
        if(!is_null($user)) {
            // suppresioon de luser en bdd
            $em->remove($user);
            $em->flush();

            return new Response('Utilisateur supprimé');
        }else {
            return new Response('Utilisateur inexistant');
        }

    }

    /**
     * paramConverter :
     * le parametre dans l'url s'appelle id comme la clé primaire de la table user
     * En typant user le parametre passé à la méthode, on récupere dans $user
     * un objet User qui est défini à partir d'un SELECT dans la table use sur cet id
     *
     * @Route("/publication/author/{id}")
     */
    public function publicationsByAuthor(User $user)
    {
        dump($user);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Publication::class);

        $publications = $repository->findBy([
           'author' =>$user
        ]);

        return $this->render(
            'doctrine/publications.html.twig',
            [
                'publications' => $publications
            ]
        );
    }

    /**
     * @Route("/user/{id}/publications")
     */
    public function userPublications(User $user)
    {
        /*
         * en appelant de getter de l'attribut publications d'un objet User
         * Doctrine va automatiquement faire une requete en bdd pour y mettre
         * les publications liées au user grace à l'annotation OneToMany
         * sur l'attribut
         */
        return $this->render(
          'doctrine/publications.html.twig',
          [
              'publications' =>$user->getPublications()
          ]
        );

    }

    /**
     * @param Request $request
     * @Route("/create-user-with-publication")
     */
    public function createUserWithPublication(Request $request)
    {
        if($request->isMethod('POST')) {
            $data = $request->request->all();

            $user = new User();
            // et on sette ses attributs  avec les données du formulaire
            $user
                ->setLastname($data['lastname'])
                ->setFirstname($data['firstname'])
                ->setEmail($data['email'])
                // le setter de birthdate attend un objet DateTime
                ->setBirthdate(new \DateTime($data['birthdate']))
            ;

            $publication = new Publication();
            $publication
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ;
            $user->addPublication($publication);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

        }

        return $this->render('doctrine/create_user_with_publication.html.twig');
    }

    /**
     * @Route("/users/team/{id}")
     */
    public function usersByTeam(Team $team)
    {
        return $this->render(
            'doctrine/list_users.html.twig',
            [
                'users' => $team->getUsers()
            ]
        );

    }

    /**
     * @Route("/add-user-to-team/{id}")
     */
    public function addUserToTeam(Request $request, Team $team)
    {
            // entity manager
            $em = $this->getDoctrine()->getManager();
            // UserRepository
            $repository = $em->getRepository(User::class);
            // tous les users de la bdd sous forme d'un tableau d'objets User
            $users = $repository->findAll();

            if($request->isMethod('POST')) {
                //$_POST['user']
                $userId = $request->request->get('user');
                // l'objet User qui a l'id que l'on a reçu du formulaire
                $user = $repository->find($userId);
                // ajout du User à la collection d'objets User de la Team
                $team->getUsers()->add($user);

                // enregistrement de la team en bdd
                // qui va enregister les ids d'user et de team
                // dans la table de relation team_user
                $em->persist($team);
                $em->flush();

            }


            return $this->render(
                'doctrine/add_user_to_team.html.twig',
                [
                    // on passe le tableau d'objets User au template
                    'users' => $users,
                    'team'  => $team
                ]
            );

    }


}







