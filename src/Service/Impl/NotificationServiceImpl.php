<?php
// src/Service/NotificationService.php
namespace App\Service\Impl;
use App\Service\NotificationService;
class NotificationServiceImpl implements NotificationService
{
    /**
     * Envoyer une notification (placeholder)
     */
    public function envoyerNotification(string $type, array $data): void
    {
        // Implémenter l'envoi de notifications
        // (Email, SMS, Push, etc.)
        
        // Pour l'instant, juste un log
        error_log(sprintf("Notification %s: %s", $type, json_encode($data)));
    }

    /**
     * Notifier changement d'état commande
     */
    public function notifierChangementEtat(int $commandeId, string $nouvelEtat): void
    {
        $this->envoyerNotification('ETAT_COMMANDE', [
            'commande_id' => $commandeId,
            'etat' => $nouvelEtat
        ]);
    }

    /**
     * Notifier nouvelle commande
     */
    public function notifierNouvelleCommande(int $commandeId): void
    {
        $this->envoyerNotification('NOUVELLE_COMMANDE', [
            'commande_id' => $commandeId
        ]);
    }

    /**
     * Notifier affectation livreur
     */
    public function notifierAffectationLivreur(int $livreurId, array $commandeIds): void
    {
        $this->envoyerNotification('AFFECTATION_LIVREUR', [
            'livreur_id' => $livreurId,
            'commande_ids' => $commandeIds
        ]);
    }
}