<?php

namespace App\Controller;

use App\Form\ChangePasswordForm;
use App\Form\DeleteAccountForm;
use App\Form\UserTypeForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show')]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $userRepository->find($id);
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getId() !== $id) {
            return $this->redirectToRoute('app_user_edit', ['id'=> $this->getUser()->getId()]);
        }
        $user = $this->getUser();

        // change user information
        $userForm  = $this->createForm(UserTypeForm::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $user = $userForm->getData();
            // $entityManager->persist($user);
            // $entityManager->flush();
            $this->addFlash(
                'status-profile-information',
                'user-updated'
            );
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        //change password
        $passwordForm = $this->createForm(ChangePasswordForm::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $user = $passwordForm->getData();
            // $entityManager->persist($user);
            // $entityManager->flush();
            $this->addFlash(
                'status-password',
                'password-changed'
            );
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        //delete user account
        $deleteAccountForm = $this->createForm(DeleteAccountForm::class, $user);
        $deleteAccountForm->handleRequest($request);
        
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            $user = $deleteAccountForm->getData();
            // $security->logout(false);
            // $entityManager->remove($user);
            // $entityManager->flush();
            // $request->getSession()->invalidate();
            return $this->redirectToRoute('posts.index');
        }


        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm,
            'passwordForm' => $passwordForm,
            'deleteAccountForm' => $deleteAccountForm
        ]);
    }

    #[Route('/users', name: 'app_user_all')]
    public function displayAll(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('user/all.html.twig');
    }
}
