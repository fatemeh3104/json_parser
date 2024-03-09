<?php

namespace Tests\Utils;

use Database\Factories\ProcessMaker\Models\ProcessFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ProcessMaker\Models\Process;
use ProcessMaker\Models\ProcessRequest;
use ProcessMaker\Models\ProcessRequestToken;
use ProcessMaker\Models\Screen;
use ProcessMaker\Models\ScreenVersion;
use ProcessMaker\Models\User;
use ProcessMaker\Package\Utils\Helppers\Parser;
use ProcessMaker\Package\Utils\Http\Middleware\ValidationItems;
use ProcessMaker\Package\Utils\Models\ItemsValidation;
use ProcessMaker\Package\Utils\Models\ScreenItems;
use SplFileInfo;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

use Faker\Factory as FakerFactory;

class BackendMaxValidationTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

    }

    public function test_max_validation()
    {
        $json = __DIR__ . "/fixtures/Json/MaxScreen.json";
        $user = User::factory()->create(['is_administrator' => true]);
        $this->actingAs($user, 'api');
        $result = [];

        //create Screen
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
        $bpmn = file_get_contents(__DIR__ . '/fixtures/bpmn/bpmnProcess.bpmn');
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
                "MaxTextarea" => "MaxTextareaTest"
            ]
        ];
        $response = $this->put('http://localhost:8080/api/1.0/tasks/1', $x);
        $response->assertStatus(422);


    }

}
