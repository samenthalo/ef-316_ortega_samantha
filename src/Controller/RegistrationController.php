<?php

namespace App\Controller;

use App\Entity\User;
use App\form\registrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(registrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $user->setName($name);

            $plainPassword = $form->get('password')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles([$form->get('isAdmin')->getData() ? 'ROLE_ADMIN' : 'ROLE_USER']);

            $entityManager->persist ($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
   
    }
}