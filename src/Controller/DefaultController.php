<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-03-27
 * Time: 10:29
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render("default/index.html.twig");
    }
}