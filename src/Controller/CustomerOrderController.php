<?php
namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Repository\CustomerOrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/orders', name: 'api_orders')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CustomerOrderController extends AbstractController
{
    public function __construct(
        private CustomerOrderRepository $customerOrderRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    // Lister les commandes de l'utilisateur connecté
    #[Route('', name: 'orders_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $orders = $this->customerOrderRepository->findBy(['customer' => $user], ['order_at' => 'DESC']);

        return $this->json($orders, 200, [], [
            'groups' => ['CustomerOrder:read']
        ]);
    }

    // Créer une nouvelle commande
    #[Route('', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Vérifier que les items sont présents
        if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
            return $this->json([
                'message' => 'Le panier est vide ou invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        
        // Créer la commande
        $customerOrder = new CustomerOrder();
        $customerOrder->setCustomer($user);
        $customerOrder->setOrderAt(new \DateTimeImmutable());
        
        // Générer un numéro de commande unique
        $orderNumber = 'CMD-' . date('Ymd') . '-' . uniqid();
        $customerOrder->setNumber($orderNumber);
        
        $total = 0;
        
        // Créer les lignes de commande
        foreach ($data['items'] as $item) {
            if (!isset($item['productId']) || !isset($item['quantity'])) {
                return $this->json([
                    'message' => 'Format d\'item invalide'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Récupérer le produit
            $product = $this->productRepository->find($item['productId']);
            
            if (!$product) {
                return $this->json([
                    'message' => "Produit avec l'ID {$item['productId']} introuvable"
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Créer la ligne de commande
            $orderLine = new OrderLine();
            $orderLine->setProduct($product);
            $orderLine->setQuantity($item['quantity']);
            $orderLine->setOrderRef($customerOrder);
            
            // Calculer le sous-total
            $lineTotal = $product->getPrice() * $item['quantity'];
            $total += $lineTotal;
            
            $customerOrder->addOrderLine($orderLine);
            $this->entityManager->persist($orderLine);
        }
        
        // Définir le total
        $customerOrder->setTotal((string) $total);
        
        // Sauvegarder
        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();
        
        return $this->json([
            'message' => 'Commande créée avec succès',
            'order' => [
                'id' => $customerOrder->getId(),
                'number' => $customerOrder->getNumber(),
                'total' => $customerOrder->getTotal(),
                'orderAt' => $customerOrder->getOrderAt()->format('Y-m-d H:i:s'),
                'itemsCount' => count($customerOrder->getOrderLines())
            ]
        ], Response::HTTP_CREATED);
    }

    // Voir le détail d'une commande
    #[Route('/{id}', name: 'show_order', methods: ['GET'])]
    public function showOrder(CustomerOrder $customerOrder): JsonResponse
    {
        // Vérifier que la commande appartient bien à l'utilisateur connecté
        if ($customerOrder->getCustomer() !== $this->getUser()) {
            return $this->json([
                'message' => 'Accès non autorisé'
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->json($customerOrder, Response::HTTP_OK, [], [
            'groups' => ['CustomerOrder:read', 'CustomerOrder:detail']
        ]);
    }
}