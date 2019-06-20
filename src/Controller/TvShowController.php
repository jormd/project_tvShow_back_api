<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-13
 * Time: 18:44
 */

namespace App\Controller;


use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\TvShow;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class TvShowController extends Controller
{
    /**
     * @Rest\Get("/api/series/all")
     * @param Request $request
     * @return JsonResponse
     */
    public function listAll(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $tvShows = $em->getRepository(TvShow::class)->findTvShowForUser($user);

        $returnTvShow = [];
        foreach ($tvShows as $tvShow){
            $res = $this->forward('App\Controller\SearchTvShowController::searchTvShowById', ['tv' => $tvShow['idApi']]);

            $res = json_decode(json_decode($res->getContent(), true)['content'], true);

            $returnTvShow[$res['id']]["id"] = $res["id"];
            $returnTvShow[$res['id']]["name"] = $res["name"];
            $returnTvShow[$res['id']]["img"] = $res["image"]["original"];
            $returnTvShow[$res['id']]["summary"] = $res["summary"];
        }
        
        return new JsonResponse([
            'code' => 'success',
            'content' => $returnTvShow
        ]);
    }

    /**
     * @Rest\Post("/api/find/serie")
     * @param Request $request
     * @return JsonResponse
     */
    public function listSpecificTvShow(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();


        $res = $this->forward('App\Controller\SearchTvShowController::searchTvShowById', ['tv' => $request->get('idApi')]);
        $res = json_decode(json_decode($res->getContent(), true)['content'], true);
        $returnTvShow[$res['id']]["name"] = $res["name"];
        $returnTvShow[$res['id']]["id"] = $res["id"];
        $returnTvShow[$res['id']]["img"] = $res["image"]["original"];
        $returnTvShow[$res['id']]["summary"] = $res["summary"];
        $returnTvShow[$res['id']]["create"] = $res["premiered"];
        $returnTvShow[$res['id']]["status"] = $res["status"];

        $seasons = $this->forward('App\Controller\SearchTvShowController::searchSeasonTvShowById', ['tv' => $request->get('idApi')]);
        $seasons = json_decode(json_decode($seasons->getContent(), true)['content'], true);
        $returnTvShow[$res['id']]['season']= [];
        foreach ($seasons as $season){
            $returnTvShow[$res['id']]['season'][$season['id']]['episodeNumber'] = $season['episodeOrder'];

            $episodes = $this->forward('App\Controller\SearchTvShowController::searchEpisodeBySeason', ['tv' => $season['id']]);
            $episodes = json_decode(json_decode($episodes->getContent(), true)['content'], true);
            $returnTvShow[$res['id']]['season'][$season['id']]['episodes'] = [];
            foreach ($episodes as $episode){

                $see = $em->getRepository(Episode::class)->findEpisodeSee($user, $res['id'], $episode['id']);

                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['id'] = $episode['id'];
                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['name'] = $episode['name'];
                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['date'] = $episode['airdate'];
                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['summary'] = $episode['summary'];
                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['number'] = $episode['number'];
                $returnTvShow[$res['id']]['season'][$season['id']]['episodes'][$episode['id']]['see'] = count($see) > 0;
            }
        }

        $userFollowTvShow = $em->getRepository(TvShow::class)->findTvShowByUserAndId($user, $request->get('idApi'));

        $returnTvShow[$res['id']]["follow"] = count($userFollowTvShow) > 0;

        return new JsonResponse([
            'code' => 'success',
            'content' => $returnTvShow
        ]);
    }

    /**
     * @Rest\Post("/api/follow/serie")
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

            //blocage pour les tests car mcok impossible ou super compliqué à mettre en place
            if(!isset($serie['test'])){
                $this->searchAndAddGenreTvShow($serie, $tvShow);
            }
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
     * @Rest\Post("/api/unfollow/serie")
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

    public function searchAndAddGenreTvShow($serie, $tvShow)
    {
        $em = $this->getDoctrine()->getManager();
        //ajout genres
        $res = $this->forward('App\Controller\SearchTvShowController::searchTvShowById', ['tv' => $serie['id']]);
        $res = json_decode(json_decode($res->getContent(), true)['content'], true);

        foreach ($res['genres'] as $genre){
            $entityGenre = $em->getRepository(Genre::class)->findBy(['name' => $genre]);

            if(count($entityGenre) == 0){
                $entityGenre = new Genre();
                $entityGenre->setName($genre);
            }
            else{
                $entityGenre = $entityGenre[0];
            }

            $entityGenre->addTvShow($tvShow);
            $tvShow->addGenre($entityGenre);
            $em->persist($entityGenre);
        }
    }

    /**
     * @Rest\Post("/api/searchbygenre")
     * @return JsonResponse
     */
    public function searchTvShowByGenreUser()
    {
        /** @var User $user */
        $user = $this->getUser();

        $genresObj = $user->getGenres();

        $genres = [];

        /** @var Genre $genreObj */
        foreach ($genresObj as $genreObj){
            if($genreObj->getIdApi() != 0){
                $genres[] = $genreObj->getIdApi();
            }
        }

        //ajout genres
        $res = $this->forward('App\Controller\SearchTvShowController::searchEpisodeGenre', ['genres' => $genres]);

        $res = json_decode(json_decode($res->getContent(), true)['content'], true);

        return new JsonResponse([
            'code' => 'success',
            'content' => $res['results']
        ]);
    }
}