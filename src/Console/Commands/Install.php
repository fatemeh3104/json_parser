<?php

namespace ProcessMaker\Package\Parssconfig\Console\Commands;

use Artisan;
use ProcessMaker\Console\PackageInstallCommand;
use ProcessMaker\Models\Screen;
use ProcessMaker\Package\Parssconfig\Helppers\Parser;

class Install extends PackageInstallCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parssconfig:install{--first : Install for the first time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Parssconfig Package';

    /**
     * Publish assets
     * @return void
     */
    public function publishAssets()
    {
        $this->info('Publishing assets');
        Artisan::call('vendor:publish', [
            '--tag' => 'parssconfig',
            '--force' => true,
        ]);
    }

    public function preinstall()
    {
        $this->publishAssets();
    }

    public function install()
    {
    }

    public function postinstall()
    {
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $firstTime = $this->option('first');
        if ($firstTime) {
            $data = [];
            $screens = Screen::all()->where('type', '=', 'FORM');
            foreach ($screens as $screen) {
                try {
                    $parser = new Parser();
                    $parser->StoreItemsAndValidations($screen);
                } catch (\Exception $e) {
                    $data[] = $screen->id;
                    $data = array_merge($data, $e);
                }
            }
            if ($data === []) {
                $this->info('items and validations store successfully');
            } else {
                $this->table(['screen name', 'screen id'], $data);
            }
        }
        parent::handle();
        $this->info('Pars config has been installed');

    }

}

