<?php

namespace App\Controller;

use App\Entity\OrderTable;
use App\Form\CommandeFilterType;
use App\Service\CommandeService;
use App\DTO\CommandeFilterDTO;
use App\DTO\OrderFilterDTO;
use App\Form\OrderFilterType;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $commandeService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_commande_index')]
    public function index(Request $request): Response
    {
        $filterDTO = new OrderFilterDTO();
        
        $form = $this->createForm(OrderFilterType::class, $filterDTO);
        $form->handleRequest($request);

        $commandes = $this->commandeService->getCommandesFilters(
            $filterDTO->toArray()
        );

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'form' => $form->createView(),
            'hasFilters' => $filterDTO->hasFiltresActifs(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', requirements: ['id' => '\d+'])]
    public function show(OrderTable $commande): Response
    {
        // Récupérer les lignes de commande
        $lignes = $this->entityManager->getRepository('App\Entity\OrderLine')
            ->findBy(['order_id' => $commande->getId()]);

        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'lignes' => $lignes,
        ]);
    }

    #[Route('/{id}/changer-etat', name: 'app_commande_changer_etat', methods: ['POST'])]
    public function changerEtat(Request $request, OrderTable $commande): Response
    {
        $nouvelEtat = $request->request->get('etat');
        
        try {
            $this->commandeService->changerEtat($commande, $nouvelEtat);
            $this->addFlash('success', 'État de la commande modifié avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
    }

    #[Route('/{id}/terminer', name: 'app_commande_terminer')]
    public function terminer(OrderTable $commande): Response
    {
        try {
            $this->commandeService->terminerCommande($commande);
            $this->addFlash('success', 'Commande marquée comme terminée.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/{id}/annuler', name: 'app_commande_annuler')]
    public function annuler(OrderTable $commande): Response
    {
        try {
            $this->commandeService->annulerCommande($commande);
            $this->addFlash('success', 'Commande annulée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible d\'annuler cette commande: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_commande_index');
    }
}