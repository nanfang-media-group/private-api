<?php

namespace SouthCN\PrivateApi;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AutoGeneratorController extends Controller
{
    public function generateRoute(Request $request)
    {
        $privateApi = Cache::get('private-api:route:' . $request->route()->uri);
        $response   = PrivateApi::app($privateApi['app'])->api($privateApi['api'], $request->all());

        if ($response instanceof Response) {
            return $response;
        }

        if (is_object($response)) {
            return (array)$response;
        }

        return $response;
    }
}
