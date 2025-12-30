<?php
// src/Controller/HomeController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        try {
            // Si connecté, aller à la page des commandes
            if ($this->getUser()) {
                return $this->redirectToRoute('app_commande_index');
            }
            // Sinon, aller au login
            return $this->redirectToRoute('app_login');
        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers login avec message
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}