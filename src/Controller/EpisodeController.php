<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-25
 * Time: 15:28
 */

namespace App\Controller;


use App\Entity\Commentaire;
use App\Entity\Episode;
use App\Entity\TvShow;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;


class EpisodeController extends Controller
{

    /**
     * @Rest\Post("/api/info/episode")
     * @param Request $request
     * @return JsonResponse
     */
    public function infoEpisode(Request $request)
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

        $res = $this->forward('App\Controller\SearchTvShowController::infoEpisode', ['serie' => $episodeReq['idSerie'], 'saison' => $episodeReq['saison'], 'episode' => $episodeReq['episode']]);
        $res = json_decode(json_decode($res->getContent(), true)['content'], true);

        $resultat['idEpisode'] = $res['id'];
        $resultat['name'] = $res['name'];
        $resultat['summary'] = $res['summary'];

        $commentaires = $em->getRepository(Commentaire::class)->findCommentaire($res['id']);

        $resultat['commentaire'] = [];
        $index = 1;
        /** @var Commentaire $commentaire */
        foreach ($commentaires as $commentaire)
        {
            $resultat['commentaire'][$index]['message'] = $commentaire->getMessage();
            $resultat['commentaire'][$index]['author'] = $commentaire->getUser()->getName();
            $index++;
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $resultat
        ]);
    }

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
        $user->addEpisode($episode);

        $em->persist($user);
        $em->persist($episode);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'l\'épisode à été ajouter à votre compte'
        ]);
    }

    /**
     * @Rest\Post("/api/uncheck/episode")
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @Rest\Post("/api/next/episode")
     * @param Request $request
     * @return JsonResponse
     */
    public function nextAllEpisodes(Request $request)
    {
        $tvShows = $this->getDoctrine()->getRepository(TvShow::class)->findTvShowForUser($this->getUser());
        $resultat = [];

        foreach ($tvShows as $tvShow){
            $res = $this->forward('App\Controller\SearchTvShowController::nextEpisode', ['tvshow' => $tvShow['idApi']]);
            $res = json_decode(json_decode($res->getContent(), true)['content'], true);

            if(isset($res["_embedded"])){
                $next = $res["_embedded"]['nextepisode'];

                $resultat[$tvShow->getId()]['idEpisode'] = $next['id'];
                $resultat[$tvShow->getId()]['name'] = $next['name'];
                $resultat[$tvShow->getId()]['summary'] = $next['summary'];
                $resultat[$tvShow->getId()]['date'] = $next['airdate'];
                $resultat[$tvShow->getId()]['season'] = $next['season'];
                $resultat[$tvShow->getId()]['episode'] = $next['number'];
            }
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $resultat
        ]);
    }
}