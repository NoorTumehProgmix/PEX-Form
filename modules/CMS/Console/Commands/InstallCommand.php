<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/juzacms
 * @author     The Anh Dang
 * @link       https://github.com/juzaweb/juzacms
 * @license    GNU V2
 */

namespace Juzaweb\CMS\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Support\Manager\DatabaseManager;

class InstallCommand extends Command
{
    protected $signature = 'juzacms:install';

    public function handle(
        DatabaseManager $databaseManager,
    ): int {
        $this->info('JUZACMS INSTALLER');
        $this->info('-- Database Install');

        $result = $databaseManager->run();
        if (Arr::get($result, 'status') == 'error') {
            throw new Exception($result['message']);
        }

        $this->info('CMS Install Successfully !!!');

        return self::SUCCESS;
    }
}
