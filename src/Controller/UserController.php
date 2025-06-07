<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordForm;
use App\Form\DeleteAccountForm;
use App\Form\UserTypeForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \Doctrine\Persistence\ManagerRegistry;

final class UserController extends AbstractController
{
    #[Route('/user/{uuid}', name: 'app_user_show')]
    public function show(string $uuid, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $userRepository->findOneBy(['uuid' => $uuid]);
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{uuid}/edit', name: 'app_user_edit')]
    public function edit(string $uuid, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser()->getUuid() !== $uuid) {
            return $this->redirectToRoute('app_user_edit', ['uuid'=> $this->getUser()->getUuid()]);
        }
        $user = $this->getUser();

        // change user information
        $userForm  = $this->createForm(UserTypeForm::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            //$user = $userForm->getData();
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'status-profile-information',
                'user-updated'
            );
            return $this->redirectToRoute('app_user_show', ['uuid' => $user->getUuid()]);
        }

        //change password
        $passwordForm = $this->createForm(ChangePasswordForm::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
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
            $security->logout(false);
            $entityManager->remove($user);
            $entityManager->flush();
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_main_homepage');
        }


        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm,
            'passwordForm' => $passwordForm,
            'deleteAccountForm' => $deleteAccountForm
        ]);
    }

    #[Route('/users', name: 'app_user_all')]
    public function displayAll(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $users = $doctrine->getRepository(User::class)->findBy([], ['logged_at' => 'DESC']);
        $form = $this->createFormBuilder($users)
            ->add('Block', SubmitType::class, ['label' => false, 'label_html' => true,])
            ->add('Unblock', SubmitType::class, ['label' => false, 'label_html' => true,])
            ->add('Delete', SubmitType::class, ['label' => false, 'label_html' => true,])
            ->add('selectAll', CheckboxType::class, ['label' => 'Select All Form',
            'required' => false,])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('user')
                        ->orderBy('user.logged_at', 'DESC');
                },
            ])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->getClickedButton() && 'Block' === $form->getClickedButton()->getName()) {
            $this->checkUser();
            $data = $form->getData();
            $u = $data['users'];
            if(!isset($u)){
                return $this->redirectToRoute('app_user_all');
            }
            foreach ($u as $user){
                $user->setStatus("Blocked");
                $entityManager->persist($user);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_user_all');
        }

         if ($form->getClickedButton() && 'Unblock' === $form->getClickedButton()->getName()) {
            $this->checkUser();
            $data = $form->getData();
            $u = $data['users'];
            if(!isset($u)){
                return $this->redirectToRoute('app_user_all');
            }
            foreach ($u as $user){
                $user->setStatus("Active");
                $entityManager->persist($user);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_user_all');
        }

        if ($form->getClickedButton() && 'Delete' === $form->getClickedButton()->getName()) {
            $this->checkUser();
            $data = $form->getData();
            $u = $data['users'];
            $isUser = false;

            // dd($u, $this->getUser());
            if(!isset($u)){
                return $this->redirectToRoute('app_user_all');
            }
            if(in_array($this->getUser() , $u->toArray())){
                 $isUser = true;
            }
            foreach ($u as $user){
                $entityManager->remove($user);
                $entityManager->flush();
            }

            if($isUser){
                $security->logout(false);
                $request->getSession()->invalidate();
                return $this->redirectToRoute('app_main_homepage');
            }
            // $security->logout(false);
            // $entityManager->remove($user);
            // $entityManager->flush();
            // $request->getSession()->invalidate();
            return $this->redirectToRoute('app_user_all');
        }
        return $this->render('user/all.html.twig',[
            "users" => $users,
            "form" => $form
        ]);
    }

    private function checkUser(){
        if($this->getUser()->getStatus() !== "Active"){
            return $this->redirectToRoute('app_login');
        }
    }
}
