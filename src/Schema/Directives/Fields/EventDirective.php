<?php

namespace Nuwave\Lighthouse\Schema\Directives\Fields;

use Closure;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class EventDirective extends BaseDirective implements FieldMiddleware
{
    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name():string
    {
        return 'event';
    }

    /**
     * Resolve the field directive.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $value
     * @param  \Closure  $next
     *
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $value, Closure $next): FieldValue
    {
        $eventBaseName = $this->directiveArgValue('fire') ?? $this->directiveArgValue('class');
        $eventClassName = $this->namespaceClassName($eventBaseName);
        $resolver = $value->getResolver();

        return $next($value->setResolver(function () use ($resolver, $eventClassName) {
            $args = func_get_args();
            $value = call_user_func_array($resolver, $args);
            event(new $eventClassName($value));

            return $value;
        }));
    }
}
