<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Indent size
    |---------------------------------------------------------------------------
    |
    | The number of spaces in an indent level
    |
    */
    'indent_size' => 4,

    /*
    |---------------------------------------------------------------------------
    | HTML self closing tags
    |---------------------------------------------------------------------------
    |
    | Indentation level won't increase after these tags.
    |
    */
    'self_closing_tags' => [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img',
        'input', 'link', 'meta', 'param', 'source', 'track', 'wbr',
    ],

    /*
    |---------------------------------------------------------------------------
    | Blade self closing directives
    |---------------------------------------------------------------------------
    |
    | Indentation level won't increase after these directives.
    |
    */
    'self_closing_directives' => [
        'extends', 'include', 'includeIf', 'includeWhen',
        'includeFirst', 'parent', 'yield', 'json',
        'case', 'break', 'continue', 'csrf',
        'each', 'inject',
    ],

    /*
    |---------------------------------------------------------------------------
    | Blade closing directives
    |---------------------------------------------------------------------------
    |
    | The "end" version of any directive is supported (for instance
    | "@endsection" for "@section" directive).
    |
    | But some directives can also be closed by special directives, like  "@show"
    | for "@section" directive.
    |
    | Define here the mapping of these behaviours (without "@" character).
    |
    | Examples:
    |
    | 'closing_directives' => [
    |     'section' => 'show',
    |     'opening_directive_2' => ['closing_directive_21', 'closing_directive_22'],
    | ],
    |
    */
    'closing_directives' => [
        'section' => 'show',
    ],

    /*
    |---------------------------------------------------------------------------
    | Blade "else" directives
    |---------------------------------------------------------------------------
    |
    | "Else" directive will be indented one level down, but previous indent
    | level will be preserved on following line.
    |
    */
    'else_directives' => [
        'else', 'elseif', 'empty',
    ],

];
