<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UsersAuthenticator;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $messageColor = "info";
        $messages = [];

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userRepository->findOneBy(['username' => $form->get('username')->getData()])) {
                $messages[] = "Le pseudo existe déjà, veuillez en choisir un autre";
                $messageColor = "danger";
            } else if ($form->get('plainPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                $messages[] = "Les mots de passe ne sont pas les mêmes";
                $messageColor = "danger";
            } else {
                $birthDay = $form->get('birth_day')->getData();
                $birthMonth = $form->get('birth_month')->getData();
                $birthYear = $form->get('birth_year')->getData();

                // Concaténez les valeurs dans le format de date souhaité
                $birthDate = $birthYear . '-' . $birthMonth . '-' . $birthDay;
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                )
                    ->setBirthDate(new DateTime($birthDate))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUpdatedAt(new DateTimeImmutable())
                    ->setRoles(["ROLE_USER"]);

                $entityManager->persist($user);
                $entityManager->flush();

                return $security->login($user, UsersAuthenticator::class, 'main');
            }
        }
        foreach ($form->getErrors(true, true) as $error) {
            // Pour chaque erreur, récupérer le message d'erreur
            $messages[] = $error->getMessage();
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'registrationForm' => $form,
                'messages' => $messages,
                'messageColor' => $messageColor,
            ]
        );
    }
}
