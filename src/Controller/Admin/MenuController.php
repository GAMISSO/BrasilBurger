<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/menu')]
class MenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
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
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('menus_directory'),
                        $newFilename
                    );
                    $menu->setImage($newFilename);
                } catch (FileException $e) {
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
    public function edit(Request $request, Menu $menu, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('menus_directory'),
                        $newFilename
                    );
                    $menu->setImage($newFilename);
                } catch (FileException $e) {
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