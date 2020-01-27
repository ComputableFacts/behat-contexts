<?php namespace ComputableFacts\Behat\Context\Laravel;

use Assert\Assertion;
use Assert\AssertionFailedException as AssertionFailure;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\MinkContext;
use ComputableFacts\Behat\Context\JsonDecode;
use Illuminate\Support\Str;
use Imbo\BehatApiExtension\Exception\AssertionFailedException;
use Imbo\BehatApiExtension\ArrayContainsComparator;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayMaxLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayMinLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\GreaterThan;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\JWT;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\LessThan;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\RegExp;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\VariableType;
use InvalidArgumentException;
use stdClass;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
class ApiContext extends MinkContext
{
    use JsonDecode;

    private $apiPathRoot = '/api/';
    private $apiVersion = 'v1';

    private $requestMethod;
    private $serverParameters;
    private $requestBody;

    private $printResponse = false;

    /** @var ArrayContainsComparator $arrayContainsComparator */
    private $arrayContainsComparator = null;

    /**
     * Create and init ArrayComparator
     *
     * @beforeScenario
     */
    public function createArrayComparator()
    {
        if (is_null($this->arrayContainsComparator)) {
            $comparator = new ArrayContainsComparator();
            $comparator
                ->addFunction('arrayLength', new ArrayLength())
                ->addFunction('arrayMinLength', new ArrayMinLength())
                ->addFunction('arrayMaxLength', new ArrayMaxLength())
                ->addFunction('variableType', new VariableType())
                ->addFunction('regExp', new RegExp())
                ->addFunction('gt', new GreaterThan())
                ->addFunction('lt', new LessThan())
                ->addFunction('jwt', new JWT($comparator));

            $this->arrayContainsComparator = $comparator;
        }
    }

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param bool $printResponse
     */
    public function __construct($printResponse = false)
    {
        $this->printResponse = $printResponse;
        $this->requestMethod = 'GET';
        $this->serverParameters = array();
        $this->requestBody = null;
    }

    /**
     * apiPath getter
     * @return string
     */
    public function getApiPath()
    {
        return Str::finish($this->apiPathRoot, '/') .
            Str::finish($this->apiVersion, '/');
    }

    /**
     * apiVersion setter
     * @param string $newVersion
     */
    public function setApiVersion(string $newVersion)
    {
        $this->apiVersion = $newVersion;
    }

    /**
     * Set the request method
     *
     * @param string $method
     * @return self
     *
     * @Given the request method is :method
     */
    protected function setRequestMethod(string $method)
    {
        $this->requestMethod = (string)$method;

        return $this;
    }


    /**
     * TODO: Given I attach :path to the request as :partName
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-i-attach-path-to-the-request-as-partname
     */

    /**
     * TODO: Given the following multipart form parameters are set: <TableNode>
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-the-following-multipart-form-parameters-are-set-tablenode
     */

    /**
     * TODO: Given I am authenticating as :username with password :password
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-i-am-authenticating-as-username-with-password-password
     */

    /**
     * Set a HTTP request header
     *
     * If the header already exists it will be overwritten
     *
     * @param string $header The header name
     * @param string $value The header value
     * @return self
     *
     * @Given the :header request header is :value
     */
    public function setRequestHeader($header, $value): self
    {
        $this->getSession()->setRequestHeader($header, $value);

        $contentHeaders = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        $header = str_replace('-', '_', strtoupper($header));

        // CONTENT_* are not prefixed with HTTP_ in PHP when building $_SERVER
        if (!isset($contentHeaders[$header])) {
            $header = 'HTTP_' . $header;
        }

        $this->serverParameters[$header] = $value;


        return $this;
    }

    /**
     * TODO: Given the :header request header contains :value
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-the-header-request-header-contains-value
     */

    /**
     * TODO: Given the following form parameters are set: <TableNode>
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-the-following-form-parameters-are-set-tablenode
     */

    /**
     * Set the request body to a string
     *
     * @param resource|string|PyStringNode $body The content to set as the request body
     *
     * @return self
     *
     * @Given the request body is:
     */
    public function setRequestBody(PyStringNode $body): self
    {
        $this->requestBody = (string)$body;

        return $this;
    }

    /**
     * TODO: Given the request body contains :path
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-the-request-body-contains-path
     */

    /**
     * TODO: Given the response body contains a JWT identified by :name, signed with :secret: <PyStringNode>
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/setup-request.html#given-the-response-body-contains-a-jwt-identified-by-name-signed-with-secret-pystringnode
     */

    /**
     * @When I request :path
     * @When I request :path using HTTP :method
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/send-request.html#when-i-request-path
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/send-request.html#when-i-request-path-using-http-method
     *
     * @param string $path
     * @param string $method
     */
    public function requestPath(string $path, string $method = null) {
        if (null === $method) {
            $this->setRequestMethod('GET');
        } else {
            $this->setRequestMethod($method);
        }

        $this->sendRequest($path);
    }


    /**
     * @Then the response code is :code
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-code-is-code
     *
     * @param $code
     */
    public function theResponseCodeIs($code)
    {
        $this->assertResponseStatus($code);
    }


    /**
     * TODO: Then the response code is not :code
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-code-is-not-code
     */

    /**
     * TODO: Then the response reason phrase is :phrase
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-reason-phrase-is-phrase
     */

    /**
     * TODO: Then the response reason phrase is not :phrase
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-reason-phrase-is-not-phrase
     */

    /**
     * TODO: Then the response reason phrase matches :pattern
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-reason-phrase-matches-pattern
     */

    /**
     * TODO: Then the response status line is :line
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-status-line-is-line
     */

    /**
     * TODO: Then the response status line is not :line
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-status-line-is-not-line
     */

    /**
     * TODO: Then the response status line matches :pattern
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-status-line-matches-pattern
     */

    /**
     * TODO: Then the response is :group
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-is-group
     */

    /**
     * TODO: Then the response is not :group
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-is-not-group
     */

    /**
     * TODO: Then the :header response header exists
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-header-response-header-exists
     */

    /**
     * TODO: Then the :header response header does not exist
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-header-response-header-does-not-exist
     */

    /**
     * @Then the :headerName response header is :headerValue
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-header-response-header-is-value
     *
     * @param $headerName
     * @param $headerValue
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function theResponseHeaderIs($headerName, $headerValue)
    {
        $this->assertSession()->responseHeaderEquals($headerName, $headerValue);
    }

    /**
     * @Then the :headerName response header is int(eger) :headerValue
     *
     * @param $headerName
     * @param $headerValue
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function theResponseHeaderIsInt($headerName, $headerValue)
    {
        $this->assertSession()->responseHeaderEquals($headerName, (int)$headerValue);
    }

    /**
     * @Then the :headerName response header contains :headerValue
     *
     * @param $headerName
     * @param $headerValue
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function theResponseHeaderContains($headerName, $headerValue)
    {
        $this->assertSession()->responseHeaderContains($headerName, $headerValue);
    }

    /**
     * TODO: Then the :header response header is not :value
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-header-response-header-is-not-value
     */

    /**
     * TODO: Then the :header response header matches :pattern
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-header-response-header-matches-pattern
     */

    /**
     * TODO: Then the response body is an empty JSON object
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-an-empty-json-object
     */

    /**
     * TODO: Then the response body is an empty JSON array
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-an-empty-json-array
     */

    /**
     * TODO: Then the response body is a JSON array of length :length
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-a-json-array-of-length-length
     */

    /**
     * TODO: Then the response body is a JSON array with a length of at least :length
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-a-json-array-with-a-length-of-at-least-length
     */

    /**
     * TODO: Then the response body is a JSON array with a length of at most :length
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-a-json-array-with-a-length-of-at-most-length
     */

    /**
     * TODO: Then the response body is: <PyStringNode>
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-pystringnode
     */

    /**
     * TODO: Then the response body is not: <PyStringNode>
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-is-not-pystringnode
     */

    /**
     * @Then the response body matches:
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-matches-pystringnode
     *
     * Assert that the response body matches some content using a regular expression
     *
     * @param PyStringNode $pattern The regular expression pattern to use for the match
     * @return void
     *
     * @throws AssertionFailure
     * @throws AssertionFailedException
     */
    public function assertResponseBodyMatches(PyStringNode $pattern)
    {
        $this->requireResponse();
        $pattern = (string)$pattern;

        try {
            Assertion::regex($body = (string)$this->getSession()->getPage()->getContent(), $pattern, sprintf(
                'Expected response body to match regular expression "%s", got "%s".',
                $pattern,
                $body
            ));
        } catch (AssertionFailure $e) {
            throw new AssertionFailedException($e->getMessage());
        }
    }


    /**
     * @Then the response body contains JSON:
     * See: https://behat-api-extension.readthedocs.io/en/latest/guide/verify-server-response.html#then-the-response-body-contains-json-pystringnode
     *
     * Assert that the response body contains all keys / values in the parameter
     *
     * @param PyStringNode $contains
     * @return void
     * @throws \Imbo\BehatApiExtension\Exception\ArrayContainsComparatorException
     * @throws AssertionFailure
     * @throws AssertionFailedException
     */
    public function assertResponseBodyContainsJson(PyStringNode $contains)
    {
        $this->requireResponse();

        // Decode the parameter to the step as an array and make sure it's valid JSON
        $contains = $this->jsonDecode((string)$contains);

        // Get the decoded response body and make sure it's decoded to an array
        $body = json_decode(json_encode($this->getResponseBody()), true);

        try {
            // Compare the arrays, on error this will throw an exception
            Assertion::true($this->arrayContainsComparator->compare($contains, $body));
        } catch (AssertionFailure $e) {
            throw new AssertionFailedException(
                'Comparator did not return in a correct manner. Marking assertion as failed.'
            );
        }
    }

    /**
     * Get the JSON-encoded array or stdClass from the response body
     *
     * @return array|stdClass
     * @throws InvalidArgumentException
     */
    private function getResponseBody()
    {
        $body = json_decode((string)$this->getSession()->getPage()->getContent());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('The response body does not contain valid JSON data.');
        } else if (!is_array($body) && !($body instanceof stdClass)) {
            throw new InvalidArgumentException('The response body does not contain a valid JSON array / object.');
        }

        return $body;
    }

    /**
     * Require a response object
     *
     * @throws AssertionFailure
     */
    protected function requireResponse()
    {
        $client = $this->getSession()->getDriver()->getClient();
        $request = $client->getInternalRequest();
        Assertion::notNull($request, 'Unable to access the response before visiting a page');
    }

    /**
     * @param $path
     * @return self
     */
    protected function sendRequest($path)
    {
        /** @var Symfony\Component\BrowserKit\Client $client */
        $client = $this->getSession()->getDriver()->getClient();

        $client->request(
            $this->requestMethod, // The request method
            $this->getApiUrl($path), // The URI to fetch
            array(), // The Request parameters
            array(), // The files
            $this->serverParameters, // The server parameters (HTTP headers are referenced with a HTTP_ prefix as PHP does)
            $this->requestBody // The raw body data
        );

        if ($this->printResponse) {
            $this->printLastResponse();
            $this->printLastResponseHeaders();
        }

        return $this;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getApiUrl($path): string
    {
        return $this->getApiPath() . $path;
    }

    /**
     * Prints last response headers to console
     */
    private function printLastResponseHeaders()
    {
        echo "\n\n--=< Response Headers >=--\n";
        foreach ($this->getSession()->getResponseHeaders() as $headerKey => $headerValue) {
            echo $headerKey . ': ' . join("|", $headerValue) . "\n";
        }
    }

}