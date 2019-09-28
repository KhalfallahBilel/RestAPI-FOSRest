<?php
namespace  App\Controller;

use FOS\UserBundle\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {


    /**
     * @Route("/",name="home.index")
     * @return Response
     */
    public function index():Response{
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $this->render('base.html.twig',compact('user'));
    }
}