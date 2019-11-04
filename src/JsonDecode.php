<?php namespace ComputableFacts\Behat\Context;

use InvalidArgumentException;

/**
 * @copyright 2019 ComputableFacts
 * @license Apache 2.0
 * @author Patrick Brisacier
 */
trait JsonDecode
{
    /**
     * Convert some variable to a JSON-array
     *
     * @param string $value The value to decode
     * @param string $errorMessage Optional error message
     * @return array
     * @throws InvalidArgumentException
     */
    protected function jsonDecode($value, $errorMessage = null)
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
