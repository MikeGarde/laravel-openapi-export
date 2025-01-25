<?php

namespace MikeGarde\OpenApiExport;

class OpenApiParameter
{
    public const LOCATION_PATH   = 'path';
    public const LOCATION_QUERY  = 'query';
    public const LOCATION_HEADER = 'header';

    /**
     * OpenApiParameter constructor.
     *
     * @param string $name
     * @param string $in
     * @param bool   $required
     * @param string $schema
     */
    public function __construct(
        public string $name,
        public string $in = self::LOCATION_PATH,
        public bool   $required = true,
        public string $schema = 'string',
    )
    {
        // If the name ends with a question mark, it's optional
        if (str_ends_with($name, '?')) {
            $this->required = false;
            $this->name     = substr($name, 0, -1);
        }

        // Validate
        if (!$this->validate()) {
            throw new \InvalidArgumentException('Invalid location');
        }
    }

    /**
     * Validate the location
     *
     * @return bool
     */
    private function validate(): bool
    {
        return in_array($this->in, [
            self::LOCATION_PATH,
            self::LOCATION_QUERY,
            self::LOCATION_HEADER,
        ]);
    }

    /**
     * Convert the parameter to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $response = [
            'name'     => $this->name,
            'in'       => $this->in,
            'required' => $this->required,
            'schema'   => [
                'type' => $this->schema,
            ],
        ];

        return $response;
    }
}
