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
        /*--------------------------- Publish config/asset/view of module ---------------------------*/
        $this->output->writeln('--------------------------- Publish modules files ---------------------------');
        $this->call('module:publish');

        /*--------------------------- Publish vendor config ---------------------------*/
        $this->output->writeln('--------------------------- Publish vendor config ---------------------------');
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

        /*--------------------------- Publish theme of module ---------------------------*/
        $this->output->writeln('--------------------------- Publish themes directory ---------------------------');
        foreach ($moduleDirs as $moduleDir) {
            if (!in_array($moduleDir, [".", ".."])) {
                $currentThemeDir = $pathModules . '/' . $moduleDir . '/themes';

                if (!file_exists($currentThemeDir))
                    continue;

                $this->output->success('Copy themes in module: ' . $moduleDir);

                $this->recurseCopy($currentThemeDir, public_path() . '/modules/themes');
            }
        }

        /*--------------------------- Merger xml config instead copy ---------------------------*/

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

    protected function recurseCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    $s = $src . '/' . $file;
                    $d = $dst . '/' . $file;
                    copy($s, $d);
                    // $this->output->success('Copy file: ' . $s);
                }
            }
        }
        closedir($dir);
    }

}
