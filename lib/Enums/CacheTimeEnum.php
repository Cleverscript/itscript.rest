<?php

namespace Itscript\Rest\Enums;

class CacheTimeEnum
{
    public const MINUTE = 60; //60
    public const HOUR = self::MINUTE * 60; //60 * 60 = 3600
    public const DAY = self::HOUR * 24; //3600 * 24 = 86400
    public const WEEK = self::DAY * 7; //86400 * 7 = 604800
    public const MONTH = self::DAY * 31; //86400 * 31 = 2678400
    public const YEAR = self::DAY * 365; //86400 * 365 = 31536000
}