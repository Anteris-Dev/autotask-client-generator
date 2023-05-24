<?php

namespace Anteris\Autotask\Generator\Helpers;

use Exception;
use GuzzleHttp\Client;

class Api
{
    public static function endpoints() : array
    {
        $response   = (new Client)->get('https://webservices14.autotask.net/ATServicesRest/swagger/docs/v1');
        $array      = json_decode($response->getBody(), true);

        if(! isset($array['paths'])) {
            throw new Exception('Invalid response!');
        }

        $paths = [];

        foreach($array['paths'] as $path => $content) {
            if (
                str_contains($path, '{parentId}') ||
                str_contains($path, 'EntityInformation') ||
                str_contains($path, 'entityInformation') ||
                str_contains($path, 'Modules') ||
                str_contains($path, 'ThresholdInformation') ||
                str_contains($path, 'Version') ||
                str_contains($path, 'ZoneInformation')
            ) {
                continue;
            }

            $path = str_replace('/V1.0/', '', $path);
            $path = explode('/', $path)[0];
            
            if (! in_array($path, $paths) && $path) {
                $paths[] = $path;
            }
        }

        return $paths;
    }
}
