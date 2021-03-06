<?php

namespace App\Models\Collections\Enum;

use BenSampo\Enum\Enum;

/**
 * @method static self INJECT_SCRIPT_LEFT_BRACE()
 * @method static self INSERT_SCRIPT_LEFT_BRACE()
 * @method static self APPEND_SCRIPT_LEFT_BRACE()
 * @method static self INSERT_BEFORE_LEFT_BRACE_SCRIPT()
 * @method static self INSERT_BEFORE_LEFT_BRACE_SCRPT()
 * @method static self INSERT_BEFORE_LEFT_BRACE_SCRIPT_SPACE()
 * @method static self INSERT_BEFORE_LEFT_BRACE_SCRPT_SPACE()
 * @method static self APPEND_CHILD_LEFT_BRACE_SCRIPT()
 * @method static self APPEND_CHILD_LEFT_BRACE_SCRPT()
 * @method static self APPEND_CHILD_LEFT_BRACE_SCRIPT_SPACE()
 * @method static self APPEND_CHILD_LEFT_BRACE_SCRPT_SPACE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_DOUBLE_QUOTES()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRPT_VARIABLE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_SPACE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_DOUBLE_QUOTES_SPACE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE_SPACE()
 * @method static self DOCUMENT_CREATE_ELEMENT_SCRPT_VARIABLE_SPACE()
 */
class SignTypeEnum extends Enum
{
    const INJECT_SCRIPT_LEFT_BRACE = "injectScript(";
    const INSERT_SCRIPT_LEFT_BRACE = "insertScript(";
    const APPEND_SCRIPT_LEFT_BRACE = "appendScript(";
    const INSERT_BEFORE_LEFT_BRACE_SCRIPT = "insertBefore(script";
    const INSERT_BEFORE_LEFT_BRACE_SCRPT = "insertBefore(scrpt";
    const INSERT_BEFORE_LEFT_BRACE_SCRIPT_SPACE = "insertBefore( script";
    const INSERT_BEFORE_LEFT_BRACE_SCRPT_SPACE = "insertBefore( scrpt";
    const APPEND_CHILD_LEFT_BRACE_SCRIPT = "appendChild(script";
    const APPEND_CHILD_LEFT_BRACE_SCRPT = "appendChild(scrpt";
    const APPEND_CHILD_LEFT_BRACE_SCRIPT_SPACE = "appendChild( script";
    const APPEND_CHILD_LEFT_BRACE_SCRPT_SPACE = "appendChild( scrpt";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING = ".createElement('script')";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_DOUBLE_QUOTES = ".createElement(\"script\")";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE = ".createElement(script)";
    const DOCUMENT_CREATE_ELEMENT_SCRPT_VARIABLE = ".createElement(scrpt)";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_SPACE = ".createElement( 'script' )";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_STRING_DOUBLE_QUOTES_SPACE = ".createElement( \"script\" )";
    const DOCUMENT_CREATE_ELEMENT_SCRIPT_VARIABLE_SPACE = ".createElement( script )";
    const DOCUMENT_CREATE_ELEMENT_SCRPT_VARIABLE_SPACE = ".createElement( scrpt )";
}
