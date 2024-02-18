<?php

namespace processmaker\parssconfig\Http\Controllers;

use Illuminate\Http\Request;

class ParserConfigController
{
    public function index(Request $request)
    {

        $inputJson = $request->getContent();
        $inputArray = json_decode($inputJson, true);
        $outputArray = [];
        $outputArray = $this->collectItemsWithLabel($inputArray['config'], []);

        return response()->json($outputArray);
    }

    private function collectItemsWithLabel($items, $outputArray)
    {
        foreach ($items as $item) {
            if (gettype($item) == "array" && !isset($item['label']) && !isset($item['items'])) {
                foreach ($item as $i) {
                    if (isset($i['label'])) {
                        $outputArray[] = $i['config'];
                    }
                    if (isset($i['items'])) {
                        $outputArray = $this->collectItemsWithLabel($i['items'], $outputArray);
                    }
                }
            }
            if (isset($item['label'])) {
                $outputArray[] = $item['config'];
            }
            if (isset($item['items'])) {
                $outputArray = $this->collectItemsWithLabel($item['items'], $outputArray);
            }
        }

        return $outputArray;
    }

    public function search(Request $request, $item_name)
    {
        $inputJson = $request->getContent();
        $inputArray = json_decode($inputJson, true);
        $outputArray = [];
        $outputArray = $this->collectItemsWithLabel($inputArray['config'], []);
        foreach ($outputArray as $item) {
            if ($item['name'] == $item_name) {
                return $item;
            }
        }
    }
}
