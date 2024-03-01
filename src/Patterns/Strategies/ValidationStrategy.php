<?php

namespace processmaker\parssconfig\Patterns\Strategies;

interface ValidationStrategy
{
    public function validate($value,array $rule);
}

