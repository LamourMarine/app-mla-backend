<?php
// src/Controller/CustomerOrderController.php
namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Repository\CustomerOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route ('/api/customer-orders' , name: 'api_customer_orders')]
class CustomerOrderController extends AbstractController
{
    public function __construct(
        private CustomerOrderRepository $CustomerOrderRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'customer_orders_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $customerOrders = $this->CustomerOrderRepository->findAll();

        return $this->json($customerOrders, 200, [], [
            'groups' => ['customerOrder:read']
        ]);
    }

    #[Route('/{id}', name: 'show_customer_order', methods: ['GET'])]
    public function showCustomerOrder(CustomerOrder $customerOrder): JsonResponse
    {
        return $this->json($customerOrder, Response::HTTP_OK, [], [
            'groups' => ['customerOrder:read']
        ]);
    }

    #[Route('/new', name: 'new_customer_order', methods: ['POST'])]
    public function newCustomerOrder(Request $request): JsonResponse
    {
        $customerOrder = $this->serializer->deserialize(
            $request->getContent(),
            CustomerOrder::class,
            'json'
        );

        $errors = $this->validator->validate($customerOrder);
        if (count($errors) >0) {
            return $this->json([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($customerOrder);
        $this->entityManager->flush();

        return $this->json($customerOrder, Response::HTTP_CREATED, [], [
            'groups' => ['customerOrder:read']
        ]);
    }

}
