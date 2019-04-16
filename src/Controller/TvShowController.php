<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-13
 * Time: 18:44
 */

namespace App\Controller;


use App\Entity\TvShow;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class TvShowController extends Controller
{
    /**
     * @Rest\Post("/api/series/all")
     * @param Request $request
     * @return JsonResponse
     */
    public function listAll(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $tvShows = $em->getRepository(TvShow::class)->findTvShowForUser($user);

        return new JsonResponse([
            'code' => 'success',
            'content' => $tvShows
        ]);
    }

    /**
     * @Rest\Get("/api/follow/serie")
     * @param Request $request
     * @return JsonResponse
     */
    public function followTvShow(Request $request)
    {
        $serie = $request->get('serie');
        /** @var User $user */
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $tvShow = $em->getRepository(TvShow::class)->findOneBy(['idApi' => $serie['id']]);

        if(is_null($tvShow)){
            $tvShow = new TvShow();
            $tvShow->setIdApi($serie['id']);
            $tvShow->setNom($serie['name']);
        }

        $tvShow->addUser($user);
        $user->addTvShow($tvShow);

        $em->persist($user);
        $em->persist($tvShow);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'la série à été ajouter à votre compte'
        ]);
    }

    /**
     * @Rest\Get("/api/unfollow/serie")
     * @param Request $request
     * @return JsonResponse
     */
    public function unfollowTvShow(Request $request)
    {
        $serie = $request->get('serie');

        /** @var User $user */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        /** @var TvShow $tvShow */
        $tvShow = $em->getRepository(TvShow::class)->findOneBy(['idApi' => $serie['id']]);

        if(is_null($tvShow)){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'vous n\'avez pas suivie cette série'
            ]);
        }
        $tvShow->removeUser($user);
        $user->removeTvShow($tvShow);
        $em->persist($user);

        if(count($tvShow->getUsers()) == 0){
            $em->remove($tvShow);
        }
        else{
            $em->persist($tvShow);
        }
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'la série à été supprimer de votre compte'
        ]);
    }
}