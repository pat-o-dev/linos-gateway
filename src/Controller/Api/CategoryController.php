<?php

namespace App\Controller\Api;


use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    #[Route('/api/categories', name: 'api.categories.list', methods: ['GET'])]
    public function index(Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository): JsonResponse
    {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) min($request->query->get('limit', 10), 20);

        $data = $categoryRepository->findAllPaginated($page, $limit);

        $categories = iterator_to_array($data);
        foreach ($categories as $category) {
            // Force loading of products for each category
            $products = iterator_to_array($productRepository->findByCategoryPaginated($category, 1, 12));
            $category->setProducts(new \Doctrine\Common\Collections\ArrayCollection($products));
        }

        return $this->json([
            'categories' => $categories,
            'meta' => [
                'page' => $page,
                'itemPerPage' => $limit,
                'totalItems' => $data->count(),
                'totalPages' => ceil($data->count() / $limit)
            ]
        ], 200, [], ['groups' => ['category:list']]);
    }

    #[Route('/api/categories/tree', name: 'api.categories.tree', methods: ['GET'])]
    public function tree(Request $request, CategoryRepository $categories): JsonResponse
    {
        $depth = (int) $request->query->get('depth', 1);

        $data = $categories->createQueryBuilder('c')
            ->where('c.depth = :depth')
            ->setParameter('depth', $depth)
            ->orderBy('c.parent', 'ASC')
            ->addOrderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->json([
            'categories' => $data
        ], 200, [], ['groups' => ['category:item', 'category:list', 'category:tree']]);
    }

    #[Route('/api/categories/{id}', name: 'api.categories.view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function view(CategoryRepository $categories, int $id): JsonResponse
    {
        $data = $categories->find($id);
        return $this->json([
            'categories' => $data
        ], 200, [], ['groups' => ['category:item', 'category:list']]);
    }

    #[Route('/api/categories/slug/{slug}', name: 'api.categories.slug.view', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+'])]
    public function slugView(CategoryRepository $categories, string $slug): JsonResponse
    {
        $data = $categories->findOneBy(['slug' => $slug]);
        return $this->json([
            'categories' => $data
        ], 200, [], ['groups' => ['category:item', 'category:list']]);
    }
}
