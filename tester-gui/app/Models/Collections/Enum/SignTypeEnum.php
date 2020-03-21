<?php

namespace App\Models\Collections;

use BenSampo\Enum\Enum;

/**
 * @method static self INJECT_SCRIPT()
 * @method static self INSERT_SCRIPT()
 * @method static self APPEND_SCRIPT()
 * @method static self INSERT_BEFORE_LEFT_BRACE_SCRIPT()
 * @method static self INSERT_BEFORE_LEFT_BRACE_LESS_SCRIPT()
 * @method static self APPEND_CHILD_LEFT_BRACE_SCRIPT()
 * @method static self APPEND_CHILD_LEFT_BRACE_LESS_SIGN_SCRIPT()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE()
 */
class SignTypeEnum extends Enum
{
    const INJECT_SCRIPT = "injectScript";
    const INSERT_SCRIPT = "insertScript";
    const APPEND_SCRIPT = "appendScript";
    const INSERT_BEFORE_LEFT_BRACE_SCRIPT = "insertBefore(script";
    const INSERT_BEFORE_LEFT_BRACE_LESS_SCRIPT = "insertBefore(<script";
    const APPEND_CHILD_LEFT_BRACE_SCRIPT = "appendChild(script";
    const APPEND_CHILD_LEFT_BRACE_LESS_SIGN_SCRIPT = "appendChild(<script";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING = "document.createElement('script')";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE = "document.createElement(script)";
}
