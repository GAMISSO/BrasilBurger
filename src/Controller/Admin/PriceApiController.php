<?php

namespace App\Controller\Admin;

use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/api', name: 'app_admin_api_')]
class PriceApiController extends AbstractController
{
    #[Route('/burger-price', name: 'burger_price', methods: ['GET'])]
    public function burgerPrice(Request $request, BurgerRepository $burgerRepository): JsonResponse
    {
        $burgerId = $request->query->get('id');
        
        if (!$burgerId) {
            return $this->json(['error' => 'ID requis'], 400);
        }
        
        $burger = $burgerRepository->find($burgerId);
        
        if (!$burger) {
            return $this->json(['error' => 'Burger non trouvé'], 404);
        }
        
        return $this->json([
            'id' => $burger->getId(),
            'nom' => $burger->getNom(),
            'prix' => $burger->getPrix()
        ]);
    }
    
    #[Route('/complement-price', name: 'complement_price', methods: ['GET'])]
    public function complementPrice(Request $request, ComplementRepository $complementRepository): JsonResponse
    {
        $complementId = $request->query->get('id');
        
        if (!$complementId) {
            return $this->json(['error' => 'ID requis'], 400);
        }
        
        $complement = $complementRepository->find($complementId);
        
        if (!$complement) {
            return $this->json(['error' => 'Complément non trouvé'], 404);
        }
        
        return $this->json([
            'id' => $complement->getId(),
            'nom' => $complement->getNom(),
            'prix' => $complement->getPrix()
        ]);
    }
}
