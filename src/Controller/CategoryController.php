<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route ('/api/categories' , name: 'api_categories')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'category_list', methods: ['GET'])]
    public function index():JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return $this->json($categories, 200, [], [
            'groups' => ['category:read']
        ]);
    }

    #[Route('/{id}', name: 'show_category', methods: ['GET'])]
    public function showCategory(Category $category): JsonResponse 
    {
        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => ['category:read']
        ]);
    }

    #[Route('/new', name: 'new_category', methods: ['POST'])]
    public function newCategory(Request $request): JsonResponse 
    {
        $category = $this->serializer->deserialize(
            $request->getContent(),
            Category::class,
            'json'
        );

        $errors = $this->validator->validate($category);
        if (count($errors) >0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->json($category, Response::HTTP_CREATED, [], [
            'groups' => ['category:read']
        ]);
    }

    #[Route('/{id}', name: 'update_categories', methods: ['PUT'])]
    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->setName($data['name']);
        }

        $errors = $this->validator->validate($category);
        if (count($errors) >0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => ['category:read']
        ]);
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory( Category $category): JsonResponse
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
