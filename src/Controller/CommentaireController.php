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
use App\Entity\User;
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
        $commentaireReq = $request->get('commentaire');

        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $hasSerie = $user->getEpisodes()->filter(function ($show) use ($commentaireReq){
            return $show->getIdEpisodeApi() == $commentaireReq['idEpisodeApi'];
        });

        // check si l'utilisateur à vu l'épisode
        if(count($hasSerie) == 0){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'Vous ne pouvez pas mettre de commentaire si vous n\'avez pas vu l\'épisode'
            ]);
        }

        $commentaire = new Commentaire();
        $commentaire->setEpisode($em->getRepository(Episode::class)->findOneBy(['idEpisodeApi' => $commentaireReq['idEpisodeApi']]));
        $commentaire->setMessage($commentaireReq['commentaire']);
        $commentaire->setUser($this->getUser());
        $this->getUser()->addCommentaire($commentaire);

        $em->persist($this->getUser());
        $em->persist($commentaire);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'Votre commentaire à été ajouté'
        ]);
    }

    /**
     * @Rest\Post("/api/commentaire/episode/remove")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeCommentaireEpisode(Request $request)
    {
        $commentaireReq = $request->get('commentaire');

        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $commentaire = $user->getCommentaires()->filter(function ($show) use ($commentaireReq){
            return $show->getId() == $commentaireReq['id'];
        });

        if(count($commentaire) == 0){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'Vous ne pouvez pas supprimer de commentaire si vous n\'avez pas crée'
            ]);
        }

        /** @var Commentaire $commentaire */
        $commentaire = $commentaire->first();

        $em->remove($commentaire);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'Votre commentaire à été supprimer'
        ]);
    }

}