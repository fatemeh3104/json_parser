<?php

namespace ProcessMaker\Package\Parssconfig\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use phpseclib3\Exception\FileNotFoundException;
use ProcessMaker\Models\Screen;


class ShowItems extends Command

{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'showItem:name';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $screens = Screen::all()->where('type', '=', 'FORM');
            foreach ($screens as $screen) {
                $items = [];
                $id = [
                    $screen->id
                ];

                $items = $this->Parser($screen['config'], []);
                $data = [];
                foreach ($items as $item) {
                    $dataArray = [];
                    if (isset($item['name']) && isset($item['validation'])) {
                        foreach ($item['validation'] as $validation) {
                            $x = [
                                'name' => $item['name'],
                                'validation' => $validation['value'],
                                'screen_id' => (string)$screen->id
                            ];
                            array_push($dataArray, $x);
                        }
                        var_dump($dataArray);
                    } elseif (isset($item['name'])) {
                        $dataArray = ['name' => $item['name'],
                            'validation' => "",
                            'screen_id'=> $screen->id
                        ];
//                        $dataArray = array_merge($dataArray, $id);
                    }

                    $data[] = $dataArray;
                }
//                var_dump($data);
                $this->table(['name', 'validation', 'screen_id'], $data);
            }

        } catch (\Exception) {
            return response()->json("not found");
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
}
