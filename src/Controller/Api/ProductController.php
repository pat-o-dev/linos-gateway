<?php

namespace App\Controller\Api;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api.products.list', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) min($request->query->get('limit', 10), 20);
        $data = $productRepository->findAllPaginated($page, $limit);

        return $this->json([
            'data' => $data, 
            'meta' => [
                'page' => $page, 
                'itemPerPage' => $limit,
                'totalItems' => $data->count(), 
                'totalPages' => ceil($data->count() / $limit)
                ]
        ], 200, [], ['groups' => ['product:list']]);
    }

    #[Route('/api/products/{id}', name: 'api.products.view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function view(ProductRepository $products, int $id): JsonResponse
    {
        $data = $products->find($id);
        return $this->json([
            'data' => $data
        ], 200, [], ['groups' => ['product:item']]);
    }

    #[Route('/api/products/slug/{slug}', name: 'api.products.slug.view', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+'])]
    public function slugView(ProductRepository $products, string $slug): JsonResponse
    {
        $data = $products->findOneBy(['slug' => $slug]);
        return $this->json([
            'data' => $data
        ], 200, [], ['groups' => ['product:item']]);
    }
}
