<?php

namespace Itscript\Rest\Entities;

class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly string $controller,
        public readonly string $function,
        public readonly array $params = []
    )
    {

    }
}