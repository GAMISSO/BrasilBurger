<?php

// src/Controller/Admin/BurgerController.php
namespace App\Controller\Admin;

use App\Entity\Burger;
use App\Form\BurgerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/burger')]
class BurgerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_admin_burger_index' )]
    public function index(): Response
    {
        $burgers = $this->entityManager->getRepository(Burger::class)->findAll();

        return $this->render('admin/burger/index.html.twig', [
            'burgers' => $burgers,
        ]);
    }

    #[Route('/new', name: 'app_admin_burger_new' )]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $burger = new Burger();
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image_url')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('burgers_directory'),
                        $newFilename
                    );
                    $burger->setImage_url($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $burger->setCreated_at(new \DateTime());
            $this->entityManager->persist($burger);
            $this->entityManager->flush();

            $this->addFlash('success', 'Burger créé avec succès !');

            return $this->redirectToRoute('app_admin_burger_index');
        }

        return $this->render('admin/burger/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_burger_edit')]
    public function edit(Request $request, Burger $burger, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image_url')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('burgers_directory'),
                        $newFilename
                    );
                    $burger->setImage_url($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Burger modifié avec succès !');

            return $this->redirectToRoute('app_admin_burger_index');
        }

        return $this->render('admin/burger/edit.html.twig', [
            'form' => $form->createView(),
            'burger' => $burger,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_burger_delete', methods: ['POST'])]
    public function delete(Request $request, Burger $burger): Response
    {
        if ($this->isCsrfTokenValid('delete'.$burger->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($burger);
            $this->entityManager->flush();

            $this->addFlash('success', 'Burger supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_burger_index');
    }
}