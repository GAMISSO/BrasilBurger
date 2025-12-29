<?php

namespace App\Controller\Admin;

use App\Entity\Complement;
use App\Form\ComplementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/complement')]
class ComplementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_admin_complement_index')]
    public function index(): Response
    {
        $complements = $this->entityManager->getRepository(Complement::class)->findAll();

        return $this->render('admin/complement/index.html.twig', [
            'complements' => $complements,
        ]);
    }

    #[Route('/new', name: 'app_admin_complement_new')]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
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
                        $this->getParameter('complements_directory'),
                        $newFilename
                    );
                    $complement->setImage($newFilename);
                } catch (FileException $e) {
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
    public function edit(Request $request, Complement $complement, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
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
                        $this->getParameter('complements_directory'),
                        $newFilename
                    );
                    $complement->setImage($newFilename);
                } catch (FileException $e) {
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