<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/products', name: 'api_products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'image' => $product->getImage(),
                'price' => $product->getPrice(),
                'availability' => $product->getAvailability(),
                'description' => $product->getDescription(),
                'category' => $product->getCategory(),
                'isBio' => $product->getIsBio(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/{id}', name: 'showProduct', methods: ['GET'])]
    public function showProduct(Product $product): JsonResponse
    {
        return $this->json($product, Response::HTTP_OK, [], [
            'groups' => ['product:read', 'product:detail']
        ]);
    }

    #[Route('/new', name: 'newProduct', methods: ['POST'])]
    public function newProduct(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $entityManager, ValidatorInterface $validatorInterface): JsonResponse
    {
    
        // Désérialise le JSON en objet User
        $product = $this->$serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json'
        );

        $errors = $validatorInterface->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->$entityManager->persist($product);
        $this->$entityManager->flush();

        return $this->json($product);
    }

    #[Route('/{id}' , name: 'deleteProduct' , methods: ['DELETE'])]
    public function deleteProduct(Product $product, 
    EntityManagerInterface $entityManager): JsonResponse
    {
        $this->$entityManager->remove($product);
        $this->$entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
