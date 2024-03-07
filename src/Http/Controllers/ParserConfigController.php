<?php

namespace processmaker\parssconfig\Http\Controllers;

use Beta\Microsoft\Graph\Model\Package;
use Illuminate\Http\Request;
use Mockery\Exception;
use ProcessMaker\Models\Screen;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;
use ProcessMaker\Package\Parssconfig\Models\ItemsValidation;
use ProcessMaker\Package\Parssconfig\Models\ScreenItems;

class ParserConfigController
{
    public function index(Request $request)
    {
        $field = $request->get('field');
        $inputJson = $request->getContent();
        $inputArray = json_decode($inputJson, true);
        $outputArray = [];
        $parser = new Parser();
        $outputArray = $parser->Parser($inputArray['config'], [], $field);
        return response()->json($outputArray);
    }

    public function store()
    {
        try {
            $screens = Screen::all()->where('type', '=', 'FORM');
            foreach ($screens as $screen) {
                $parser = new Parser();
                $parser->StoreItemsAndValidations($screen);
            }
            return response()->json('save');


        } catch (\Exception $e) {
            return $e;
        }
    }


    public function search(Request $request, $item_name)
    {
        $inputJson = $request->getContent();
        $field = $request->get('field');
        $inputArray = json_decode($inputJson, true);
        $outputArray = [];
        $parser = new Parser();
        $outputArray = $parser->Parser($inputArray['config'], [], $field);
        foreach ($outputArray as $item) {
            if ($item['name'] == $item_name) {
                return $item;
            } else {
                return response()->json('item not found');
            }
        }
    }

    public function fetch($screen_id)
    {
        try {
            $screen = Screen::findOrFail($screen_id);
            $items = [];
            $parser = new Parser();
            $items = $parser->Parser($screen['config'], []);
            return $items;
        } catch (\Exception $e) {

            return response()->json("not found");
        }

    }

    public function all()
    {
        try {
            $screens = Screen::all()->where('type', '=', 'FORM');
            foreach ($screens as $screen) {
                $items = [];
                $parser = new Parser();
                $items = $parser->Parser($screen['config'], []);
                return $items;
            }

        } catch (\Exception) {
            return response()->json("not found");
        }


    }



}
