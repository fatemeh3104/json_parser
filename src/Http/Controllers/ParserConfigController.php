<?php

namespace processmaker\parssconfig\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;
use ProcessMaker\Models\Screen;
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
        $outputArray = $this->Parser($inputArray['config'], [], $field);
        return response()->json($outputArray);
    }

    public function store()
    {
        try {
            $screens = Screen::all()->where('type', '=', 'FORM');
            foreach ($screens as $screen) {
                $items = [];
                $items = $this->Parser($screen->config, []);
                foreach ($items as $item) {
                    $screen_item = new ScreenItems();
                    if (isset($item['name'])) {
                        $screen_item['name'] = $item['name'];
                    } else {
                        $screen_item['name'] = "anonymous";
                    }
                    $screen_item['screen_id'] = $screen->id;
                    $screen_item->save();
                    if (isset($item['validation'])) {
                        foreach ($item['validation'] as $validation) {
                            $item_validation = new ItemsValidation();
                            $item_validation['type'] = $validation['value'];
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
            return response()->json('save');


        } catch (\Exception $e) {
            return $e;
        }
    }

    private function Parser($items, $outputArray, $field = 'config')
    {
        foreach ($items as $item) {
            if (gettype($item) == "array" && !isset($item['label']) && !isset($item['items'])) {
                foreach ($item as $i) {
                    if (isset($i['label'])) {
//                        $outputArray[] = $i['config'];
                        $outputArray[] = $i[$field];
                    }
                    if (isset($i['items'])) {
                        $outputArray = $this->Parser($i['items'], $outputArray, $field);
                    }
                }
            }
            if (isset($item['label'])) {
                $outputArray[] = $item[$field];
//                $outputArray[] = $item['config'];
            }
            if (isset($item['items'])) {
                $outputArray = $this->Parser($item['items'], $outputArray, $field);
            }
        }

        return $outputArray;
    }

    public function search(Request $request, $item_name)
    {
        $inputJson = $request->getContent();
        $field = $request->get('field');
        $inputArray = json_decode($inputJson, true);
        $outputArray = [];
        $outputArray = $this->Parser($inputArray['config'], [], $field);
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
            $items = $this->Parser($screen['config'], []);
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
                $items = $this->Parser($screen['config'], []);
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
