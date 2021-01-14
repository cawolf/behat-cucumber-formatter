<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

class ExampleRow
{
    /**
     * @var array
     */
    private $cells = [];

    /**
     * @var int
     */
    private $line = 0;

    /**
     * @var string
     */
    private $id = '';

    public function getCells(): array
    {
        return $this->cells;
    }

    public function setCells(array $cells): void
    {
        $this->cells = $cells;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
