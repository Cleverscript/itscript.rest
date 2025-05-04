<?php

namespace Itscript\Rest\Controllers;

use Itscript\Rest\Contracts\BaseControllerInterface;

class UserController extends BaseController implements BaseControllerInterface
{
    public function show(int $id)
    {
        $obj = new class {
            public string $name = "Bob Dylan";
            public int $age = 70;
        };

        $obj->id = $id;

        return $obj;
    }
}
