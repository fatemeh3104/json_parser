<?php

namespace ProcessMaker\Package\Utils\Patterns\Strategies;


use Illuminate\Support\Arr;
use processmaker\utils\Patterns\Strategies\ValidationStrategy;

class BetweenValidation implements ValidationStrategy
{
    public function validate($value,Array $rule=null)
    {
        $min = $rule[0]['value'];
        $max = $rule[1]['value'];
        if (gettype($value) == 'array') {
            foreach ($value as $item) {

                if ($value < $min || $value > $max) {
                    return response()->json('not valid (BetweenValidation)', 422);
                }
            }
        } else {

            if ($value < $min || $value > $max) {
                return response()->json('not valid (BetweenValidation) ', 422);
            }
        }
    }
}
