<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Les tests ne dépendent pas des assets compilés. Sans cela, la suite
        // ne passe que si `npm run build` a déjà tourné : elle est verte sur
        // le poste du développeur et rouge en intégration continue, ce qui est
        // le pire des deux mondes.
        $this->withoutVite();
    }
}
