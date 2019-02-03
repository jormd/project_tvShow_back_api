<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-31
 * Time: 13:45
 */

namespace App\Controller;


use App\Entity\User;
use App\form\type\RegistrationPersoFormType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{


//    /**
//     * @Rest\Post("/api/create/user/{username}/{email}/{plainPassword}")
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function createUser(Request $request, $username, $email, $plainPassword)
//    {
//        if($request->isMethod(Request::METHOD_POST)){
//            $user = new User();
//            $user->setEnabled(true);
//            var_dump($this->container);
//            var_dump($this->container);die();
//
//var_dump($this->createForm(RegistrationPersoFormType::class));die();
//            $form = $this->createForm(RegistrationPersoFormType::class, $user)->handleRequest($request);
//            var_dump($form);
//
//            $res = [
//                'username' => $username,
//                'email' => $email,
//                'plainPassword' => $plainPassword
//            ];
//
//            $form->submit($res);
//
//            if ($form->isValid()) {
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($user);
//                $em->flush();
//
//                return new JsonResponse([
//                    'code' => 'succes',
//                    'message' => 'L\'utilisateur à bien été crée'
//                ]);
//            }
//
//
//            return new JsonResponse([
//                'code' => 'error',
//                'message' => 'Erreur pendant la création'
//            ]);
//        }
//
//        return new JsonResponse([
//            'code' => 'error',
//            'message' => 'Vous n\'avez pas accès à la création d\'utilisateur'
//        ]);
//    }
}