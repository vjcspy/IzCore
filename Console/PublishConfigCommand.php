<?php namespace Modules\IzCore\Console;

use Illuminate\Console\Command;
use Module;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishConfigCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'iz:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Override All Config';

    /**
     * PublishConfigCommand constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $pathModules = Module::getPath();

        $moduleDirs = scandir($pathModules);
        foreach ($moduleDirs as $moduleDir) {
            if (!in_array($moduleDir, [".", ".."])) {
                /*Path Config/Vendor của module hiện tại*/
                $currentVendorConfigPaths = $pathModules . '/' . $moduleDir . '/Config/Vendor';

                /*Kiểm tra xem module hiện tại có thư mục Vendor không*/
                if (!file_exists($currentVendorConfigPaths))
                    continue;

                $configs = scandir($currentVendorConfigPaths);

                foreach ($configs as $config) {
                    if (!in_array($config, [".", ".."])) {
                        $currentVendorConfigPath = $currentVendorConfigPaths . '/' . $config;

                        if (!copy($currentVendorConfigPath, config_path() . '/' . $config))
                            $this->error('Failed to copy file: ' . $config);
                        else
                            $this->output->success('Copy file: ' . $currentVendorConfigPath);
                    }
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
