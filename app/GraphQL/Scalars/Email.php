<?php

namespace App\GraphQL\Scalars;

use Exception;
use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\ScalarType;

/**
 * Read more about scalars here https://webonyx.github.io/graphql-php/type-definitions/scalars
 */
final class Email extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function serialize($value)
    {
        if ( ! filter_var($value,FILTER_VALIDATE_EMAIL))
            throw new InvariantViolation('Not Email:');

        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function parseValue($value)
    {
        // TODO implement validation

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * E.g.
     * {
     *   user(email: "user@example.com")
     * }
     *
     * @param  \GraphQL\Language\AST\Node  $valueNode
     * @param  array<string, mixed>|null  $variables
     * @return mixed
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ( ! filter_var($valueNode->value,FILTER_VALIDATE_EMAIL))
            throw new InvariantViolation('Not Email:');
            
        return $valueNode->value;
    }
}
