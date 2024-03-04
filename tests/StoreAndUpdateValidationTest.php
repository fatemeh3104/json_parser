<?php

namespace Tests\Parssconfig;

use Database\Factories\ProcessMaker\Models\ProcessFactory;
use Factories\ScreenItemsFactory\ScreenItemsFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ProcessMaker\Models\Process;
use ProcessMaker\Models\ProcessRequest;
use ProcessMaker\Models\ProcessRequestToken;
use ProcessMaker\Models\Screen;
use ProcessMaker\Models\ScreenVersion;
use ProcessMaker\Models\User;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationItems;
use ProcessMaker\Package\Parssconfig\Models\ItemsValidation;
use ProcessMaker\Package\Parssconfig\Models\ScreenItems;
use SplFileInfo;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

use Faker\Factory as FakerFactory;

class StoreAndUpdateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

    }

//    public function test_update_items_validation_and_screen_items_on_terminator()
//    {
//        $jsons = File::allFiles('storage/app/Test/jsons');
//        $result = [];
//        foreach ($jsons as $json) {
//            $filename = basename($json);
//            $json = File::get($json);
//            $contents = json_decode($json);
//            $config = $contents->screens[0]->config;
//            $user = User::factory()->create();
//            $this->actingAs($user, 'api');
//            $screen = Screen::factory()->create();
//            $screen['type'] = "form";
//            $screen['config'] = $config;
//            $screen->save();
//            $response = $this->put('http://localhost:8080/api/1.0/screens/1', $screen->toArray());
//            $screen_items = ScreenItems::all();
//            $validation = ItemsValidation::all();
//            $result = ($contents->items_count == count($screen_items) && $contents->validation_count == count($validation));
//            $results[$filename] = $result;
//        }
//        foreach ($results as $filename => $result) {
//            if ($result) {
//                $this->assertTrue(true, "The test for file $filename passed.");
//            } else {
//                $this->markTestSkipped("The test for file $filename did not pass.");
//            }
//        }
////        foreach ($results as $filename => $result) {
////            if ($result) {
////                $this->assertEquals(true, $result, "The test for file $filename passed.");
////            } else {
////                $this->assertEquals(false, $result, "The test for file $filename did not pass.");
////            }
//////            if ($result) {
//////                $this->assertTrue(true, "The test for file $filename passed.");
//////            } else {
//////                $this->assertFalse(true, "The test for file $filename did not pass.");
//////            }
////        }
//    }
//
////    public function test_required_validation()
////    {
////        $contents = file_get_contents(storage_path('app/jsons/screen.json'));
////        $contents = json_decode($contents);
////        $config = $contents->screens[0]->config;
////        $user = User::factory()->create();
////        $this->actingAs($user, 'api');
////        $screen = Screen::factory()->create();
////        $screen['type'] = "form";
////        $screen['config'] = $config;
////        $screen->save();
////        $response = $this->put('http://localhost:8080/api/1.0/screens/1', $screen->toArray());
////        $screen_items = ScreenItems::all();
////        $validation = ItemsValidation::all();
////        if ($contents->items_count == count($screen_items) && $contents->validation_count == count($validation)) {
////            return $response->assertStatus(204);
////        }
////    }
//
//    public function test_max_validation()
//    {
//
//    }
//
//    public function test_min_validation()
//    {
//
//    }
//
//    public function test_between_validation()
//    {
//
//    }

//    public function test_alpha_validation()
//    {
//
//    }

    public function test_backend_validation_for_form_screen()
    {
        $jsons = File::allFiles('storage/app/Test/jsons');
        $result = [];
        foreach ($jsons as $json) {
            //create Screen
            $filename = basename($json);
            $json = File::get($json);
            $contents = json_decode($json);
            $config = $contents->screens[0]->config;
            $user = User::factory()->create();
            $this->actingAs($user, 'api');
            $screen = Screen::factory()->create();
            $screen['type'] = "form";
            $screen['config'] = $config;
            $screen->save();
            //create process
            $process = Process::factory()->create();
            $bpmn = file_get_contents(storage_path('app/Test/xml/bpmnProcess.xml'));
            $process->bpmn = $bpmn;
            $process->save();
            //request

            $request = ProcessRequest::factory()->create();
            $request['process_id'] = $process->id;
            $request->save();
            $request_token = ProcessRequestToken::factory()->create();
            $request_token['process_id']= $process->id;
            $request_token->save();
//        dd($process['name'],Process::all('bpmn'));
            $xml = strpos($bpmn, "pm:screenRef=");
            $screen_id = $bpmn[$xml + 14];
            $contents = file_get_contents(storage_path('app/Test/jsons/screen.json'));
            $contents = json_decode($contents);
            $this->actingAs($user, 'api');
            $this->put('http://localhost:8080/api/1.0/screens/1', $screen->toArray());
            $user = User::factory()->create();
            $user = $user->getAttributes();
            $request = $request->getAttributes();
//            $event = $this->post('http://localhost:8080/api/1.0/process_events',['process'=>$process->id]);
            $x = [
                "status" => "COMPLETED",
                "data" => [
                    "_user" => $user,
                    "_request" => $request
                ],
                "screen_items"
            ];
            $response = $this->put('http://localhost:8080/api/1.0/tasks/1', $x);
            dd(123123);
            dd($response);
//        $response = $this->put(,$x);
        }
        dd(Screen::all());

    }


}
