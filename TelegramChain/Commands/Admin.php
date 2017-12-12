<?php
/**
 * Created by PhpStorm.
 * User: stosdima
 * Date: 12.12.17
 * Time: 12:39
 */

namespace TelegramNotifier\TelegramChain\Commands;


use TelegramNotifier\TelegramChain\CommandParser;

class Admin extends CommandParser
{
    public function parse(string $commandName)
    {
        if ($this->canHandleCommand($commandName)) {
            print('Handling command: ' . $commandName);
        } else {
            parent::parse($commandName); // TODO: Change the autogenerated stub
        }
    }
}