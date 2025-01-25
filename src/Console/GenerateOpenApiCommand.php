<?php

namespace MikeGarde\OpenApiExport\Console;

use Illuminate\Console\Command;
use MikeGarde\OpenApiExport\OpenApiGenerator;
use Symfony\Component\Yaml\Yaml;

class GenerateOpenApiCommand extends Command
{
    protected $signature = 'openapi:generate
                            {--f|format=yaml : Output format (yaml or json)}
                            {--o|output= : The output file path. Defaults to "openapi.yml" for yaml or "openapi.json" for json.}}
                            {--l|limit= : Limit the number of routes to include in the documentation.}
                            {--raw : Output without aligning to the OpenAPI specification.}';

    protected $description = 'Generate OpenAPI/Swagger documentation from routes';

    public function handle(OpenApiGenerator $generator): int
    {
        $format = strtolower($this->option('format'));
        $output = $this->option('output') ?? ($format === 'yaml' ? 'openapi.yml' : 'openapi.json');
        $limit  = (int)$this->option('limit');
        $raw    = $this->option('raw');

        if ($raw) {
            $this->info('Raw output, read more here: https://github.com/MikeGarde/openapi-export#raw-output');
        }

        if (!in_array($format, ['json', 'yaml'])) {
            $this->error('Invalid format specified. Allowed formats: json, yaml.');

            return Command::FAILURE;
        }

        $openApi = $generator->generate($limit);

        if (!$raw) {
            $openApi->alignToSpec();
        }

        if ($format === 'yaml') {
            $content = Yaml::dump($openApi->toArray(), 6, 2);
        } else {
            $content = json_encode($openApi->toArray(), JSON_PRETTY_PRINT);
        }

        file_put_contents($output, $content);
        $this->info("OpenAPI documentation generated at $output");

        return Command::SUCCESS;
    }
}
