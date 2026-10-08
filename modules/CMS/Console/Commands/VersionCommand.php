<?php


namespace Juzaweb\CMS\Console\Commands;

use Illuminate\Console\Command;
use Juzaweb\CMS\Version;

class VersionCommand extends Command
{
    protected $name = 'juza:version';

    public function handle()
    {
        echo Version::getVersion();
    }
}
