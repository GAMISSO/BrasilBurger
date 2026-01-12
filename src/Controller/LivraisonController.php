<?php
namespace App\Controller;

use App\Entity\Zones;
use App\Entity\Livreur;
use App\Entity\LivreurZone;
use App\Form\LivraisonAffectationType;
use App\Service\LivraisonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    public function __construct(
        private LivraisonService $livraisonService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'app_livraison_index')]
    public function index(Request $request): Response
    {
        try {
            // Créer une zone
            if ($request->request->has('creer_zone')) {
                $nomZone = $request->request->get('nom_zone');
                $prixZone = (int) $request->request->get('prix_zone');
                
                if ($nomZone && $prixZone > 0) {
                    $zone = new Zones();
                    $zone->setNom($nomZone);
                    $zone->setPrix_zone($prixZone);
                    $zone->setCreated_at(new \DateTime());
                    
                    $this->entityManager->persist($zone);
                    $this->entityManager->flush();
                    
                    $this->addFlash('success', "Zone '$nomZone' créée avec succès!");
                }
            }
            
            // Créer un livreur
            if ($request->request->has('creer_livreur')) {
                $nomLivreur = $request->request->get('nom_livreur');
                $disponibilite = $request->request->get('disponibilite_livreur', 'Disponible');
                
                if ($nomLivreur) {
                    $livreur = new Livreur();
                    $livreur->setNom($nomLivreur);
                    $livreur->setDisponibilite($disponibilite);
                    
                    $this->entityManager->persist($livreur);
                    $this->entityManager->flush();
                    
                    $this->addFlash('success', "Livreur '$nomLivreur' créé avec succès!");
                }
            }

            // Affecter livreur à zone
            if ($request->request->has('affecter_zone')) {
                $livreurId = (int) $request->request->get('livreur_id');
                $zoneIds = $request->request->all()['zone_ids'] ?? [];
                
                if ($livreurId > 0) {
                    $livreur = $this->entityManager->getRepository(Livreur::class)->find($livreurId);
                    
                    if ($livreur) {
                        // Supprimer les affectations existantes
                        $existants = $this->entityManager->getRepository(LivreurZone::class)->findBy(['livreur' => $livreur]);
                        foreach ($existants as $existant) {
                            $this->entityManager->remove($existant);
                        }
                        
                        // Ajouter les nouvelles affectations
                        foreach ($zoneIds as $zoneId) {
                            $zone = $this->entityManager->getRepository(Zones::class)->find($zoneId);
                            if ($zone) {
                                $lz = new LivreurZone();
                                $lz->setLivreur($livreur);
                                $lz->setZone($zone);
                                $this->entityManager->persist($lz);
                            }
                        }
                        
                        $this->entityManager->flush();
                        $this->addFlash('success', "Zones affectées au livreur '{$livreur->getNom()}'!");
                    }
                }
            }
            
            // Récupérer les données
            $commandesParZone = $this->livraisonService->getCommandesParZone();
            $livreurs = $this->livraisonService->getLivreursDisponibles();
            $zones = $this->entityManager->getRepository(Zones::class)->findAll();
            $tousLivreurs = $this->entityManager->getRepository(Livreur::class)->findAll();
            
            // Récupérer les affectations livreur-zone
            $affectationsLivreurZone = $this->entityManager->getRepository(LivreurZone::class)->findAll();

            return $this->render('livraison/index.html.twig', [
                'commandesParZone' => $commandesParZone,
                'livreurs' => $livreurs,
                'zones' => $zones,
                'tousLivreurs' => $tousLivreurs,
                'affectationsLivreurZone' => $affectationsLivreurZone,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur livraison: ' . $e->getMessage());
            return $this->render('livraison/index.html.twig', [
                'commandesParZone' => [],
                'livreurs' => [],
                'zones' => [],
                'tousLivreurs' => [],
                'affectationsLivreurZone' => [],
            ]);
        }
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