<?php

namespace Juzaweb\API\Support\Documentation;

use Juzaweb\API\Support\Swagger\SwaggerDocument;

interface APISwaggerDocumentation
{
    public function handle(SwaggerDocument $document): SwaggerDocument;
}
