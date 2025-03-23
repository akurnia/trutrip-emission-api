<?php

namespace App\DataTransferObjects;

class EmissionRequestDTO
{
    public string $type;
    public array $parameters;

    public function __construct(array $validatedData)
    {
        $this->type = strtolower($validatedData['type']);
        $this->parameters = $validatedData['parameters'];
    }

    public static function fromRequest(array $validatedData): self
    {
        return new self($validatedData);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
