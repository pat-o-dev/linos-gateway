<?php

namespace App\Controller\Api;

use App\Service\Prestashop\CustomerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CustomerController extends AbstractController
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService,)
    {
        $this->customerService = $customerService;
    }

    #[Route('/api/customers', name: 'api.customers.index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $customers = [];
        return $this->json([
            'customers' => $customers,
        ], 200, [], []);
    }

    #[Route('/api/customers/create', name: 'api.customers.create', methods: ['GET'])]
    public function create(Request $request): JsonResponse 
    {
        $customers = [];
        return $this->json([
            'customers' => $customers,
        ], 200, [], []);
    }

    #[Route('/api/customers/connect', name: 'api.customers.connect', methods: ['POST'])]
    public function connect(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ??  '';
        $password = $data['password'] ?? '';

        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password required'], 400);
        }

        $customer = $this->customerService->authenticate($email, $password);

        if (!$customer) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->json($customer);
    }
}
