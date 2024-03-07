<?php

namespace Tests\Parssconfig;

use Database\Factories\ProcessMaker\Models\ProcessFactory;
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

class BackendTaskValidationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

    }

    public function test_backend_validation_for_form_screen()
    {
        $jsons = File::allFiles('storage/app/Test/jsons');
        $user = User::factory()->create(['is_administrator' => true]);
        $this->actingAs($user, 'api');
        $result = [];
        foreach ($jsons as $json) {
            //create Screen
            $filename = basename($json);
            $json = File::get($json);
            $contents = json_decode($json);
            $config = $contents->screens[0]->config;
            $screen = Screen::factory()->create();
            $screen['type'] = "form";
            $screen['config'] = $config;
            $screen->save();
            $screen_version = ScreenVersion::factory()->create([
                'screen_id' => $screen->id,
                'config' => $config,
                'status' => "ACTIVE"
            ]);
            //create process
            $bpmn = file_get_contents(storage_path('app/Test/xml/bpmnProcess.xml'));
            $bpmn = str_replace('test_screen_id', $screen->id, $bpmn);
            $bpmn = str_replace('test_screen_id', $screen->id, $bpmn);
            $process = Process::factory()->create(['bpmn' => $bpmn]);
            $element_id = Str::betweenFirst($bpmn, 'task id="', '" name');

            //create Request
            $process_request = ProcessRequest::factory()->create(['process_id' => $process->id, 'user_id' => $user->id]);
            //create Task
            $request_token = ProcessRequestToken::factory()->create([
                'status' => 'ACTIVE',
                'process_id' => $process->id,
                'process_request_id' => $process_request->id,
                'user_id' => $user->id,
                'element_id' => $element_id
            ]);
//            $contents = file_get_contents(storage_path('app/Test/jsons/A-Screen.json'));
//            $contents = json_decode($contents);
            $this->actingAs($user, 'api');
            $this->put('http://localhost:8080/api/1.0/screens/1', $screen->toArray());

            $x = [
                "status" => "COMPLETED",
                "data" => [
                    "_user" => $user->getAttributes(),
                    "_request" => $process_request->getAttributes(),
                    "b_form_component_1" => ['lll'],
                    "form_text_area_1" => 12
                ]
            ];

            $response = $this->put('http://localhost:8080/api/1.0/tasks/1', $x);
            $response->assertStatus(200);

        }




    }


}
