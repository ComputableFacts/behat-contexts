<?php namespace ComputableFacts\Behat\Context\Laravel;

use App\User;
use Illuminate\Support\Facades\Auth;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait ApiWithTokenFromUsers
{
    /**
     * @Given I am connected with user email :email
     *
     * @param $email
     */
    public function iAmConnectedWithUserEmail($email)
    {
        $user = User::whereEmail($email)->firstOrFail();
        $token = $user->tokens->first();
        Auth::setUser($user);

        $this->apiToken = $token->token;
    }

}
