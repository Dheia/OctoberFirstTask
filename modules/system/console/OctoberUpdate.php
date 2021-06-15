<?php namespace System\Console;

use Illuminate\Console\Command;
use October\Rain\Process\Composer as ComposerProcess;
use Exception;

/**
 * OctoberUpdate performs a system update.
 *
 * This updates October CMS and all plugins, database and libraries.
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class OctoberUpdate extends Command
{
    /**
     * @var string name of console command
     */
    protected $name = 'october:update';

    /**
     * @var string description of the console command
     */
    protected $description = 'Updates October CMS and all plugins, database and files.';

    /**
     * handle executes the console command
     */
    public function handle()
    {
        $this->output->writeln('<info>Updating October CMS...</info>');

        $this->comment("Executing: composer update");
        $this->output->newLine();

        // Composer update via local library
        try {
            $composer = new ComposerProcess;
            $composer->setCallback(function($message) { echo $message; });
            $composer->update();

            if ($composer->lastExitCode() !== 0) {
                $this->output->error('Update failed. Check output above');
                exit(1);
            }
        }
        // Composer update via console
        catch (Exception $ex) {
            $errCode = null;
            passthru('composer update', $errCode);

            if ($errCode !== 0) {
                $this->output->error('Update failed. Check output above');
                exit(1);
            }
        }

        $this->output->success('System updated');

        // Run migrations
        $this->comment('Please migrate the database with the following command');
        $this->output->newLine();
        $this->line("* php artisan october:migrate");
        $this->output->newLine();
    }
}
