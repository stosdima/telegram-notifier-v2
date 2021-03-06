<?php
/**
 * Created by PhpStorm.
 * User: stosdima
 * Date: 03.01.18
 * Time: 22:09
 */

namespace TelegramNotifier\TelegramBot\Commands;


use TelegramBot\Api\Types\Update;
use TelegramNotifier\Exception\BotCommandException;
use TelegramNotifier\ServiceContainer\Loader;
use TelegramNotifier\TelegramBot\Commands\CommandInterface;

class CommandBus
{
    /** List of commands
     * @var array $commands
     */
    public $commands = [];

    /** Api
     * @var \TelegramBot\Api\Client $client
     */
    protected $client;

    public function __construct(\TelegramBot\Api\Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $command
     * @return $this
     * @throws BotCommandException
     * @throws \Exception
     */
    public function addCommand($command)
    {
        if (!is_object($command)) {
            if (!class_exists($command)) {
                throw new BotCommandException(sprintf(
                    'Command class "%s" not found! Please make sure the class exists.',
                    $command
                ));
            }
            Loader::register($command, function () use ($command) {
                return new $command;
            });
            $command = Loader::resolve($command);
        }

        if ($command instanceof CommandInterface) {
            $this->commands[$command->getName()] = $command;
            return $this;
        }
        throw new BotCommandException('Command class should be an instance of "TelegramNotifier\TelegramBot\CommandInterface"');

    }

    public function addCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    public function removeCommand($name)
    {
        unset($this->commands[$name]);
        return $this;
    }

    public function removeCommands(array $names)
    {
        foreach ($names as $name) {
            $this->removeCommand($name);
        }
        return $this;
    }

    public function handler($message, Update $update)
    {
        $match = $this->parseCommand($message);
        if (!empty($match)) {
            $command = $match[1];
            $arguments = $match[3];
            $this->execute($command, $arguments, $update);
        } else {
            $this->execute($message, null, $update);

        }
        return $update;
    }

    public function execute($name, $arguments, $message)
    {
        if (array_key_exists($name, $this->commands)) {
            return $this->commands[$name]->make($this->client, $arguments, $message);
        } else {
            return $this->commands['incomingMessages']->make($this->client, null, $message);
        }
    }

    public function parseCommand($text)
    {
        if (trim($text) === '') {
            throw new \InvalidArgumentException('Message is empty, Cannot parse for command');
        }

        preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $text, $matches);

        return $matches;
    }
}