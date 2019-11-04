<?php namespace ComputableFacts\Behat\Context\Laravel;

use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Token;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait ApiWithTokenFromAutomaticUserCreation
{
    /**
     * Create a user to have an API token
     *
     * @beforeScenario
     */
    public function apiWithTokenFromAutomaticUserCreationBeforeScenario(): void
    {
        $this->createApiToken();
    }

    /**
     * Create a user with a token for API
     * Change Laravel current user to this new user
     */
    private function createApiToken(): void
    {
        $apiUser = factory(User::class)->create();
        $token = factory(Token::class)->make();
        $apiUser->tokens()->save($token);
        Auth::setUser($apiUser);

        $this->apiToken = $token->token;
    }

}
