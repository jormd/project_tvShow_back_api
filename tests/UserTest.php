<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-01-31
 * Time: 15:59
 */

namespace App\Tests;


use App\Controller\UserController;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserTest extends WebTestCase
{

    /** @var UserController */
    private $userController;

    /** @var Request */
    private $request;

    private $client;

    private $encoder;

    /** @var EntityManager */
    private $entityManager;

    protected function setUp()
    {
        $this->client = self::createClient();
        $this->userController = new UserController();
        $this->request = new Request();
        $this->userController->setContainer($this->client->getContainer());
        $this->encoder = $this->createMock(UserPasswordEncoder::class);
        $this->encoder->method('encodePassword')->willReturn('test');


        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadatas);
    }

    /**
     * Test de la création d'un utilisateur en get qui doit être refuser
     */
    public function testCreateUserWithGet()
    {
        $this->request->setMethod(Request::METHOD_GET);

        $json = $this->userController->createUserAction($this->request, $this->encoder);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals("error", $res["code"]);
        $this->assertEquals("Vous n'avez pas accès à la création d'utilisateur", $res["message"]);
    }

    /**
     * Test de la création user qui contient bien tout les paramètre
     */
    public function testCreateUserWithPost()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'test@gmail.com',
            'name' => 'test',
            'lastname' => 'test',
            'password' => '1aE#kajddssd'
        ];

        $this->request->request->add($params);

        $json = $this->userController->createUserAction($this->request, $this->encoder);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals(1, count($this->entityManager->getRepository(User::class)->findAll()));

        $this->assertEquals("success", $res['code']);
        $this->assertEquals("l'utilisateur à bien été crée", $res['message']);
    }

    public function testCreateUserWithPostAndError()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'test@gmail.com',
            'name' => 'test',
            'lastname' => 'test',
            'password' => 'test'
        ];

        $this->request->request->add($params);

        $mock = $this->createMock(UserPasswordEncoder::class);
        $mock->method('encodePassword')->willReturn('test');


        $json = $this->userController->createUserAction($this->request, $this->encoder);

        $res = json_decode($json->getContent(), true);


        $this->assertEquals("error", $res['code']);
        $this->assertEquals("MDP pas sécuriser", $res['message']);
    }
//
//    public function testGetTokenUser()
//    {
//        $user = new User();
//        $user->setName('aaa');
//        $user->setLastname('aaa');
//        $user->setEmail('aaa@zae.de');
//        $user->setPassword('zezaeesqdaaa');
//
//        $res = $this->userController->getTokenUser($user);
//
//        $this->assertIsString($res);
//        $this->assertTrue(count($res) > 0);
//    }
}