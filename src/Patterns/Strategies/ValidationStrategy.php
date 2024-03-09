<?php

namespace processmaker\utils\Patterns\Strategies;

interface ValidationStrategy
{
    public function validate($value, array $rule = null);
}

