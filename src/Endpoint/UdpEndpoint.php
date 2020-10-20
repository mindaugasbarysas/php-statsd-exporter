<?php

namespace App\Endpoint;

class UdpEndpoint
{
    const BUFFER_SIZE = 4048;

    public function listen($address, $port, callable $processor)
    {
        echo "listen on $address:$port\n";
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($socket, $address, $port);
        while (true) {
            $string = socket_read($socket, self::BUFFER_SIZE, PHP_BINARY_READ);
            if ($string === "") {
                usleep(1000);
                continue;
            }
            $processor($string);
        }
    }
}