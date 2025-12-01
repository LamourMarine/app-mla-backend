<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/register', name: 'api_register', methods: ['POST'])]
class RegistrationController extends AbstractController
{
    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // 1. Récupérer les données JSON
        $data = json_decode($request->getContent(), true);
        
        // 2. Vérifier que les données nécessaires sont présentes
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['userType'])) {
            return $this->json([
                'message' => 'Email, mot de passe et type d\'utilisateur requis'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 3. Vérifier que le type d'utilisateur est valide
        $allowedTypes = ['producteur', 'structure'];
        if (!in_array($data['userType'], $allowedTypes)) {
            return $this->json([
                'message' => 'Type d\'utilisateur invalide. Choisir: producteur ou structure'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 4. Créer l'utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name'] ?? '');
        $user->setAddress($data['address']);
        $user->setPhoneNumber($data['phone_number']);

        
        // 5. Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        
        // 6. Définir le rôle selon le type d'utilisateur
        $role = match($data['userType']) {
            'producteur' => 'ROLE_PRODUCTEUR',
            'structure' => 'ROLE_STRUCTURE',
            default => 'ROLE_USER'
        };
        $user->setRoles([$role]);

        // 7. Définir le status en fonction du rôle
        if (in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
            $user->setStatus(User::STATUS_PENDING);
        } else {
            $user->setStatus(User::STATUS_APPROVED);
        }
        
        // 8. Valider l'entité
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json([
                'message' => 'Validation échouée',
                'errors' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // 9. Vérifier si l'email existe déjà
        $existingUser = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);
            
        if ($existingUser) {
            return $this->json([
                'message' => 'Cet email est déjà utilisé'
            ], Response::HTTP_CONFLICT);
        }
        
        //10. Sauvegarder l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();
        
        // 11. Retourner une réponse JSON
        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'address' => $user->getAddress(),
                'phone_number' => $user->getPhoneNumber(),
                'role' => $role,
                'userType' => $data['userType'],
                'status' => $user->getStatus()
            ]
        ], Response::HTTP_CREATED);
    }
}