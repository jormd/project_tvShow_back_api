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
        $commentaireReq = $request->get('episode');

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

    public function removeCommentaireEpisode(Request $request)
    {
        $commentaireReq = $request->get('episode');

        $em = $this->getDoctrine()->getManager();

        $commentaire = $em->getRepository(Commentaire::class)->findBy(['id' => $commentaireReq['id'], 'users' => $this->getUser()]);

        if(is_null($commentaire)){
            return new JsonResponse([
                'code' => 'error',
                'content' => 'Vous ne pouvez pas mettre de commentaire si vous n\'avez pas vu l\'épisode'
            ]);
        }

        $this->getUser()->removeCommentaire($commentaire);

        $em->persist($this->getUser());
        $em->remove($commentaire);
        $em->flush();

        return new JsonResponse([
            'code' => 'success',
            'content' => 'Votre commentaire à été supprimer'
        ]);
    }

}