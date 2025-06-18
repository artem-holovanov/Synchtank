<?php
/**
 * Created by Artem Holovanov.
 * Date: 18.06.2025 20:28.
 */

declare(strict_types=1);

namespace Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrackControllerTest extends WebTestCase
{
    public function testCreateTrack(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/tracks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Test Track',
            'artist' => 'Test Artist',
            'duration' => 300,
            'isrc' => 'US-ABC-24-12345',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('Test Track', $data['title']);
    }

    public function testListTracks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tracks');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testUpdateTrack(): void
    {
        $client = static::createClient();

        // Create a new track first
        $client->request('POST', '/api/tracks', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Original Title',
            'artist' => 'Original Artist',
            'duration' => 240,
        ]));

        $this->assertResponseStatusCodeSame(201);
        $created = json_decode($client->getResponse()->getContent(), true);
        $id = $created['id'];

        // Update it
        $client->request('PUT', "/api/tracks/$id", [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => 'Updated Title',
            'artist' => 'Updated Artist',
            'duration' => 180,
        ]));

        $this->assertResponseIsSuccessful();

        $updated = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated Title', $updated['title']);
    }
}