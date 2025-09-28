<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use App\Repository\PageRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PageController extends AbstractController
{
    #[Route('/api/pages/slug/{slug}', name: 'api.pages.slug.view', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+'])]
    public function slugView(PageRepository $pages, string $slug): JsonResponse
    {
        $data = $pages->findOneBy(['slug' => $slug]);
        return $this->json([
            'pages' => $data
        ], 200, [], ['groups' => ['page:item']]);
    }
}
