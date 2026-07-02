<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Главная страница — публичный лендинг.
     */
    public function test_the_application_shows_public_home_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Сокращайте ссылки');
    }
}
