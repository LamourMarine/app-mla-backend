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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/products', name: 'api_products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'product_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();

        return $this->json($products, 200, [], [
            'groups' => ['product:read']
        ]);
    }


    #[Route('', name: 'create_product', methods: ['POST'])]
    #[IsGranted('ROLE_PRODUCTEUR')]
    public function createProduct(Request $request): JsonResponse
    {
        // Désérialise le JSON en objet Product
        $product = $this->serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json'
        );

        // Assigne automatiquement le producteur connecté
        $product->setSeller($this->getUser());

        // Validation
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        // Persistance
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Renvoi JSON
        return $this->json($product, Response::HTTP_CREATED, [], [
            'groups' => ['product:read']
        ]);
    }



    #[Route('/{id}', name: 'show_product', methods: ['GET'])]
    public function showProduct(Product $product): JsonResponse
    {
        return $this->json($product, Response::HTTP_OK, [], [
            'groups' => ['product:read']
        ]);
    }


    #[Route('/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, Product $product): JsonResponse
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['image_Product'])) {
            $product->setImageProduct($data['image_Product']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['availability'])) {
            $product->setAvailability($data['availability']);
        }
        if (isset($data['description_Product'])) {
            $product->setDescriptionProduct($data['description_Product']);
        }
        if (isset($data['category'])) {
            $product->setCategory($data['category']);
        }
        if (isset($data['isBio'])) {
            $product->setIsBio($data['isBio']);
        }

        $product->setSeller($this->getUser());

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_OK, [], [
            'groups' => ['product:read']
        ]);
    }

    #[Route('/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(
        Product $product,
    ): JsonResponse {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
