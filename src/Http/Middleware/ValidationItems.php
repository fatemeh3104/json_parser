<?php

namespace ProcessMaker\Package\Parssconfig\Http\Middleware;

use AWS\CRT\Log;
use Closure;
use Illuminate\Routing\Route;
use ProcessMaker\Models\FormalExpression;
use ProcessMaker\Models\ProcessRequestToken;
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
        //find screen from task
        $task = $request->task;
        $screenVersion = $task->getScreenVersion();
        $task->screen = $screenVersion ? $screenVersion->toArray() : null;
        $screen = $task->screen;
        //fetch screen items from DB
        $screen_items = ScreenItems::where('screen_id', '=', $screen['screen_id'])->get();
        $items_name = [];
        foreach ($screen_items as $screen_item) {
            $items_name[] = $screen_item->name;
        }
        $items = $request->data;
        unset($items['_user']);
        unset($items['_request']);
        //check do not send items is not in form
        foreach ($items as $key => $value) {
            if (!(in_array($key, $items_name))) {
                return response()->json('not valid ' . $key . ' is not a item from this form ', 422);
            }
        }
        //check validations for item
        foreach ($screen_items as $screen_item) {
            // when item is hide do not validation
            if (isset($screen_item['conditionalHide'])) {
                if (!ExpressionEvaluator::evaluate('feel', $screen_item['conditionalHide'], [])) {
                    continue;
                }
            }
            //get all items validation
            $items_validation = ItemsValidation::where('screen_item_id', '=', $screen_item['id'])->get();
            //validate input
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

}
