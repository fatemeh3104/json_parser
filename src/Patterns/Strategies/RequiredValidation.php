<?php

namespace processmaker\utils\Patterns\Strategies;


class RequiredValidation implements ValidationStrategy
{
    public function validate($value, $rule = null)
    {
        if ($value == null) {
            return response()->json('not valid (required) ', 422);
        }

    }
}
