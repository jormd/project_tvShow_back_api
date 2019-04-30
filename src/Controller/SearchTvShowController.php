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
    private function callApi($route, $data = null)
    {
        /** @var ExternalApi $externalAPI */
        $externalAPI = $this->getDoctrine()->getManager()->getRepository(ExternalApi::class)->findAll()[0];

        $curl = curl_init();

        if(!is_null($data)){
            curl_setopt($curl, CURLOPT_URL, $externalAPI->getUrl().$route."?".$data[0][0]."=".$data[0][1]);
        }
        else{
            curl_setopt($curl, CURLOPT_URL, $externalAPI->getUrl().$route);

        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * Méthode de recherché des séries par rapport à une recherche
     *
     * @Rest\Get("/api/search")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchSpecifyTvShow(Request $request)
    {
        $res = false;

        if(!is_null($request->get('tv'))){
            $res = $this->callApi('search/shows', [0 => ['q', $request->get('tv')]]);
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
        $res = $this->callApi('schedule/full');


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
     * @param Request $request
     * @return JsonResponse
     */
    public function searchTvShowById($tv = null)
    {
        $res = false;

        if(!is_null($tv)){
            $res = $this->callApi('shows/'.$tv);
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