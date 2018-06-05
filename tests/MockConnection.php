<?php


namespace App\Test;


use Ratchet\ConnectionInterface;

class MockConnection implements ConnectionInterface
{
    public $last = [
        'send' => '',
        'close' => false,
    ];

    public $remoteAddress = '127.0.0.1';

    public function send($data)
    {
        $this->last[__FUNCTION__] = $data;
    }

    public function close()
    {
        $this->last[__FUNCTION__] = true;
    }
}
