<?php


namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Genre;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GenreController extends Controller
{

    /**
     * Méthode permettant de rajouter un genre
     * @Rest\Post("/api/add/genre")
     * @param Request $request
     * @return JsonResponse
     */
    public function addGenre(Request $request)
    {
        $genres = $request->request->get("genre");

        if(!is_null($genres)){
            $genres = explode(',', $genres);
            $em = $this->getDoctrine()->getManager();

            /** @var Genre $genre */
            foreach ($this->getUser()->getGenres() as $genre){
                $this->getUser()->removeGenre($genre);
                $genre->removeUser($this->getUser());
                $em->persist($genre);
            }
            $em->persist($this->getUser());
            $em->flush();

            foreach ($genres as $genre){
                $entityGenre = $em->getRepository(Genre::class)->findBy(['name' => $genre]);

                if(count($entityGenre) == 0){
                    $entityGenre = new Genre();
                    $entityGenre->setName($genre);
                    $entityGenre->setIdApi(0);
                    $em->persist($entityGenre);
                }
                else{
                    $entityGenre = $entityGenre[0];
                    if(is_null($entityGenre->getIdApi())){
                        $entityGenre->setIdApi(0);
                    }
                }

                /** @var User $user */
                $user = $this->getUser();

                if(!in_array($entityGenre, $user->getGenres()->toArray())){
                    $user->addGenre($entityGenre);
                    $entityGenre->addUser($user);
                    $em->persist($user);
                    $em->flush();
                }
            }
            return new JsonResponse([
                'code' => 'success',
                'content' => "Le genre à été rajouter à votre compte"
            ]);
        }

        return new JsonResponse([
            'code' => 'error'
        ]);
    }

    /**
     * Méthode permettant de supprimer un genre
     * @Rest\Post("/api/remove/genre")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeGenre(Request $request)
    {
        $genre = $request->request->get("genre");

        if(!is_null($genre)){
            $em = $this->getDoctrine()->getManager();

            $entityGenre = $em->getRepository(Genre::class)->findBy(['name' => $genre]);
            $user = $this->getUser();

            if(count($entityGenre) == 0){
                return new JsonResponse([
                    'code' => 'error',
                    'content' => "Le genre n'existe pas"
                ]);
            }
            else{
                /** @var Genre $entityGenre */
                $entityGenre = $entityGenre[0];
            }

            /** @var User $user */

            if(!in_array($entityGenre->getId(), $user->getGenres()->map(function ($genre){ return $genre->getId();})->toArray())){
                return new JsonResponse([
                    'code' => 'error',
                    'content' => "Vous n'avez pas le genre"
                ]);
            }

            $entityGenre->removeUser($user);
            $user->removeGenre($entityGenre);
            $em->persist($user);

            if(count($entityGenre->getUsers()) == 0){
                $em->remove($entityGenre);
            }
            else{
                $em->persist($entityGenre);
            }

            $em->flush();

            return new JsonResponse([
                'code' => 'success',
                'content' => "Le genre a supprimer de votre compte"
            ]);
        }

        return new JsonResponse([
            'code' => 'error'
        ]);
    }
}