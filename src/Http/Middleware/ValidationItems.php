<?php

namespace ProcessMaker\Package\Parssconfig\Http\Middleware;

use Closure;
use ProcessMaker\Models\FormalExpression;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;
use ProcessMaker\Package\Parssconfig\Models\ItemsValidation;
use ProcessMaker\Package\Parssconfig\Models\ScreenItems;
use ProcessMaker\Package\Parssconfig\Patterns\Strategies\AlphaValidation;
use ProcessMaker\Package\Parssconfig\Patterns\Strategies\BetweenValidation;
use ProcessMaker\Package\Parssconfig\Patterns\Strategies\MaxValidation;
use ProcessMaker\Package\Parssconfig\Patterns\Strategies\MinValidation;
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
        $items_name = [];
        foreach ($screen_items as $screen_item) {
            $items_name[] = $screen_item->name;
        }
        $items = $request->data;
        unset($items['_user']);
        unset($items['_request']);
        foreach ($items as $key => $value) {
            if (!(in_array($key, $items_name))) {
                return response()->json('not valid ' . $key . ' is not a item from this form ', 422);
            }
        }

        foreach ($screen_items as $screen_item) {
            if (isset($screen_item['conditionalHide'])) {
                if (!ExpressionEvaluator::evaluate('feel', $screen_item['conditionalHide'], [])) {
                    continue;
                }
            }
            $items_validation = ItemsValidation::where('screen_item_id', '=', $screen_item['id'])->get();
            if (isset($items_validation[0])) {
                foreach ($items as $key => $data) {
                    if ($key == $screen_item['name']) {
                        foreach ($items_validation as $item_validation) {
                            switch ($item_validation['type']) {
                                case "required":
                                    $validation = new RequiredValidation();
                                    if ($validation->validate($data) != null)
                                        return $validation->validate($data);
                                    break;

                                case "max:":
                                    $validation = new MaxValidation();
                                    if ($validation->validate($data, $item_validation['validation']))
                                        return $validation->validate($data, $item_validation['validation']);
                                    break;

                                case "min:":
                                    $validation = new MinValidation();
                                    if ($validation->validate($data, $item_validation['validation']))
                                        return $validation->validate($data, $item_validation['validation']);
                                    break;
                                case "alpha":

                                    $validation = new AlphaValidation();
                                    if ($validation->validate($data))
                                        return $validation->validate($data);
                                    break;
                                case "between:":

                                    $validation = new BetweenValidation();
                                    if ($validation->validate($data, $item_validation['validation']))
                                        return $validation->validate($data, $item_validation['validation']);
                                    break;
                            }
                        }
                    }

                }
            }
        }
        return $next($request);
    }

//    public function RequiredValidation($value)
//    {
//        if ($value == null) {
//            return response()->json('not valid (required) ', 422);
//        }
//    }
//
//    public function AlphaValidation($value)
//    {
//        if (gettype($value) == 'array') {
//            foreach ($value as $item) {
//                if (!ctype_alpha($item)) {
//                    return response()->json('not valid (AlphaValidation)', 422);
//                }
//            }
//        } else {
//            if (!ctype_alpha($value)) {
//                return response()->json('not valid (AlphaValidation)', 422);
//            }
//        }
//    }
//
//    public function MaxValidation($value, $rule)
//    {
//        if (gettype($value) == 'array') {
//            foreach ($value as $item) {
//                $charCount = mb_strlen($item, 'UTF-8');
//                if ($charCount > $rule[0]['value']) {
//                    return response()->json('not valid (MaxValidation) ', 422);
//                }
//            }
//        } else {
//            $charCount = mb_strlen($value, 'UTF-8');
//            if ($charCount > $rule[0]['value']) {
//                return response()->json('not valid ', 422);
//            }
//        }
//
//    }
//
//    public function MinValidation($value, $rule)
//    {
//        if (gettype($value) == 'array') {
//            foreach ($value as $item) {
//                $charCount = mb_strlen($item, 'UTF-8');
//                if ($charCount < $rule[0]['value']) {
//                    return response()->json('not valid (MinValidation) ', 422);
//                }
//            }
//        } else {
//            $charCount = mb_strlen($value, 'UTF-8');
//            if ($charCount < $rule[0]['value']) {
//                return response()->json('not valid ', 422);
//            }
//        }
//
//    }

//    public function BetweenValidation($value, $rule)
//    {
//        $min = $rule[0]['value'];
//        $max = $rule[1]['value'];
//        if (gettype($value) == 'array') {
//            foreach ($value as $item) {
//
//                if ($value < $min || $value > $max) {
//                    return response()->json('not valid (BetweenValidation)', 422);
//                }
//            }
//        } else {
//
//            if ($value < $min || $value > $max) {
//                return response()->json('not valid (BetweenValidation) ', 422);
//            }
//        }
//    }
}
