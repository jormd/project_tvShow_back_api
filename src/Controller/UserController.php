<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-31
 * Time: 13:45
 */

namespace App\Controller;


use App\Entity\User;
use App\form\data\UserData;
use App\form\type\RegistrationPersoFormType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{

    /**
     * @Rest\Post("/api/create/user")
     * @param Request $request
     * @return JsonResponse
     */
    public function createUserAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->isMethod(Request::METHOD_POST)){
            $userData = new UserData();

            $form = $this->createForm(RegistrationPersoFormType::class, $userData)->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = new User();

                $user = $userData->extract($user);

                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                ));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return new JsonResponse([
                    'code' => 'success',
                    'message' => 'l\'utilisateur à bien été crée'
                ]);
            }


            return new JsonResponse([
                'code' => 'error',
                'message' => 'Erreur pendant la création'
            ]);
        }

        return new JsonResponse([
            'code' => 'error',
            'message' => 'Vous n\'avez pas accès à la création d\'utilisateur'
        ]);
    }

    public function authAction(Request $request)
    {

    }
}