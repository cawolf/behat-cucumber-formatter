<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

class Example
{
    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $keyword = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var int
     */
    private $line = 0;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var ExampleRow[]
     */
    private $rows = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword($keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ExampleRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param array $rows
     */
    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }
}
