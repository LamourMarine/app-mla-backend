<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Entity\Unit;
use App\Repository\UnitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route ('/api/units' , name: 'api_units')]
class UnitController extends AbstractController
{
    public function __construct(
        private UnitRepository $unitRepository,
    ) {}

    #[Route('' , name: 'unit_list' , methods: ['GET'])]
    public function index():JsonResponse
    {
        $unit = $this->unitRepository->findAll();

        return $this->json($unit, 200, [], [
            'groups' => ['unit:read']
        ]);
    }
}