<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-30
 * Time: 15:15
 */

namespace App\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;

class testController
{
    /**
     * @Rest\Get("/test")
     */
    public function testApi()
    {
        return new JsonResponse([
            'code' => 200,
            "content" => "hourra"
        ]);
    }
}