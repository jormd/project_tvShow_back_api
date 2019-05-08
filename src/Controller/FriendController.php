<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FriendController extends Controller
{
    public function addFriend(Request $request)
    {
        $user = $request->request->get("friend");

        if(!is_null($user)){
            $this->getUser()->addFriends($user);

            $em = $this->getDoctrine()->getManager();

            $em->persist($this->getUser());
            $em->flush();

            return new JsonResponse([
                'code' => 'success',
                'content' => "utilisateur ajouté comme amie"
            ]);
        }

        return new JsonResponse([
            'code' => 'error',
            'content' => "utilisateur n'a pas été ajouté comme amie"
        ]);
    }

    public function removeFriend(Request $request)
    {
        $user = $request->request->get("friend");

        if(!is_null($user)){
            if(in_array($user, $this->getUser()->getFriends()->toArray())){
                $this->getUser()->removeFriends($user);

                $em = $this->getDoctrine()->getManager();

                $em->persist($this->getUser());
                $em->flush();

                return new JsonResponse([
                    'code' => 'success',
                    'content' => "utilisateur à bien été retiré comme amie"
                ]);
            }
            return new JsonResponse([
                'code' => 'error',
                'content' => "vous n'est pas amie avec cette personne"
            ]);
        }

        return new JsonResponse([
            'code' => 'error',
            'content' => "utilisateur n'a pas été ajouté comme amie"
        ]);
    }
}