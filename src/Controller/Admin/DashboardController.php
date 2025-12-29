<?php

// src/Controller/Admin/DashboardController.php
namespace App\Controller\Admin;

use App\Service\StatistiqueService;
use App\Service\CommandeService;
use App\Service\Impl\OrderServiceImpl;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    public function __construct(
        private StatistiqueService $statistiqueService,
        private OrderServiceImpl $commandeService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_dashboard')]
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $aujourd_hui = new \DateTime();
        
        // Statistiques du jour
        $stats = $this->statistiqueService->getStatistiquesJour($aujourd_hui);

        // Commandes récentes
        $commandesRecentes = $this->commandeService->getCommandesFilters([
            'date' => $aujourd_hui->format('Y-m-d')
        ]);

        // Limiter à 10
        $commandesRecentes = array_slice($commandesRecentes, 0, 10);

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'commandesRecentes' => $commandesRecentes,
            'date' => $aujourd_hui,
        ]);
    }
}