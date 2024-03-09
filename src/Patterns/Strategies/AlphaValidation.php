<?php

namespace ProcessMaker\Package\Utils\Patterns\Strategies;


use processmaker\utils\Patterns\Strategies\ValidationStrategy;

class AlphaValidation implements ValidationStrategy
{
    public function validate($value, $rule = null)
    {
        if (gettype($value) == 'array') {
            foreach ($value as $item) {
                if (!ctype_alpha($item)) {
                    return response()->json('not valid (AlphaValidation)', 422);
                }
            }
        } else {
            if (!ctype_alpha($value)) {
                return response()->json('not valid (AlphaValidation)', 422);
            }
        }

    }
}
