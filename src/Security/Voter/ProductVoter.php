<?php
// src/Security/Voter/ProductVoter.php
namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter 
{
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';
    public const VIEW = 'PRODUCT_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
        && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // L'utilisateur doit être connecté
        if (!$user instanceof User) {
            return false;
        }

        /** @var Product $product */
        $product = $subject;

        // Les admins peuvent tout faire
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => true, // Tout le monde peut voir les produits
            self::EDIT, self::DELETE => $this->canEditOrDelete($product, $user),
            default => false,
        };
    }

    private function canEditOrDelete(Product $product, User $user): bool
    {
        // Le producteur ne peut modifier/supprimer que ses produits
        return $product->getSeller() === $user && $user->isProducteur();
    }
}