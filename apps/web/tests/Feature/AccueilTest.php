<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AccueilTest extends TestCase
{
    public function test_la_page_d_accueil_repond_et_rend_la_page_inertia(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('Accueil')
                    ->has('version')
            );
    }
}
