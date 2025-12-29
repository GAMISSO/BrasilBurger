<?php

namespace App\Security\Voter;

use App\Entity\OrderTable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    public const VIEW = 'ORDER_VIEW';
    public const EDIT = 'ORDER_EDIT';
    public const DELETE = 'ORDER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof OrderTable;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?\Symfony\Component\Security\Core\Authorization\Voter\Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var OrderTable $order */
        $order = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($order, $user),
            self::EDIT => $this->canEdit($order, $user),
            self::DELETE => $this->canDelete($order, $user),
            default => false,
        };
    }

    private function canView(OrderTable $order, UserInterface $user): bool
    {
        // Les utilisateurs peuvent voir leurs propres commandes
        return true;
    }

    private function canEdit(OrderTable $order, UserInterface $user): bool
    {
        // Seul un gestionnaire peut éditer
        return in_array('ROLE_GESTIONNAIRE', $user->getRoles());
    }

    private function canDelete(OrderTable $order, UserInterface $user): bool
    {
        // Seul un gestionnaire peut supprimer
        return in_array('ROLE_GESTIONNAIRE', $user->getRoles());
    }
}
