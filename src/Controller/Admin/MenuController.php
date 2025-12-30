<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/menu')]
class MenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {}

    #[Route('/', name: 'app_admin_menu_index')]
    public function index(): Response
    {
        $menus = $this->entityManager->getRepository(Menu::class)->findAll();

        return $this->render('admin/menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/new', name: 'app_admin_menu_new')]
    public function new(Request $request): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/menus']
                    );
                    $menu->setImage($result['secure_url']);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            // Get burger_id from form
            $burgerId = $form->get('burger_id')->getData();
            if ($burgerId) {
                $menu->setBurger_id($burgerId->getId());
            }

            $menu->setCreated_at(new \DateTime());
            $this->entityManager->persist($menu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Menu créé avec succès !');

            return $this->redirectToRoute('app_admin_menu_index');
        }

        return $this->render('admin/menu/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_menu_edit')]
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/menus']
                    );
                    $menu->setImage($result['secure_url']);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $burgerId = $form->get('burger_id')->getData();
            if ($burgerId) {
                $menu->setBurger_id($burgerId->getId());
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Menu modifié avec succès !');

            return $this->redirectToRoute('app_admin_menu_index');
        }

        return $this->render('admin/menu/edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($menu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Menu supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_menu_index');
    }
}