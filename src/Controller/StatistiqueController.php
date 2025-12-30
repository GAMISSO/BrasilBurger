<?php

namespace App\Controller;

use App\Service\StatistiqueService;
use App\DTO\StatistiqueDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/statistiques')]
class StatistiqueController extends AbstractController
{
    public function __construct(
        private StatistiqueService $statistiqueService
    ) {}

    #[Route('/', name: 'app_statistiques_index')]
    public function index(Request $request): Response
    {
        try {
            $dateStr = $request->query->get('date', date('Y-m-d'));
            $date = new \DateTime($dateStr);

            $statistiques = $this->statistiqueService->getStatistiquesJour($date);

            return $this->render('statistiques/index.html.twig', [
                'date' => $date,
                'stats' => $statistiques,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur statistiques: ' . $e->getMessage());
            return $this->render('statistiques/index.html.twig', [
                'date' => new \DateTime(),
                'stats' => [],
            ]);
        }
    }

    #[Route('/periode', name: 'app_statistiques_periode')]
    public function periode(Request $request): Response
    {
        $debutStr = $request->query->get('debut', date('Y-m-d', strtotime('-7 days')));
        $finStr = $request->query->get('fin', date('Y-m-d'));

        $debut = new \DateTime($debutStr);
        $fin = new \DateTime($finStr);

        $statistiques = $this->statistiqueService->getStatistiquesPeriode($debut, $fin);

        return $this->render('statistiques/periode.html.twig', [
            'debut' => $debut,
            'fin' => $fin,
            'stats' => $statistiques,
        ]);
    }

    #[Route('/export', name: 'app_statistiques_export')]
    public function export(Request $request): Response
    {
        $dateStr = $request->query->get('date', date('Y-m-d'));
        $date = new \DateTime($dateStr);

        $statistiques = $this->statistiqueService->getStatistiquesJour($date);

        // Créer un CSV
        $csv = [];
        $csv[] = ['Indicateur', 'Valeur'];
        $csv[] = ['Commandes en cours', $statistiques['commandes_en_cours']];
        $csv[] = ['Commandes validées', $statistiques['commandes_validees']];
        $csv[] = ['Recettes journalières', $statistiques['recettes_journalieres'] . ' FCFA'];
        $csv[] = ['Commandes annulées', $statistiques['commandes_annulees']];

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="stats-' . $dateStr . '.csv"');

        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $response->setContent(stream_get_contents($output));
        fclose($output);

        return $response;
    }
}