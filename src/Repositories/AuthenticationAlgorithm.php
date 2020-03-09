<?php

namespace SouthCN\PrivateApi\Repositories;

class AuthenticationAlgorithm
{
    public $app;
    public $ticket;

    public function __construct(string $app, string $ticket)
    {
        $this->app    = $app;
        $this->ticket = $ticket;
    }

    public function processParams(array $params): array
    {
        $time = time();

        $params['app']   = $this->app;
        $params['time']  = $time;
        $params['token'] = $this->calculateToken($time);

        return $params;
    }

    public function verifyToken(string $token, string $time): bool
    {
        return $token == $this->calculateToken($time);
    }

    protected function calculateToken(string $time): string
    {
        return md5($this->app . $time . $this->ticket);
    }
}
