<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, (string)$user->getPlainPassword()));
            $user->eraseCredentials();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', sprintf('%s, регистрация прошла успешно', $user->getName()));

            //редирект, чтобы обновление страницы не отправляло форму второй раз
            return $this->redirectToRoute('register');
        }

        return $this->render('registration/register.html.twig', ['form' => $form]);
    }
}
