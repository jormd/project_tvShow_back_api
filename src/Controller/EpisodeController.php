<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-25
 * Time: 15:28
 */

namespace App\Controller;


use App\Entity\Episode;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;


class EpisodeController extends Controller
{

    /**
     * @Rest\Post("/api/check/episode")
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEpisode(Request $request)
    {
        $episodeReq = $request->get('episode');

        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $hasSerie = $user->getTvShows()->filter(function ($show) use ($episodeReq){
            return $show->getIdApi() == $episodeReq['idSerie'];
        });

        if(count($hasSerie) == 0){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'vous ne suivez pas cette série, si vous voulez suivre cette série veuillez suivre la série'
            ]);
        }

        $episode = $em->getRepository(Episode::class)->findOneBy([
            'idEpisodeApi' => $episodeReq['idEpisode'],
            'idSerieApi' => $episodeReq['idSerie']
            ]
        );

        if(is_null($episode)){
            $episode = new Episode($episodeReq['idEpisode'], $episodeReq['idSerie']);
        }

        $episode->addUser($this->getUser());

        $em->persist($episode);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'l\'épisode à été ajouter à votre compte'
        ]);
    }

    public function unCheckEpisode(Request $request)
    {
        $episodeReq = $request->get('episode');

        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $hasSerie = $user->getTvShows()->filter(function ($show) use ($episodeReq){
            return $show->getIdApi() == $episodeReq['idSerie'];
        });

        if(count($hasSerie) == 0){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'vous ne suivez pas cette série, si vous voulez suivre cette série veuillez suivre la série'
            ]);
        }

        /** @var Episode $episode */
        $episode = $em->getRepository(Episode::class)->findOneBy([
            'idEpisodeApi' => $episodeReq['idEpisode'],
            'idSerieApi' => $episodeReq['idSerie']
            ]);

        if(is_null($episode)){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'L\'épisode n\'existe pas'
            ]);
        }

        $episode->removeUser($this->getUser());

        if(count($episode->getUsers()) == 0){
            $em->remove($episode);
        }
        else{
            $em->persist($episode);
        }

        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'l\'épisode à été retirer de votre compte'
        ]);
    }
}