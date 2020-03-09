<?php

namespace SouthCN\PrivateApi\Repositories;

use AbelHalo\ApiProxy\ApiProxy;

class HttpLogic
{
    protected $httpLogic;
    protected $authAlgorithm;

    public function __construct(?string $httpLogic, AuthenticationAlgorithm $authAlgorithm)
    {
        $this->httpLogic = $httpLogic;
        $this->authAlgorithm = $authAlgorithm;
    }

    public function valid(): bool
    {
        return class_exists($this->httpLogic);
    }

    public function run(ApiProxy $proxy, string $url, array $params)
    {
        $wrapper = app($this->httpLogic);

        if ($wrapper->useAuthenticationAlgorithm ?? false) {
            $params = $this->authAlgorithm->processParams($params);
        }

        return $wrapper($proxy, $url, $params);
    }
}
