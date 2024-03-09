<?php

namespace ProcessMaker\Package\Utils\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use ProcessMaker\Http\Resources\Screen;
use ProcessMaker\Package\Utils\Helppers\Parser;
use ProcessMaker\Package\Utils\Models\ItemsValidation;
use ProcessMaker\Package\Utils\Models\ScreenItems;


class ValidationUpdate
{


    public function handle($request, Closure $next)
    {
        // Perform actions before the request is handled by the application
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $screen = $request->route('screen');
        $screen_items = ScreenItems::all()->where('screen_id', '=', $screen->id);
        foreach ($screen_items as $screen_item) {
            $item_validations = ItemsValidation::all()->where('screen_item_id', '=', $screen_item->id);
            foreach ($item_validations as $item_validation) {
                $item_validation->delete();
            }
            $screen_item->delete();
        }
        $parser = new Parser();
        $parser->StoreItemsAndValidations($screen);

    }


}
