<?php

namespace App\Controller\Admin;

use App\Entity\Complement;
use App\Form\ComplementType;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/complement')]
class ComplementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {}

    #[Route('/', name: 'app_admin_complement_index')]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $repository = $this->entityManager->getRepository(Complement::class);
        $total = count($repository->findAll());
        $complements = $repository->findBy([], ['created_at' => 'DESC'], $limit, $offset);
        $totalPages = ceil($total / $limit);

        return $this->render('admin/complement/index.html.twig', [
            'complements' => $complements,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    #[Route('/new', name: 'app_admin_complement_new')]
    public function new(Request $request): Response
    {
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/complements']
                    );
                    $complement->setImage($result['secure_url']);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $complement->setCreated_at(new \DateTime());
            $this->entityManager->persist($complement);
            $this->entityManager->flush();

            $this->addFlash('success', 'Complément créé avec succès !');

            return $this->redirectToRoute('app_admin_complement_index');
        }

        return $this->render('admin/complement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_complement_edit')]
    public function edit(Request $request, Complement $complement): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload with Cloudinary
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $result = $this->cloudinaryService->upload(
                        $imageFile->getRealPath(),
                        ['folder' => 'burgerBrasil/complements']
                    );
                    $complement->setImage($result['secure_url']);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Complément modifié avec succès !');

            return $this->redirectToRoute('app_admin_complement_index');
        }

        return $this->render('admin/complement/edit.html.twig', [
            'form' => $form->createView(),
            'complement' => $complement,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_complement_delete', methods: ['POST'])]
    public function delete(Request $request, Complement $complement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$complement->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($complement);
            $this->entityManager->flush();

            $this->addFlash('success', 'Complément supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_complement_index');
    }
}