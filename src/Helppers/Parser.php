<?php

namespace ProcessMaker\Package\Parssconfig\Helppers;

class Parser
{
    public function Parser($items, $outputArray, $field = 'config')
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


}
