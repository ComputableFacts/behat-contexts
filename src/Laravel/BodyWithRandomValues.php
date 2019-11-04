<?php namespace ComputableFacts\Behat\Context\Laravel;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Behat\Gherkin\Node\PyStringNode;
use Illuminate\Support\Str;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait BodyWithRandomValues
{

    /** @var string $lastRandomText */
    private $lastRandomText;

    /**
     * @Given the request body with random values is:
     *
     * @param PyStringNode $string
     * @throws AssertionFailedException
     */
    public function theRequestBodyWithRandomValuesIs(PyStringNode $string)
    {
        Assertion::true(method_exists($this, 'setRequestBody'),
            'BodyWithRandomValues trait must be used in ComputableFacts\Behat\Context\Laravel\ApiContext');

        $pattern = '/{RandomText}/';
        $this->lastRandomText = Str::random(15);
        echo 'New RandomText: ' . $this->lastRandomText;

        $inputLines = $string->getStrings();
        $outputLines = preg_replace($pattern, $this->lastRandomText, $inputLines);

        $this->setRequestBody(new PyStringNode($outputLines, $string->getLine()));
    }

    /**
     * @Then the response body with random values contains JSON:
     *
     * @param PyStringNode $string
     * @throws AssertionFailedException
     */
    public function theResponseBodyWithRandomValuesContainsJson(PyStringNode $string)
    {
        Assertion::true(method_exists($this, 'assertResponseBodyContainsJson'),
            'BodyWithRandomValues trait must be used in ComputableFacts\Behat\Context\Laravel\ApiContext');

        $pattern = '/{LastRandomText}/';
        echo 'LastRandomText: ' . $this->lastRandomText;

        $inputLines = $string->getStrings();
        $outputLines = preg_replace($pattern, $this->lastRandomText, $inputLines);

        $this->assertResponseBodyContainsJson(new PyStringNode($outputLines, $string->getLine()));
    }
}
