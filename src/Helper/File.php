<?php

namespace Anteris\Autotask\Generator\Helper;

use Anteris\Autotask\Generator\DataTransferObject\EndpointDataTransferObject;

class File
{
    public static function collectionFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . 'Collection.php';
    }

    public static function endpointDirectory(EndpointDataTransferObject $endpoint)
    {
        return 'API/' . $endpoint->plural;
    }

    public static function dataTransferObjectFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . "Entity.php";
    }

    public static function paginatorFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . "Paginator.php";
    }

    public static function queryBuilderFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . "QueryBuilder.php";
    }

    public static function serviceFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . "Service.php";
    }

    public static function serviceTestFilename(EndpointDataTransferObject $endpoint)
    {
        return $endpoint->singular . "ServiceTest.php";
    }

    public static function testDirectory(EndpointDataTransferObject $endpoint)
    {
        return 'API/' . $endpoint->plural;
    }
}
