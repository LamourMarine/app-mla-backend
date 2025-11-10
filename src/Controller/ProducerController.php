<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProducerController extends AbstractController
{
    #[Route('/api/producers', name: 'api_producers', methods: ['GET'])]
    public function __invoke(UserRepository $userRepo): JsonResponse
    {
        $producers = $userRepo->findByRole('ROLE_PRODUCTEUR');

        return $this->json($producers, 200, [], [
            'groups' => ['user:read']
        ]);
    }

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

        $userRepo->remove($user, true);

        return $this->json(['message' => 'Producteur supprimé']);
    }
}
