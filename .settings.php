<?php
return [
    'services' => [
        'value' => [
            'itscript.rest.ModuleService' => [
                'className' => '\\Itscript\\Rest\\Services\\ModuleService',
            ],
            'itscript.rest.RoutService' => [
                'className' => '\\Itscript\\Rest\\Services\\RoutService',
            ],
            'itscript.rest.Middleware' => [
                'className' => '\\Itscript\\Rest\\Services\\Middleware',
            ],
            'itscript.rest.ControllerService' => [
                'className' => '\\Itscript\\Rest\\Services\\ControllerService',
            ],
        ],
        'readonly' => true,
    ]
];