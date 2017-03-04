<?php

namespace Absolvent\swagger;

use JsonSchema\Constraints\Factory;
use JsonSchema\Validator;

/**
 * Unfortunately JsonSchema\Validator is stateful. To overcome this we need
 * a builder.
 */
class JsonSchemaValidatorBuilder
{
    public $schemaStorageBuilder;

    public function __construct(SwaggerSchema $swaggerSchema)
    {
        $this->schemaStorageBuilder = new SchemaStorageBuilder($swaggerSchema);
    }

    public function createJsonSchemaFactory(): Factory
    {
        $schemaStorage = $this->schemaStorageBuilder->createJsonSchemaStorage();

        return new Factory($schemaStorage);
    }

    public function createJsonSchemaValidator(): Validator
    {
        $factory = $this->createJsonSchemaFactory();

        return new Validator($factory);
    }
}
