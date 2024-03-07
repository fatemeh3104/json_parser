<?php

namespace ProcessMaker\Package\Parssconfig\Helppers;

use ProcessMaker\Package\Parssconfig\Models\ItemsValidation;
use ProcessMaker\Package\Parssconfig\Models\ScreenItems;


class Parser
{
    //method for pars config Json
    public function Parser($items, $outputArray, $field = 'config')
    {
        foreach ($items as $item) {
            //When several arrays are on the same level
            if (gettype($item) == "array" && !isset($item['label']) && !isset($item['items'])) {
                foreach ($item as $i) {
                    //if have label add to out put array
                    if (isset($i['label'])) {
                        $outputArray[] = $i[$field];
                    }
                    //if had children pars them
                    if (isset($i['items'])) {
                        $outputArray = $this->Parser($i['items'], $outputArray, $field);
                    }
                }
            }
            if (isset($item['label'])) {
                $outputArray[] = $item[$field];
            }
            //when have nested items
            if (isset($item['items'])) {
                $outputArray = $this->Parser($item['items'], $outputArray, $field);
            }
        }

        return $outputArray;
    }

    //store items and items validation in data base
    public function StoreItemsAndValidations($screen)
    {
        $items = [];
        if (isset($screen->config)) {
            //get all items in screen config
            $items = $this->Parser($screen->config, []);

            foreach ($items as $item) {
                //store screen items in DB
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
                //store validation items in DB
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

}
