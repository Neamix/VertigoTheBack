<?php 

namespace App\GraphQL\Directives;

use Closure;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class AuthorizedDirective implements FieldMiddleware
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        directive @authorized on INPUT_FIELD_DEFINITION
        GRAPHQL;
    }

    public function handleField(FieldValue $fieldValue, Closure $next)
    {
        if ( Auth::check() ) {
            if (  Auth::user()->is_suspend ) {
                abort(403,"FORBIDDEN");
            }
        }
        return $next($fieldValue);
    }
}