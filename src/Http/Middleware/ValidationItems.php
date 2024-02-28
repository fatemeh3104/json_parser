<?php

namespace ProcessMaker\Package\Parssconfig\Http\Middleware;

use Closure;
use ProcessMaker\Models\FormalExpression;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;
use ProcessMaker\Package\Parssconfig\Models\ItemsValidation;
use ProcessMaker\Package\Parssconfig\Models\ScreenItems;
use processmaker\parssconfig\Patterns\Strategies\RequiredValidation;
use ProcessMaker\WebServices\ExpressionEvaluator;

class ValidationItems
{
    public function handle($request, Closure $next)
    {

        $task = $request->task;
        $screenVersion = $task->getScreenVersion();
        $task->screen = $screenVersion ? $screenVersion->toArray() : null;
        $screen = $task->screen;
        $screen_items = ScreenItems::where('screen_id', '=', $screen['screen_id'])->get();
        foreach ($screen_items as $screen_item) {
            if (isset($screen_item['conditionalHide'])){
                if (!ExpressionEvaluator::evaluate('feel',$screen_item['conditionalHide'],[])){
                    break;
                }
            }
            $items_validation = ItemsValidation::where('screen_item_id', '=', $screen_item['id'])->get();
            if (isset($items_validation[0])) {
                foreach ($request->data as $key => $data) {
                    if ($key == $screen_item['name']) {
                        foreach ($items_validation as $item_validation) {
                            switch ($item_validation['type']) {
                                case "required":
                                    if ($this->RequiredValidation($data) != null)
                                        return $this->RequiredValidation($data);
                                    break;

                                case "max:":
                                    if ($this->MaxValidation($data, $item_validation['validation']))
                                        return $this->MaxValidation($data, $item_validation['validation']);
                                    break;

                                case "min:":
                                    if ($this->MinValidation($data, $item_validation['validation']))
                                        return $this->MinValidation($data, $item_validation['validation']);
                                    break;
                                case "alpha":
                                    if ($this->AlphaValidation($data, $item_validation['validation']))
                                        return $this->AlphaValidation($data, $item_validation['validation']);
                                    break;
                                case "between:":
                                    if ($this->BetweenValidation($data, $item_validation['validation']))
                                        return $this->BetweenValidation($data, $item_validation['validation']);
                                    break;
                            }
                        }
                    }
                }
            }
        }
        echo ("ok");
        dd(4444);
        return $next($request);

    }

    public function RequiredValidation($value)
    {
        if ($value == null) {
            return response()->json('not valid (required) ', 422);
        }
    }

    public function AlphaValidation($value)
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

    public function MaxValidation($value, $rule)
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

    public function MinValidation($value, $rule)
    {
        if (gettype($value) == 'array') {
            foreach ($value as $item) {
                $charCount = mb_strlen($item, 'UTF-8');
                if ($charCount < $rule[0]['value']) {
                    return response()->json('not valid (MinValidation) ', 422);
                }
            }
        } else {
            $charCount = mb_strlen($value, 'UTF-8');
            if ($charCount < $rule[0]['value']) {
                return response()->json('not valid ', 422);
            }
        }

    }

    public function BetweenValidation($value, $rule)
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
