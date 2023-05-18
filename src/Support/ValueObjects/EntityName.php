<?php

namespace Anteris\Autotask\Generator\Support\ValueObjects;

use Anteris\Autotask\Generator\Helpers\Str;

final readonly class EntityName
{
    public string $plural;
    public string $pluralSnake;
    public string $singular;
    public string $singularSnake;

    public function __construct(string $name)
    {
        $name = ucfirst($name);

        $this->plural = Str::pluralStudly($name);
        $this->singular = Str::singular($name);
        $this->pluralSnake = Str::snake($this->plural);
        $this->singularSnake = Str::snake($this->singular);
    }

    public static function fromString(string $string): EntityName
    {
        return new self($string);
    }
}
