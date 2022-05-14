<?php

namespace App\Controller;

use App\Form\ProfileEditFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'main_profile_index')]
    public function index(): Response
    {
        return $this->render('main/profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/profile/edit', name: 'main_profile_edit')]
    public function edit(ManagerRegistry $doctrine, Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
