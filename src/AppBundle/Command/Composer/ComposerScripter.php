<?php

namespace AppBundle\Command\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
use Composer\Script\Event;

/**
 * @author Vehsamrak
 */
class ComposerScripter extends ScriptHandler
{

    /**
     * Creating database
     */
    public static function createDatabase(Event $event)
    {
        $consoleDir = static::getConsoleDir($event, 'Creates the configured database');

        static::executeCommand($event, $consoleDir, 'doctrine:database:create --if-not-exists');
    }

    /**
     * Update database schema
     */
    public static function updateDatabaseSchema(Event $event)
    {
        $consoleDir = static::getConsoleDir($event, 'Creates the configured database');

        static::executeCommand($event, $consoleDir, 'doctrine:schema:update --force');
    }
}
