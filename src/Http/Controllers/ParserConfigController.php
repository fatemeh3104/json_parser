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
                $items = [];
                if (isset($screen->config)) {
                    $parser = new Parser();
                    $items = $parser->Parser($screen->config, []);
                    foreach ($items as $item) {
                        $screen_item = new ScreenItems();
                        if (isset($item['name'])) {
                            $screen_item['name'] = $item['name'];
                        } else {
                            $screen_item['name'] = "anonymous";
                        }
                        if (isset($item['conditionalHide'])) {
                            $screen_item['conditionalHide'] = $item['conditionalHide'];
                        }
                        $screen_item['screen_id'] = $screen->id;
                        $screen_item->save();
                        if (isset($item['validation'])) {
                            foreach ($item['validation'] as $validation) {
                                $item_validation = new ItemsValidation();
                                if (isset($validation['field'])) {
                                    $item_validation['type'] = $validation['field'];
                                } else {
                                    $item_validation['type'] = $validation['value'];
                                }
                                if (isset($validation['configs'])) {
                                    $item_validation['validation'] = $validation['configs'];
                                } else {
                                    $item_validation['validation'] = null;
                                }
                                $item_validation['screen_item_id'] = $screen_item->id;
                                $item_validation->save();
                            }
                        }
                    }
                }
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

    public function edit($screen_id)
    {

    }

}
