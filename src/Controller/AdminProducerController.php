<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/producers')]
#[IsGranted('ROLE_ADMIN')]
class AdminProducerController extends AbstractController
{
    #[Route('/pending', name: 'api_admin_producers_pending', methods: ['GET'])]
    public function getPendingProducers(EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer le repository User
        $userRepository = $entityManager->getRepository(User::class);
        
        // Récupérer TOUS les users avec status pending
        $allPendingUsers = $userRepository->findBy(['status' => User::STATUS_PENDING]);
        
        // Filtrer en PHP pour ne garder que les producteurs
        $pendingProducers = array_filter($allPendingUsers, function(User $user) {
            return in_array('ROLE_PRODUCTEUR', $user->getRoles());
        });
        
        // Formater les données pour le JSON
        $data = array_map(function(User $user) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'address' => $user->getAddress(),
                'phoneNumber' => $user->getPhoneNumber(),
                'status' => $user->getStatus(),
            ];
        }, $pendingProducers);
        
        return $this->json([
            'producers' => array_values($data), // array_values pour réindexer
            'total' => count($data)
        ], Response::HTTP_OK);
    }
    #[Route('/{id}/approve', name: 'api_admin_producer_approve', methods: ['PATCH'])]
    public function approveProducer(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Vérifier que c'est un producteur
        if (!in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            return $this->json([
                'message' => 'Cet utilisateur n\'est pas un producteur'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Vérifier qu'il est en attente
        if ($user->getStatus() !== User::STATUS_PENDING) {
            return $this->json([
                'message' => 'Ce producteur n\'est pas en attente de validation'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Approuver le producteur
        $user->setStatus(User::STATUS_APPROVED);
        $entityManager->flush();
        
        return $this->json([
            'message' => 'Producteur approuvé avec succès',
            'producer' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus()
            ]
        ], Response::HTTP_OK);
    }

    #[Route('/{id}/reject', name: 'api_admin_producer_reject', methods: ['PATCH'])]
    public function rejectProducer(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }
        
        if (!in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            return $this->json([
                'message' => 'Cet utilisateur n\'est pas un producteur'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        if ($user->getStatus() !== User::STATUS_PENDING) {
            return $this->json([
                'message' => 'Ce producteur n\'est pas en attente de validation'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Rejeter le producteur
        $user->setStatus(User::STATUS_REJECTED);
        $entityManager->flush();
        
        return $this->json([
            'message' => 'Producteur rejeté',
            'producer' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus()
            ]
        ], Response::HTTP_OK);
    }
}
