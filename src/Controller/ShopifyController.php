<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ShopifyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/shopify', name: 'api_shopify_')]
class ShopifyController extends AbstractController
{
    public function __construct(
        private readonly ShopifyClient $shopifyClient
    ) {
    }

    #[Route('/shop', name: 'shop', methods: ['GET'])]
    public function getShopInfo(): JsonResponse
    {
        try {
            $data = $this->shopifyClient->getShopInfo();
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/products', name: 'products_list', methods: ['GET'])]
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $first = (int) $request->query->get('first', 10);
            $after = $request->query->get('after');

            $data = $this->shopifyClient->getProducts($first, $after);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/products/{productId}', name: 'product_detail', methods: ['GET'])]
    public function getProduct(string $productId): JsonResponse
    {
        try {
            // Shopify GraphQL IDs need gid:// prefix if not provided
            if (!str_starts_with($productId, 'gid://')) {
                $productId = "gid://shopify/Product/$productId";
            }

            $data = $this->shopifyClient->getProduct($productId);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/orders', name: 'orders_list', methods: ['GET'])]
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $first = (int) $request->query->get('first', 10);
            $after = $request->query->get('after');

            $data = $this->shopifyClient->getOrders($first, $after);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/customers', name: 'customers_list', methods: ['GET'])]
    public function getCustomers(Request $request): JsonResponse
    {
        try {
            $first = (int) $request->query->get('first', 10);
            $after = $request->query->get('after');

            $data = $this->shopifyClient->getCustomers($first, $after);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/graphql', name: 'graphql', methods: ['POST'])]
    public function executeGraphQL(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            
            if (!isset($payload['query'])) {
                return $this->json([
                    'error' => 'GraphQL query is required',
                ], 400);
            }

            $query = $payload['query'];
            $variables = $payload['variables'] ?? [];

            $data = $this->shopifyClient->query($query, $variables);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
