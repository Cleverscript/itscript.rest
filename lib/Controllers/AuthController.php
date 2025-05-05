<?php

namespace Itscript\Rest\Controllers;

use Itscript\Rest\Traits\JwtAuthTrait;
use Itscript\Rest\Contracts\BaseControllerInterface;
use Itscript\Rest\Contracts\AuthControllerInterface;

class AuthController extends BaseController implements BaseControllerInterface, AuthControllerInterface
{
    use JwtAuthTrait;
}

