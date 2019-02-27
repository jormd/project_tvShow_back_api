<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-27
 * Time: 09:06
 */

namespace App\Controller;


use App\Entity\Commentaire;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class CommentaireController extends Controller
{

    /**
     * @Rest\Post("/api/commentaire/episode/add")
     * @param Request $request
     * @return JsonResponse
     */
    public function addCommentaireEpisode(Request $request)
    {
        $commentaireReq = $request->get('episode');

        $em = $this->getDoctrine()->getManager();

        // check si l'utilisateur à vu l'épisode
        if(is_null($em->getRepository(Episode::class)->findBy(['idEpisodeApi' => $commentaireReq['episode'], 'users' => $this->getUser()]))){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'Vous ne pouvez pas mettre de commentaire si vous n\'avez pas vu l\'épisode'
            ]);
        }

        $commentaire = new Commentaire();
        $commentaire->setEpisode($commentaireReq['episode']);
        $commentaire->setMessage($commentaireReq['commentaire']);
        $commentaire->setUser($this->getUser());

        $em->persist($commentaire);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'Votre commentaire à été ajouté'
        ]);
    }

}