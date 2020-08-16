<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Server;
/**
 * Description of ServerListingTest
 *
 * @author ramkumar
 */
class ServerListingTest extends ApiTestCase
{
    public function testGetServerCollection(): void
    {   
        $response = static::createClient()->request('GET', '/api/servers');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            "@context"=> "/api/contexts/Server",
            "@id" => "/api/servers",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 486,
            "hydra:view" => [
                "@id" => "/api/servers?_page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/servers?_page=1",
                "hydra:last" => "/api/servers?_page=49",
                "hydra:next" => "/api/servers?_page=2"
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(10, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Server::class);
    }
    
    public function testGetServerFilterCollection(): void
    {   
        $response = static::createClient()->request('GET', '/api/servers?_page=22&ram=16,64');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            "@context"=> "/api/contexts/Server",
            "@id" => "/api/servers",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 217,
            "hydra:view" => [
                "@id" => "/api/servers?ram=16%2C64&_page=22",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/servers?ram=16%2C64&_page=1",
                "hydra:last" => "/api/servers?ram=16%2C64&_page=22",
                "hydra:previous" => "/api/servers?ram=16%2C64&_page=21"
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(7, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Server::class);
    }
}