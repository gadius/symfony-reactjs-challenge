<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SumControllerTest extends WebTestCase
{
    public function testSumWithValidData(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => 5, 'b' => 3])
        );
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(8, $data['sum']);
    }

    public function testSumWithNegativeNumbers(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => -5, 'b' => 3])
        );
        
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(-2, $data['sum']);
    }

    public function testSumWithDecimals(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => 2.5, 'b' => 3.7])
        );
        
        $this->assertResponseIsSuccessful();
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(6.2, $data['sum']);
    }

    public function testSumWithMissingFieldA(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['b' => 3])
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSumWithMissingFieldB(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => 5])
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSumWithNonNumericFieldA(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => 'not-a-number', 'b' => 3])
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSumWithNonNumericFieldB(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['a' => 5, 'b' => 'invalid'])
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSumWithInvalidJson(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'invalid-json'
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSumWithEmptyBody(): void
    {
        $client = static::createClient();
        
        $client->request(
            'POST',
            '/api/sum',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            ''
        );
        
        $this->assertResponseStatusCodeSame(400);
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
