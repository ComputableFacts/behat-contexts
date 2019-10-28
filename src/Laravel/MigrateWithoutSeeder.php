<?php namespace ComputableFacts\BehatContexts\Laravel;

use Illuminate\Support\Facades\Artisan;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait MigrateWithoutSeeder
{

    /**
     * Migrate the database before each suite.
     *
     * @BeforeSuite
     */
    public static function migrate()
    {
        Artisan::call('migrate:fresh', [
            '--step' => true,
        ]);
    }
}
