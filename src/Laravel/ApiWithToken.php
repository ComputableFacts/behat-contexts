<?php namespace ComputableFacts\Behat\Context\Laravel;

use App\User;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Illuminate\Support\Str;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait ApiWithToken
{
    private $apiToken = null;
    private $useToken = false;

    /**
     * @When I request :path with my api token using HTTP :method
     *
     * @param $path
     * @param $method
     */
    public function iRequestWithMyApiTokenUsingHttpPost($path, $method)
    {
        $this->setRequestMethod($method);
        $this->iRequestWithMyApiToken($path);
    }

    /**
     * @When I request :path with my api token
     */
    public function iRequestWithMyApiToken($path)
    {
        $this->useToken = true;
        $this->sendRequest($path);
        $this->useToken = false;
    }

    /**
     * @param $path
     * @return string
     * @throws AssertionFailedException
     */
    protected function getApiUrl($path): string
    {
        Assertion::true(method_exists($this, 'getApiPath'),
            'ApiWithToken trait must be used in ComputableFacts\Behat\Context\Laravel\ApiContext');

        $tokenQueryString = (Str::contains($path, '?') ? '&' : '?') . 'api_token=' . $this->apiToken;

        return $this->getApiPath() . $path . ($this->useToken ? $tokenQueryString : '');
    }


}
