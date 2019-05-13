<?php


namespace App\Controller;


use App\Entity\Friends;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class FriendController extends Controller
{

    /**
     * @Rest\Post("api/serach/people")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchFriend(Request $request)
    {
        $user = $request->request->get("friend");

        if(!is_null($user)){
            $em = $this->getDoctrine()->getManager();

            $peoples = $em->getRepository(User::class)->findPeople($user);

            $res = [];

            /** @var User $people */
            foreach ($peoples as $people){
                $res[$people->getId()]['name'] = $people->getName();
                $res[$people->getId()]['id'] = $people->getId();
            }

            return new JsonResponse([
                'code' => 'success',
                'content' => $res
            ]);
        }
        return new JsonResponse([
            'code' => 'error',
        ]);
    }

    /**
     * @Rest\Post("api/add/friend")
     * @param Request $request
     * @return JsonResponse
     */
    public function addFriend(Request $request)
    {
        $user = $request->request->get("friend");

        if(!is_null($user)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($user);

            $friend = new Friends();
            $friend->setUser($this->getUser());
            $friend->setFriend($user);
            $this->getUser()->addHasFriends($friend);
            $user->addFriends($friend);

            $em->persist($friend);
            $em->persist($this->getUser());
            $em->persist($user);
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

    /**
     * @Rest\Post("/api/remove/friend")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFriend(Request $request)
    {
        $user = $request->request->get("friend");

        if(!is_null($user)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($user);

            $friend = $em->getRepository(Friends::class)->findBy(['user' => $this->getUser()->getId(), 'friend' => $user->getId()]);
            if(!is_null($friend) && count($friend) > 0){

                $user->removeFriends($friend[0]);
                $this->getUser()->removeHasFriends($friend[0]);

                $em->persist($this->getUser());
                $em->persist($user);
                $em->remove($friend[0]);
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