<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-09
 * Time: 14:09
 */

namespace App\Controller;


use App\Entity\ExternalApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;


class SearchTvShowController extends Controller
{

    /**
     * Méthode permettant d'appelle une api externe pour récupérer des informations sur des séries
     *
     * @param $route
     * @param $data
     * @return JsonResponse
     */
    private function callApi($type, $route, $data = null)
    {
        /** @var ExternalApi $externalAPI */
        $externalAPI = $this->getDoctrine()->getManager()->getRepository(ExternalApi::class)->findAll()[$type];

        $curl = curl_init();

        if(!is_null($data)){
            curl_setopt($curl, CURLOPT_URL, $externalAPI->getUrl().$route."?".$data[0][0]."=".$data[0][1]);
        }
        else{
            if($type == 0){
                curl_setopt($curl, CURLOPT_URL, $externalAPI->getUrl().$route);
            }
            else{
                curl_setopt($curl, CURLOPT_URL, $externalAPI->getUrl().$route.'&api_key='.$externalAPI->getToken());
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * Méthode de recherché des séries par rapport à une recherche
     *
     * @Rest\Post("/api/search")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSpecifyTvShow(Request $request)
    {
        $res = false;

        if(!is_null($request->get('tv'))){
            $res = $this->callApi(0, 'search/shows', [0 => ['q', $request->get('tv')]]);
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    /**
     * Méthode permettant de récupérer les épisodes tendances de la semaine
     *
     * @Rest\Get("/api/schedule")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSchedule(Request $request)
    {
        $res = $this->callApi(0, 'schedule/full');


        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    /**
     * Recherche de la série par son id
     * @param Request $request
     * @return JsonResponse
     */
    public function searchTvShowById($tv = null)
    {
        $res = false;

        if(!is_null($tv)){
            $res = $this->callApi(0, 'shows/'.$tv);
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    /**
     * Recherche des saisons d'une série par son id
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSeasonTvShowById($tv = null)
    {
        $res = false;

        if(!is_null($tv)){
            $res = $this->callApi(0, 'shows/'.$tv.'/seasons');
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    /**
     * Recherche des épisodes par saison
     * @param Request $request
     * @return JsonResponse
     */
    public function searchEpisodeBySeason($tv = null)
    {
        $res = false;

        if(!is_null($tv)){
            $res = $this->callApi(0, 'seasons/'.$tv.'/episodes');
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    /**
     * info épisode
     * @param Request $request
     * @return JsonResponse
     */
    public function infoEpisode($serie = null, $saison = null, $episode = null)
    {
        $res = false;

        if(!is_null($serie) && !is_null($saison) && !is_null($episode)){
            $res = $this->callApi(0, 'shows/'.$serie.'/episodebynumber?season='.$saison.'&number='.$episode);
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    public function nextEpisode($tvshow = null)
    {
        $res = false;

        if(!is_null($tvshow)){
            $res = $this->callApi(0, 'shows/'.$tvshow.'?embed=nextepisode');
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

    public function searchEpisodeGenre($genres = null)
    {
        $res = false;

        if(!is_null($genres)){
            $res = $this->callApi(1, 'discover/tv?with_genres='.implode(',', $genres));
        }

        if(is_bool($res) && !$res){
            return new JsonResponse([
                'code' => 'error'
            ]);
        }

        return new JsonResponse([
            'code' => 'success',
            'content' => $res
        ]);
    }

}