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
use Symfony\Component\HttpFoundation\Request;

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

        $json = $this->userController->createUser($this->request, "johann", "johann@gmail.com@", "azert");

        $res = json_decode($json->getContent(), true);

        $this->assertEquals("error", $res["code"]);
    }

//    public function testCreateUserWithPost()
//    {
//        $this->request->setMethod(Request::METHOD_POST);
//
//        $json = $this->userController->createUser($this->request, "johann", "johann@gmail.com@", "azert");
//
//        var_dump($json);
//
//        $res = json_decode($json->getContent(), true);
//
//
//    }
}