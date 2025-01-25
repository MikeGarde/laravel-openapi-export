<?php

namespace MikeGarde\OpenApiExport;

class OpenApiElement
{
    public string $path;
    public string $method;
    public string $summary;
    /** @var OpenApiParameter[] */
    public array $parameters = [];
    public array $responses  = [];

    /**
     * OpenApiElement constructor.
     *
     * @param string $path
     * @param string $method
     * @param string $summary
     * @param array  $parameters
     * @param array  $responses
     */
    public function __construct(string $path, string $method, string $summary = '', array $parameters = [],
                                array  $responses = [])
    {
        $this->path       = $path;
        $this->method     = strtolower($method); // Normalize the HTTP method to lowercase
        $this->summary    = $summary;
        $this->parameters = $parameters;
        $this->responses  = $responses;
    }

    /**
     * Add a parameter to the element
     *
     * @param OpenApiParameter $parameter
     */
    public function addParameter(OpenApiParameter $parameter): void
    {
        $this->parameters[] = $parameter;

        // If parameter is in the path and it is not required, remove the question mark from the path
        if ($parameter->in === OpenApiParameter::LOCATION_PATH && !$parameter->required) {
            $this->path = str_replace($parameter->name . '?', $parameter->name, $this->path);
        }
    }

    /**
     * Remove a parameter from the element
     *
     * @param OpenApiParameter $parameter
     */
    public function removeParameter(OpenApiParameter $parameter): void
    {
        $this->path       = str_replace('{' . $parameter->name . '}', '', $this->path);
        $this->parameters = array_filter($this->parameters, fn($p) => $p->name !== $parameter->name);
    }

    /**
     * Align the parameters in the element
     */
    public function alignParameters(): void
    {
        $parameterHolder  = $this->parameters;
        $this->parameters = [];
        foreach ($parameterHolder as $parameter) {
            $this->addParameter($parameter);
        }
    }

    /**
     * Returns the parameters that are not required
     *
     * @return OpenApiParameter[]
     */
    public function unrequiredParameters(): array
    {
        return array_filter($this->parameters, fn($parameter) => !$parameter->required);
    }

    /**
     * Returns true if all parameters are required
     *
     * @return bool
     */
    public function allParametersRequired(): bool
    {
        return empty($this->unrequiredParameters());
    }

    /**
     * Convert the element to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $this->alignParameters();

        $response = [
            $this->path => [
                $this->method => [
                    'summary'    => $this->summary,
                    'parameters' => array_map(fn($parameter) => $parameter->toArray(), $this->parameters),
                    'responses'  => $this->responses,
                ],
            ],
        ];

        // remove parameters if empty
        if (empty($response[ $this->path ][ $this->method ]['parameters'])) {
            unset($response[ $this->path ][ $this->method ]['parameters']);
        }

        return $response;
    }
}
