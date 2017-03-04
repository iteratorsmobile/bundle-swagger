<?php

namespace Absolvent\swagger;

use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class SwaggerSchema
{
    public $schema;

    public static function fromFilename($filename): SwaggerSchema
    {
        $schema = Yaml::parse(file_get_contents($filename));
        $schema = json_decode(json_encode($schema));

        return new static($schema);
    }

    public function __construct(stdClass $schema)
    {
        $this->schema = $schema;
    }

    public function findRequestMethodSchemaByHttpRequest(Request $request): stdClass
    {
        $pathSchema = $this->findRequestPathSchemaByHttpRequest($request);
        $method = strtolower($request->getMethod());

        if (!isset($pathSchema->{$method})) {
            throw MethodNotFound::fromRequest($request);
        }

        return $pathSchema->{$method};
    }

    public function findRequestPathSchemaByHttpRequest(Request $request): stdClass
    {
        $pathInfo = $request->getPathInfo();
        $pathsSchema = $this->schema->paths;

        if (!isset($pathsSchema->{$pathInfo})) {
            throw PathNotFound::fromRequest($request);
        }

        return $pathsSchema->{$pathInfo};
    }

    public function findResponseSchemaByHttpResponse(Request $request, Response $response): stdClass
    {
        $responsesSchema = $this->findRequestMethodSchemaByHttpRequest($request)->responses;
        $statusCode = $response->getStatusCode();

        if (!isset($responsesSchema->{$statusCode})) {
            throw StatusCodeNotFound::fromResponse($request, $response);
        }

        return $responsesSchema->{$statusCode}->schema;
    }
}
