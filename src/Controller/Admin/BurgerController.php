<?php

// src/Controller/Admin/BurgerController.php
namespace App\Controller\Admin;

use App\Entity\Burger;
use App\Form\BurgerType;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/burger')]
class BurgerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {}

    #[Route('/', name: 'app_admin_burger_index' )]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Burger::class);
        $total = count($repository->findAll());
        $burgers = $repository->findBy([], ['created_at' => 'DESC'], $limit, $offset);
        $totalPages = ceil($total / $limit);

        return $this->render('admin/burger/index.html.twig', [
            'burgers' => $burgers,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    #[Route('/new', name: 'app_admin_burger_new' )]
    public function new(Request $request): Response
    {
        $burger = new Burger();
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image_url')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/burgers']
                    );
                    $burger->setImage_url($result['secure_url']);
                } catch (\Exception $e) {
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
    public function edit(Request $request, Burger $burger): Response
    {
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image_url')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/burgers']
                    );
                    $burger->setImage_url($result['secure_url']);
                } catch (\Exception $e) {
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