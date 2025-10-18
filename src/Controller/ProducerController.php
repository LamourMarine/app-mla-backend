<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/producers', name: 'api_producers', methods: ['GET'])]
class ProducerController extends AbstractController
{
    public function __invoke(UserRepository $userRepo): JsonResponse
    {
        // Récupérer uniquement les producteurs
        $producers = $userRepo->findByRole('ROLE_PRODUCTEUR');
        
        return $this->json($producers, 200, [], [
            'groups' => ['user:read']
        ]);
    }
}