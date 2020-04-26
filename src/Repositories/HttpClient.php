<?php

namespace SouthCN\PrivateApi\Repositories;

use AbelHalo\ApiProxy\ApiProxy;
use Closure;

class HttpClient
{
    protected $proxy;
    protected $authAlgorithm;
    protected $config;

    public function __construct(ApiProxy $proxy, AuthenticationAlgorithm $authAlgorithm, string $config = '')
    {
        $this->proxy = $proxy;
        $this->authAlgorithm = $authAlgorithm;
        $this->config = $config;
    }

    public function post(string $url, array $params)
    {
        $key = md5($this->authAlgorithm->app . $url . serialize($params));

        return $this->smartCache($key, function () use ($url, $params) {
            return $this->proxy->post(
                $url,
                $this->authAlgorithm->processParams($params)
            );
        });
    }

    public function postWithFiles(string $url, array $params)
    {
        return $this->proxy->postWithFiles(
            $url,
            $this->authAlgorithm->processParams($params)
        );
    }

    protected function smartCache(string $key, Closure $callback)
    {
        $apiCache = new ApiCache($this->config);

        if ($response = $apiCache->get($key)) {
            return $response;
        }

        $apiCache->smartCache($key, $response = $callback());

        return $response;
    }
}
