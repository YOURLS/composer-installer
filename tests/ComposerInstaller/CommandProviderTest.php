<?php
namespace YOURLS\ComposerInstaller;

use PHPUnit\Framework\TestCase;
use Composer\Command\BaseCommand;

class CommandProviderTest extends TestCase
{
    public function testCommands()
    {
        $plugin = new CommandProvider();

        $commands = $plugin->getCommands();

        $this->assertCount(2, $commands);

        $this->assertContainsOnlyInstancesOf(BaseCommand::class, $commands);
    }

}
