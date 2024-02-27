<?php

namespace processmaker\Parssconfig\Patterns\Strategies;


class RequiredValidation implements ValidationStrategy
{
    public function validate($value)
    {
        if ($value!=null){
            return true;
        }else{
            return response()->json('not valid ', 422);
        }

    }
}
