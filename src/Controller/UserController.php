<?php
// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users', name: 'api_users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

        #[Route('', name: 'user_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->userRepository->findAll();

        return $this->json($user, 200, [], [
            'groups' => ['user:read']
        ]);
    }


    #[Route('', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED, [], [
            'groups' => ['user:read']
        ]);
    }


    #[Route('/{id}', name: 'show_user', methods: ['GET'])]
    public function showUser(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => ['user:read']
        ]);
    }


    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        if (isset($data['address'])) {
            $user->setAdress($data['address']);
        }

        if (isset($data['phoneNumber'])) {
            $user->setPhoneNumber($data['phoneNumber']);
        }

        if (isset($data['names'])) {
            $user->setName($data['names']);
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => ['user:read']
        ]);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
