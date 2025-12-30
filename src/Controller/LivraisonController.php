<?php
namespace App\Controller;

use App\Form\LivraisonAffectationType;
use App\Service\LivraisonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    public function __construct(
        private LivraisonService $livraisonService
    ) {}

    #[Route('/', name: 'app_livraison_index')]
    public function index(): Response
    {
        $commandesParZone = $this->livraisonService->getCommandesParZone();
        $livreurs = $this->livraisonService->getLivreursDisponibles();

        return $this->render('livraison/index.html.twig', [
            'commandesParZone' => $commandesParZone,
            'livreurs' => $livreurs,
        ]);
    }

    #[Route('/{id}/affecter', name: 'app_livraison_affecter', methods: ['POST'])]
    public function affecter(Request $request, $id): Response
    {
        $livreurId = $request->request->get('livreur_id');

        if (!$livreurId) {
            $this->addFlash('error', 'Veuillez sélectionner un livreur.');
            return $this->redirectToRoute('app_livraison_index');
        }

        try {
            $this->livraisonService->affecterLivreur([$id], $livreurId);
            $this->addFlash('success', 'Commande affectée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'affectation: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_livraison_index');
    }
}