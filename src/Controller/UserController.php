<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show')]
    public function show(int $id): Response
    {
        return $this->render('user/show.html.twig', [
            'id' => $id,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function edit(int $id): Response
    {
        return $this->render('user/edit.html.twig', [
            'id' => $id,
        ]);
    }

    #[Route('/users', name: 'app_user_all')]
    public function displayAll(): Response
    {
        return $this->render('user/all.html.twig');
    }
}
