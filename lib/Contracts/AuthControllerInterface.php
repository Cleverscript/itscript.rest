<?php

namespace Itscript\Rest\Contracts;

interface AuthControllerInterface
{
    public function getToken(): array;
}
