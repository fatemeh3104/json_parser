<?php

namespace ProcessMaker\Package\Parssconfig\Patterns\Strategies;


use processmaker\parssconfig\Patterns\Strategies\ValidationStrategy;

class MaxValidation implements ValidationStrategy
{
    public function validate($value, $rule=null)
    {
        if (gettype($value) == 'array') {
            foreach ($value as $item) {
                $charCount = mb_strlen($item, 'UTF-8');
                if ($charCount > $rule[0]['value']) {
                    return response()->json('not valid (MaxValidation) ', 422);
                }
            }
        } else {
            $charCount = mb_strlen($value, 'UTF-8');
            if ($charCount > $rule[0]['value']) {
                return response()->json('not valid ', 422);
            }
        }

    }
}
