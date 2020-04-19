<?php

namespace App\Models\Enum;

use BenSampo\Enum\Enum;

/**
 * @method static self ON_START_TESTS()
 * @method static self NO_ERROR()
 */
class TestTypesEnum extends Enum
{
    const ON_START_TESTS = "on-start-test";
    const NO_ERROR = "no-error";
}
