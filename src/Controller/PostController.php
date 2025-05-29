<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/posts', methods:['GET'], name: 'app_post_index')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/posts/user/{id<\d+>}', methods:['GET'], name: 'app_post_user')]
    public function user(int $id): Response
    {
        return new Response(
            '<h1>List of post from specific user <br>'.
            'User id:'.$id.'<br>'.
            'Named route that we will use in the view:'.
            $this->generateUrl('app_post_user', ['id'=> $id]).
            '</h1>'
        );
    }
}
