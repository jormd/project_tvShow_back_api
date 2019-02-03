<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-31
 * Time: 15:59
 */

namespace App\Tests;


use App\Controller\UserController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserTest extends TestCase
{

    /** @var UserController */
    private $userController;

    /** @var Request */
    private $request;

    protected function setUp()
    {
        $this->userController = new UserController();
        $this->request = new Request();
    }

    /**
     * Test de la création d'un utilisateur en get qui doit être refuser
     */
    public function testCreateUserWithGet()
    {
        $this->request->setMethod(Request::METHOD_GET);

        $json = $this->userController->createUserAction($this->request, new UserPasswordEncoder(new EncoderFactory([])));

        $res = json_decode($json->getContent(), true);

        $this->assertEquals("error", $res["code"]);
    }

    public function testCreateUserWithPost()
    {
        $mock = $this->createMock(UserController::class);
        $mock->method('createUserAction')
            ->willReturn(['code' => 'success']);

        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'test@gmail.com',
            'name' => 'test',
            'lastname' => 'test',
            'password' => "test"
        ];

        $this->request->request->add($params);

        $res = $mock->createUserAction($this->request, new UserPasswordEncoder(new EncoderFactory([])));

        $this->assertEquals("success", $res['code']);
    }
}