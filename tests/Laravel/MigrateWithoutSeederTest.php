<?php

use ComputableFacts\BehatContexts\Laravel\MigrateWithoutSeeder;
use Illuminate\Support\Facades\Artisan;


/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
class MigrateWithoutSeederTest extends Orchestra\Testbench\TestCase
{

    /**
     * @throws ReflectionException
     */
    public function testMigrateShouldCallArtisanMigrateFresh()
    {
        $mock = $this->getMockForTrait(MigrateWithoutSeeder::class);

        Artisan::shouldReceive('call')
            ->once()
            ->with('migrate:fresh', Mockery::andAnyOtherArgs())
            ->andReturn(0);

        $mock->migrate();
    }

}
