<?php
/**
 * Created by PhpStorm.
 * User: stosdima
 * Date: 12.12.17
 * Time: 12:08
 */

namespace TelegramNotifier\TelegramChain;


use TelegramNotifier\TelegramChain\Commands\Help;
use TelegramNotifier\TelegramChain\Commands\Search;
use TelegramNotifier\TelegramChain\Commands\Start;
use TelegramBot\Api\BotApi;

class CommandParserProcessor
{
    public static function runCommands($command)
    {
        $start = new Start();
        $help = new Help($start);
        $search = new Search($help);
        $search->parse($command);
    }
}