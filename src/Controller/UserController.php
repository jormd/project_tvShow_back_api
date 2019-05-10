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
use App\Security\LoginFormAuthentificatorAuthenticator;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
                $user->setCoGoogle(false);

                $user = $userData->extract($user);

                if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$^=!*()@%&]).{6,}$/', $form->get('password')->getData())){
                    return new JsonResponse([
                        'code' => 'error',
                        'message' => 'MDP pas sécuriser'
                    ]);
                }

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

    /**
     * @Rest\Post("/api/login")
     * @param Request $request
     * https://symfony.com/doc/current/security/guard_authentication.html
     */
    public function authAction(Request $request, LoginFormAuthentificatorAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->isMethod(Request::METHOD_POST)) {

            /** @var User $user */
            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);

            if(!is_null($user)){
                if($passwordEncoder->isPasswordValid($user, $request->request->get('password'))){

                    $request->request->add(['tokenJWT' => $this->getTokenUser($user) ]);

                    return $guardHandler->authenticateUserAndHandleSuccess(
                        $user,          // the User object you just created
                        $request,
                        $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                        'main'          // the name of your firewall in security.yaml
                    );
                }
            }

            return new JsonResponse([
                'code' => 'error',
                'message' => 'Erreur connexion, le mdp est incorret ou l\'utilisateur n\'existe pas'
            ]);
        }

        return new JsonResponse([
            'code' => 'error',
            'message' => 'Vous ne pouvez pas vous connecté'
        ]);
    }

    /**
     * @Rest\Post("/api/logingoogle")
     * @param Request $request
     * @return JsonResponse
     */
    public function loginGoogleAction(Request $request){
        if ($request->isMethod(Request::METHOD_POST)) {
            $token = $request->request->all()['token'];


            if($token){
                $client = new \Google_Client();
                $check = $client->verifyIdToken($token);

                if($check){
                    $email = $check['email'];
                    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

                    if (is_null($user) || !$user) {
                        $user = new User();
                        $user->setEmail($email);
                        $user->setLastname($check['family_name']);
                        $user->setName($check['given_name']);
                        $user->setCoGoogle(true);

                        $this->getDoctrine()->getManager()->persist($user);
                        $this->getDoctrine()->getManager()->flush();
                    }

                    return new JsonResponse([
                        'code' => 'succes',
                        'tokenJWT' => $this->getTokenUser($user)
                    ]);
                }


            }

            return new JsonResponse([
                'code' => 'error',
                'message' => 'Erreur connexion'
            ]);
        }

        return new JsonResponse([
            'code' => 'error',
            'message' => 'Vous ne pouvez pas vous connecté'
        ]);
    }

    public function getTokenUser(User $user)
    {
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');


        return $jwtManager->create($user);
    }

    /**
     * @Rest\Post("/api/profile")
     * @param Request $request
     * @return JsonResponse
     */
    public function profileUser(Request $request)
    {
        $user = $request->request->get("user");

        if(!is_null($user)){
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->find($user);

            $res['name'] = $user->getName();
            $res['id'] = $user->getId();

            if($this->getUser()->getId() != $user->getId()){
                $res['amis'] = in_array($user->getId(), $this->getUser()->getFriends()) ? true: false;
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
}