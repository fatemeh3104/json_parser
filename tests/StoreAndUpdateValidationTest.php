<?php
namespace Tests\Parssconfig;

use Illuminate\Foundation\Testing\RefreshDatabase;
use ProcessMaker\Models\Screen;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;
use Tests\TestCase;

class StoreAndUpdateValidationTest extends TestCase
{
//    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

    }
    public function test_update_items_validation_and_screen_items_on_terminator()
    {
        $screen = Screen::factory()->count(5)->create();



    }
    public function testParser()
    {

        $screen =Screen::factory()->create();

        // Create a new instance of Parser class
        $parser = new Parser();

        // Run the method you want to test
        $o =[];
        dd($screen);
        $output = $parser->Parser($screen->config, $o);
        dd($output);
        // Assertions for Parser class output
        // Add your assertions here based on the expected output

        // Clean up if necessary
    }



}
