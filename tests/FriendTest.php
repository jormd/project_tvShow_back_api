<?php


namespace App\Tests;

use App\Controller\FriendController;
use App\Controller\UserController;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class FriendTest extends WebTestCase
{
    /** @var UserController */
    private $userController;

    /** @var Request */
    private $request;

    private $encoder;

    /** @var EntityManager */
    private $entityManager;

    /** @var FriendController */
    private $friendController;

    protected $container2;

    protected function setUp()
    {
        $client = self::createClient();

        $this->container2 = $client->getContainer();
        $this->userController = new UserController();
        $this->friendController = new FriendController();
        $this->request = new Request();



        $this->encoder = $this->createMock(UserPasswordEncoder::class);
        $this->encoder->method('encodePassword')->willReturn('test');

        $user = new User();
        $user->setEmail('test@email.com');
        $user->setName('test');
        $user->setLastname('aaa');
        $user->setPassword('test');
        $user->setCoGoogle(false);


        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $this->container2->get('security.token_storage')->setToken($mockUser);

        $this->userController->setContainer($this->container2);
        $this->friendController->setContainer($this->container2);

        // Run the schema update tool using our entity metadata
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadatas);
    }

    public function CreateUserWithPost()
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

        $this->assertEquals("success", $res['code']);
        $this->assertEquals("l'utilisateur à bien été crée", $res['message']);
    }

    public function testAddFriend()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'tezaest@gmail.com',
            'name' => 'teszaet',
            'lastname' => 'teazest',
            'password' => '1aE#kajddssd'
        ];

        $this->request->request->add($params);

        $this->userController->createUserAction($this->request, $this->encoder);

        $this->assertEquals(1, count($this->entityManager->getRepository(User::class)->findAll()));

        $this->CreateUserWithPost();

        $this->assertEquals(2, count($this->entityManager->getRepository(User::class)->findAll()));

        $request = new Request();
        $request->request->add(['friend' => 2]);

        $json = $this->friendController->addFriend($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

    }

    public function testAddFriend2()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'tezaest@gmail.com',
            'name' => 'teszaet',
            'lastname' => 'teazest',
            'password' => '1aE#kajddssd'
        ];

        $this->request->request->add($params);

        $this->userController->createUserAction($this->request, $this->encoder);

        $params['registration_perso_form'] = [
            'email' => 'claude@gmail.com',
            'name' => 'claude',
            'lastname' => 'teazest',
            'password' => '1aE#kajddssd'
        ];
        $this->request->request->add($params);

        $this->userController->createUserAction($this->request, $this->encoder);

        $request = new Request();
        $request->request->add(['friend' => 1]);

        $json = $this->friendController->addFriend($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $request = new Request();
        $request->request->add(['friend' => 2]);

        $json = $this->friendController->addFriend($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }

    public function testRemoveError()
    {

        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'tezaezaest@gmail.com',
            'name' => 'teszaezaet',
            'lastname' => 'teazaezest',
            'password' => '1aE#kajddssd'
        ];

        $this->request->request->add($params);

        $this->userController->createUserAction($this->request, $this->encoder);

        $request = new Request();
        $request->request->add(['friend' => 1]);

        $json = $this->friendController->removeFriend($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testRemoveSuccess()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $params['registration_perso_form'] = [
            'email' => 'tezaest@gmail.com',
            'name' => 'teszaet',
            'lastname' => 'teazest',
            'password' => '1aE#kajddssd'
        ];

        $this->request->request->add($params);

        $this->userController->createUserAction($this->request, $this->encoder);

        $request = new Request();
        $request->request->add(['friend' => 1]);
        $json = $this->friendController->addFriend($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        
        $json = $this->friendController->removeFriend($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }
}