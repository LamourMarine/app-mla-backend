<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;


class ProducerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}
    
    #[Route('/api/producers', name: 'api_producers', methods: ['GET'])]
    public function __invoke(UserRepository $userRepo): JsonResponse
    {
        $allProducers = $userRepo->findByRole('ROLE_PRODUCTEUR');

        // Filtre uniquement les actifs
        $activeProducers = array_filter($allProducers, fn($user) => $user->isActive());

        return $this->json(array_values($activeProducers), 200, [], [
            'groups' => ['user:read']
        ]);
    }

    // Liste des producteurs DÉSACTIVÉS (admin seulement)
    #[Route('/api/producers/deactivated', name: 'api_producers_deactivated', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getDeactivated(UserRepository $userRepo): JsonResponse
    {
        $allProducers = $userRepo->findByRole('ROLE_PRODUCTEUR');
        
        // Filtre uniquement les désactivés
        $deactivatedProducers = array_filter($allProducers, fn($user) => !$user->isActive());
        
        return $this->json(array_values($deactivatedProducers), 200, [], [
            'groups' => ['user:read']
        ]);
    }

    // Désactiver un producteur (soft delete)
    #[Route('/api/producers/{id}/deactivate', name: 'api_producers_deactivate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deactivate(int $id, UserRepository $userRepo): JsonResponse
    {
        $user = $userRepo->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'Producteur non trouvé'], 404);
        }
        
        if (!in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            return $this->json(['error' => 'Cet utilisateur n\'est pas un producteur'], 400);
        }
        
        $user->deactivate();
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Producteur désactivé',
            'user' => $user
        ], 200, [], ['groups' => ['user:read']]);
    }

    // Réactiver un producteur
    #[Route('/api/producers/{id}/activate', name: 'api_producers_activate', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function activate(int $id, UserRepository $userRepo): JsonResponse
    {
        $user = $userRepo->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'Producteur non trouvé'], 404);
        }
        
        if (!in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            return $this->json(['error' => 'Cet utilisateur n\'est pas un producteur'], 400);
        }
        
        $user->activate();
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Producteur réactivé',
            'user' => $user
        ], 200, [], ['groups' => ['user:read']]);
    }

    // Suppression définitive (optionnel, garde-le si tu veux vraiment supprimer)
    #[Route('/api/producers/{id}', name: 'api_producers_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, UserRepository $userRepo): JsonResponse
    {
        $user = $userRepo->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'Producteur non trouvé'], 404);
        }
        
        if (!in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            return $this->json(['error' => 'Cet utilisateur n\'est pas un producteur'], 400);
        }
        
        // Vérifie qu'il est déjà désactivé
        if ($user->isActive()) {
            return $this->json([
                'error' => 'Désactivez d\'abord le producteur avant de le supprimer définitivement'
            ], 400);
        }
        
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Producteur supprimé définitivement']);
    }
}
