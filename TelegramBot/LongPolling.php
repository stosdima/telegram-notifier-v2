<?php
/**
 * Created by PhpStorm.
 * User: stosdima
 * Date: 03.01.18
 * Time: 15:59
 */

namespace TelegramNotifier\TelegramBot;


use TelegramNotifier\ServiceContainer\Loader;

class LongPolling implements PollingMechanism
{
    protected $offset;

    public function run()
    {
        try {
            $this->offset = 0;
            $client = Loader::resolve('clientApi');
            $updates = $client->getUpdates($this->offset, 60);
            foreach ($updates as $update) {
                $this->offset = $updates[count($updates) - 1]->getUpdateId() + 1;
            }
            $client->handle($updates);
            $updates = $client->getUpdates($this->offset, 60);
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}