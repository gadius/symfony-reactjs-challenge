<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ShopifyClient
{
    private string $shopDomain;
    private string $accessToken;
    private string $apiVersion;
    private string $graphqlEndpoint;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        string $shopifyShopDomain,
        string $shopifyAccessToken,
        string $shopifyApiVersion
    ) {
        $this->shopDomain = $shopifyShopDomain;
        $this->accessToken = $shopifyAccessToken;
        $this->apiVersion = $shopifyApiVersion;
        $this->graphqlEndpoint = sprintf(
            'https://%s/admin/api/%s/graphql.json',
            $this->shopDomain,
            $this->apiVersion
        );
    }

    /**
     * Execute a GraphQL query against Shopify Admin API
     */
    public function query(string $query, array $variables = []): array
    {
        try {
            $payload = ['query' => $query];
            
            // Only include variables if not empty
            if (!empty($variables)) {
                $payload['variables'] = $variables;
            }
            
            $response = $this->httpClient->request('POST', $this->graphqlEndpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Shopify-Access-Token' => $this->accessToken,
                ],
                'json' => $payload,
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            // Try to get response body for more details
            $errorMessage = $e->getMessage();
            if (method_exists($e, 'getResponse')) {
                try {
                    $responseBody = $e->getResponse()->getContent(false);
                    $errorMessage .= ' | Response: ' . $responseBody;
                } catch (\Exception $innerE) {
                    // Ignore
                }
            }
            
            throw new \RuntimeException(
                sprintf('Shopify API error: %s', $errorMessage),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Fetch products with pagination
     */
    public function getProducts(int $first = 10, ?string $after = null): array
    {
        $afterParam = $after ? sprintf(', after: "%s"', $after) : '';
        
        $query = <<<GRAPHQL
        {
            products(first: $first$afterParam) {
                edges {
                    cursor
                    node {
                        id
                        title
                        handle
                        status
                        totalInventory
                        createdAt
                        updatedAt
                        priceRangeV2 {
                            minVariantPrice {
                                amount
                                currencyCode
                            }
                            maxVariantPrice {
                                amount
                                currencyCode
                            }
                        }
                        variants(first: 5) {
                            edges {
                                node {
                                    id
                                    title
                                    sku
                                    price
                                    inventoryQuantity
                                }
                            }
                        }
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                }
            }
        }
        GRAPHQL;

        return $this->query($query);
    }

    /**
     * Fetch a single product by ID
     */
    public function getProduct(string $productId): array
    {
        $query = <<<GRAPHQL
        query getProduct(\$id: ID!) {
            product(id: \$id) {
                id
                title
                description
                handle
                status
                totalInventory
                createdAt
                updatedAt
                priceRangeV2 {
                    minVariantPrice {
                        amount
                        currencyCode
                    }
                    maxVariantPrice {
                        amount
                        currencyCode
                    }
                }
                variants(first: 50) {
                    edges {
                        node {
                            id
                            title
                            sku
                            price
                            inventoryQuantity
                        }
                    }
                }
            }
        }
        GRAPHQL;

        return $this->query($query, ['id' => $productId]);
    }

    /**
     * Fetch orders with pagination
     */
    public function getOrders(int $first = 10, ?string $after = null): array
    {
        $afterParam = $after ? sprintf(', after: "%s"', $after) : '';
        
        $query = <<<GRAPHQL
        {
            orders(first: $first$afterParam) {
                edges {
                    cursor
                    node {
                        id
                        name
                        email
                        createdAt
                        updatedAt
                        totalPriceSet {
                            shopMoney {
                                amount
                                currencyCode
                            }
                        }
                        displayFulfillmentStatus
                        displayFinancialStatus
                        lineItems(first: 50) {
                            edges {
                                node {
                                    id
                                    title
                                    quantity
                                    originalUnitPriceSet {
                                        shopMoney {
                                            amount
                                            currencyCode
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                }
            }
        }
        GRAPHQL;

        return $this->query($query);
    }

    /**
     * Fetch customers with pagination
     */
    public function getCustomers(int $first = 10, ?string $after = null): array
    {
        $afterParam = $after ? sprintf(', after: "%s"', $after) : '';
        
        $query = <<<GRAPHQL
        {
            customers(first: $first$afterParam) {
                edges {
                    cursor
                    node {
                        id
                        firstName
                        lastName
                        email
                        phone
                        state
                        createdAt
                        updatedAt
                        numberOfOrders
                    }
                }
                pageInfo {
                    hasNextPage
                    hasPreviousPage
                }
            }
        }
        GRAPHQL;

        return $this->query($query);
    }

    /**
     * Get shop information
     */
    public function getShopInfo(): array
    {
        $query = <<<GRAPHQL
        {
            shop {
                id
                name
                email
                myshopifyDomain
                plan {
                    displayName
                }
                currencyCode
                timezoneAbbreviation
            }
        }
        GRAPHQL;

        return $this->query($query);
    }
}
