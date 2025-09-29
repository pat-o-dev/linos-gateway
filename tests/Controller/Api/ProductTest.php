<?php

namespace App\Tests\Controller\Api;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTest extends WebTestCase
{
    private EntityManagerInterface $em;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        $this->em->createQuery('DELETE FROM App\Entity\Product')->execute();
    }
    public function testProductWithoutSkuFails(): void
    {
        $product = new Product();
        $product->setTitle('Produit test')
            ->setSlug('produit-test')
            ->setPrice(9.99)
            ->setStatus("PUBLISHED");

        $this->em->persist($product);

        $this->expectException(\Doctrine\DBAL\Exception\NotNullConstraintViolationException::class);
        $this->em->flush();
    }

    public function testProductsEndpoint(): void
    {
        $this->client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testGetSingleProduct(): void
    {
        // Virtual Product
        $product = new Product();
        $product->setTitle('T-shirt')
            ->setSku('sku-5')
            ->setSlug('t-shirt')
            ->setPrice(19.99)
            ->setStatus("PUBLISHED");

        $this->assertSame('T-shirt', $product->getTitle(), 'Error Product Title');
        $this->assertSame('t-shirt', $product->getSlug(), 'Error Product Slug');
        $this->assertSame(19.99, $product->getPrice(), 'Error Product Price');

        $this->em->persist($product);
        $this->em->flush();
        $productId = $product->getId();
        // Call API
        $this->client->request('GET', '/api/products/' . $productId);
        // Check Content
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertSame($productId, $data['products']['id']);
        $this->assertSame('T-shirt', $data['products']['title']);
        $this->assertSame('t-shirt', $data['products']['slug']);
        $this->assertSame(19.99, $data['products']['price']);
    }
}
