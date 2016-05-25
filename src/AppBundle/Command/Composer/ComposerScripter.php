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
     * Clears the Symfony cache.
     */
    public static function loadFixtures(Event $event)
    {
        $consoleDir = static::getConsoleDir($event, 'Load fixtures to database');

        static::executeCommand($event, $consoleDir, 'rock:fixture:load');
    }
}
