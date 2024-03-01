<?php

namespace processmaker\parssconfig\Patterns\Strategies;


class RequiredValidation implements ValidationStrategy
{
    public function validate($value,$rule='')
    {
        if ($value == null) {
            return response()->json('not valid (required) ', 422);
        }

    }
}
