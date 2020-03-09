<?php

namespace SouthCN\PrivateApi;

use AbelHalo\ApiProxy\ApiProxy;
use SouthCN\PrivateApi\Repositories\ApiCache;
use SouthCN\PrivateApi\Repositories\AuthenticationAlgorithm;
use SouthCN\PrivateApi\Repositories\Guard;
use SouthCN\PrivateApi\Repositories\Hook;
use SouthCN\PrivateApi\Repositories\HttpLogic;
use SouthCN\PrivateApi\Repositories\Preparer;

class Repository
{
    protected $guard;
    protected $hook;
    protected $httpLogic;
    protected $proxy;

    protected $authAlgorithm;
    protected $app;
    protected $config;

    public function __construct(string $app, ?\Closure $guard = null)
    {
        $this->app = $app;
        $this->config = config("private-api.$app");

        $this->authAlgorithm = new AuthenticationAlgorithm($this->config['app'], $this->config['ticket']);
        $this->guard = new Guard($guard);
        $this->hook = new Hook($this->config['_']['hooks'] ?? []);
        $this->httpLogic = new HttpLogic($this->config['_']['custom_http_logic'] ?? null, $this->authAlgorithm);
        $this->proxy = (new ApiProxy)
            ->headers(['Accept' => 'application/json'])
            ->setReturnAs(config('private-api._.return_type'));

        $this->proxy->logger->enable();
    }

    /**
     * @param  string  $name
     * @param  array   $params
     * @return mixed
     */
    public function api(string $name, array $params = [])
    {
        $this->guard->run($this->app, $name);

        $preparer  = new Preparer($this->config[$name] ?? []);
        $url       = array_get($this->config, "$name.url");
        $hasFiles  = array_get($this->config, "$name.has_files", false);
        $httpLogic = array_get($this->config, "$name.custom_http_logic");

        // Prepare API request
        $params = $preparer->cast($params);
        $params = $preparer->setDefaults($params);
        $params = $preparer->setParameterMap($params);

        $this->hook->run('before-requesting', $this->proxy, $url, $params);

        if ($httpLogic) {
            $wrapper = app($httpLogic);

            return $wrapper($this->proxy, $url, $params);
        }

        if ($this->httpLogic->valid()) {
            return $this->httpLogic->run($this->proxy, $url, $params);
        }

        return $this->post($url, $params, $hasFiles);
    }

    protected function post(string $url, array $params, bool $withFiles = false)
    {
        $cache = array_get($this->config, 'cache');

        if (!$withFiles) {
            $apiCache = new ApiCache($cache ?: '');
            $key      = md5($this->authAlgorithm->app . $url . serialize($params));

            if ($response = $apiCache->get($key)) {
                return $response;
            }
        }
        $response = $this->proxy->{$withFiles ? 'postWithFiles' : 'post'}(
            $url,
            $this->authAlgorithm->processParams($params)
        );

        if (!$withFiles) {
            $apiCache->smartCache($key, $response);
        }

        return $response;
    }
}
