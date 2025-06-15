<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
	'GROUPS' => [
        'LIST' => [
            'NAME' => GetMessage('T_PARAMS_GRID'),
            'SORT' => 300
        ]
    ],
	'PARAMETERS' => [
        'NUM_PAGE' => [
            "PARENT" => "LIST",
            "NAME" => GetMessage("T_NUM_PAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => 10,
        ],
	],
    'CACHE_TIME' => ['DEFAULT' => 86400],
];