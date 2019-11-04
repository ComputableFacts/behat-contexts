<?php namespace ComputableFacts\BehatContexts;

use Assert\Assertion;
use Assert\AssertionFailedException as AssertionFailure;
use Behat\Gherkin\Node\PyStringNode;
use Imbo\BehatApiExtension\ArrayContainsComparator;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayMaxLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\ArrayMinLength;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\GreaterThan;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\JWT;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\LessThan;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\RegExp;
use Imbo\BehatApiExtension\ArrayContainsComparator\Matcher\VariableType;
use Imbo\BehatApiExtension\Exception\AssertionFailedException;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait RequestWithJson
{

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
     * Assert that the response body contains all keys / values in the parameter
     *
     * @param PyStringNode $contains
     * @throws AssertionFailedException
     * @throws \Imbo\BehatApiExtension\Exception\ArrayContainsComparatorException
     * @throws AssertionFailure
     * @return void
     *
     * @Then the response body contains JSON:
     */
    public function assertResponseBodyContainsJson(PyStringNode $contains)
    {
        $this->requireResponse();

        // Decode the parameter to the step as an array and make sure it's valid JSON
        $contains = $this->jsonDecode((string) $contains);

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
     * Convert some variable to a JSON-array
     *
     * @param string $value The value to decode
     * @param string $errorMessage Optional error message
     * @throws InvalidArgumentException
     * @return array
     */
    private function jsonDecode($value, $errorMessage = null)
    {
        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                $errorMessage ?: 'The supplied parameter is not a valid JSON object.'
            );
        }

        return $decoded;
    }
}
