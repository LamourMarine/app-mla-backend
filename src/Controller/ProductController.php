<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\UnitRepository;
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
    public function index(Request $request): JsonResponse
    {
        $mine = $request->query->get('mine');

        if ($mine === 'true') {
            $user = $this->getUser();

            if (!$user) {
                return $this->json(['error' => 'Unauthorized'], 401);
            }

            $products = $this->productRepository->findBy(['seller' => $user]);
        } else {
            $products = $this->productRepository->findAll();
        }

        return $this->json($products, 200, [], [
            'groups' => ['product:read']
        ]);
    }


    #[Route('', name: 'create_product', methods: ['POST'])]
    #[IsGranted('ROLE_PRODUCTEUR')]
    public function createProduct(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepo,
        UnitRepository $unitRepo
    ): JsonResponse {
        // Récupérer le JSON depuis le champ 'data' du FormData
        $jsonData = $request->request->get('data');
        if (!$jsonData) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $data = json_decode($jsonData, true);

        // Créer le produit manuellement au lieu d'utiliser le serializer
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescriptionProduct($data['description_Product']);
        $product->setPrice((float) $data['price']); // ← Conversion explicite en float
        $product->setIsBio($data['isBio']);
        $product->setAvailability($data['availability']);

        // Set category et unit
        if (isset($data['categoryId'])) {
            $category = $categoryRepo->find($data['categoryId']);
            if (!$category) {
                return $this->json(['error' => 'Catégorie invalide'], 400);
            }
            $product->setCategory($category);
        }

        if (isset($data['unitId'])) {
            $unit = $unitRepo->find($data['unitId']);
            if (!$unit) {
                return $this->json(['error' => 'Unité invalide'], 400);
            }
            $product->setUnit($unit);
        }

        // Gérer l'upload de l'image
        $imageFile = $request->files->get('image_Product');
        if ($imageFile) {
            // Générer un nom unique
            $filename = uniqid() . '.' . $imageFile->guessExtension();

            // Déplacer le fichier vers public/uploads/products
            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/products',
                    $filename
                );

                // Enregistrer le chemin relatif dans la base
                $product->setImageProduct('/uploads/products/' . $filename);
            } catch (\Exception $e) {
                return $this->json(['error' => 'Erreur lors de l\'upload: ' . $e->getMessage()], 500);
            }
        }
        // Assigne le producteur connecté
        $product->setSeller($this->getUser());

        // Validation
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        // Persistance
        $em->persist($product);
        $em->flush();

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


    #[Route('/{id}', name: 'update_product', methods: ['POST'])]
    public function updateProduct(
        Request $request,
        Product $product,
        CategoryRepository $categoryRepo,
        UnitRepository $unitRepo
    ): JsonResponse {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        error_log('=== DEBUG POST ===');
        error_log('Content-Type: ' . $request->headers->get('Content-Type'));
        error_log('Request data: ' . json_encode($request->request->all()));
        error_log('Files: ' . json_encode(array_keys($request->files->all())));
        error_log('Method: ' . $request->getMethod());
        error_log('=================');

        // Récupérer les données depuis FormData
        $jsonData = $request->request->get('data');
        if (!$jsonData) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $data = json_decode($jsonData, true);  // Décoder le JSON depuis 'data'

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['price'])) {
            $product->setPrice((float) $data['price']);  //Convertir en float
        }
        if (isset($data['availability'])) {
            $product->setAvailability($data['availability']);
        }
        if (isset($data['description_Product'])) {
            $product->setDescriptionProduct($data['description_Product']);
        }
        if (isset($data['categoryId'])) {
            $category = $categoryRepo->find($data['categoryId']);
            if ($category) {
                $product->setCategory($category);
            }
        }
        if (isset($data['unitId'])) {
            $unit = $unitRepo->find($data['unitId']);
            if ($unit) {
                $product->setUnit($unit);
            }
        }
        if (isset($data['isBio'])) {
            $product->setIsBio($data['isBio']);
        }

        // Gérer l'image si un nouveau fichier est uploadé
        $imageFile = $request->files->get('image_Product');
        if ($imageFile) {
            $filename = uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/products',
                    $filename
                );

                $product->setImageProduct('/uploads/products/' . $filename);
            } catch (\Exception $e) {
                return $this->json(['error' => 'Erreur lors de l\'upload: ' . $e->getMessage()], 500);
            }
        }

        // Validation
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
