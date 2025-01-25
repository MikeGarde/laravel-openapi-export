<?php

namespace MikeGarde\OpenApiExport;

use Illuminate\Support\Facades\Route;

class OpenApiGenerator
{
    /** @var OpenApiElement[] */
    private array $elements = [];

    /**
     * Add an element to the OpenAPI documentation
     *
     * @param OpenApiElement $element
     */
    public function addElement(OpenApiElement $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * Generate OpenAPI documentation from Laravel routes
     *
     * @return $this
     */
    public function generate(int $limit = 0): static
    {
        $routes = Route::getRoutes();

        $count = 1;
        foreach ($routes as $route) {
            $this->addElement(
                new OpenApiElement(
                    path      : $this->formatUrl($route->uri()),
                    method    : $route->methods()[0] ?? 'GET',
                    summary   : $route->action['as'] ?? '',
                    parameters: $this->extractParameters($route->uri()),
                    responses : [
                        '200' => ['description' => 'Success'],
                    ]
                )
            );

            if ($count++ === $limit && $limit > 0) {
                break;
            }
        }

        return $this;
    }

    /**
     * Extract parameters from the URI
     *
     * @param string $uri
     *
     * @return array
     */
    private function extractParameters(string $uri): array
    {
        preg_match_all('/\{(.*?)\}/', $uri, $matches);
        $parameters = [];
        foreach ($matches[1] as $param) {
            $parameters[] = new OpenApiParameter($param);;
        }

        return $parameters;
    }

    /**
     * Aligns to OpenAPI Spec by duplicating routes with unrequired parameters
     */
    public function alignToSpec(): void
    {
        $elements = $this->elements;
        foreach ($elements as $element) {
            $element->alignParameters();

            if ($element->allParametersRequired()) {
                continue;
            }

            $unrequiredParameters = $element->unrequiredParameters();
            foreach ($unrequiredParameters as $parameter) {
                $newElement = clone $element;
                $newElement->removeParameter($parameter);
                $this->addElement($newElement);

                // Unrequired parameter has been removed from alternative route and can now be required
                $parameter->required = true;
            }
        }

        // Sort the elements by path
        usort($this->elements, fn($a, $b) => strcmp($a->path, $b->path));
    }

    /**
     * Organize the elements by path
     *
     * @return array
     */
    public function organizeByPath(): array
    {
        $organized = [];
        foreach ($this->elements as $element) {
            $array = $element->toArray();
            foreach ($array as $key => $value) {
                if (!isset($organized[ $key ])) {
                    $organized[ $key ] = [];
                }
                $organized[ $key ] = array_merge(
                    $organized[ $key ],
                    $value
                );
            }
        }

        return $organized;
    }

    /**
     * All URLs need to start with a slash
     *
     * @param $uri
     *
     * @return string
     */
    private function formatUrl($uri): string
    {
        return '/' . ltrim($uri, '/');
    }

    /**
     * Convert the OpenAPI documentation to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $name = config('app.name', 'OpenAPI');
        $env  = config('app.env', 'unknown');
        $url  = config('app.url', 'http://localhost');

        return [
            'openapi' => '3.0.0',
            'servers' => [
                [
                    'description' => "$name - $env server",
                    'url'         => $url,
                ],
            ],
            'info'    => [
                'title'   => 'API Documentation',
                'version' => '1.0.0',
            ],
            'paths'   => $this->organizeByPath(),
        ];
    }
}
