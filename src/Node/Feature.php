<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

class Feature
{
    /**
     * @var ?string
     */
    private $name;

    /**
     * @var ?string
     */
    private $description = '';

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @var ?string
     */
    private $file;

    /**
     * @var int
     */
    private $failedScenarios = 0;

    /**
     * @var int
     */
    private $passedScenarios = 0;

    /**
     * @var int
     */
    private $scenarioCounter = 1;

    /**
     * @var string
     */
    private $keyword = '';

    /**
     * @var int
     */
    private $line = 0;

    /**
     * @var Scenario[]
     */
    private $scenarios = [];

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function getUri(): string
    {
        return $this->getFile();
    }

    public function getId(): string
    {
        return preg_replace('/\s/', '-', mb_strtolower($this->getName(), 'UTF-8'));
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getFile(): string
    {
        return $this->file ?? '';
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return Scenario[]
     */
    public function getScenarios(): array
    {
        return $this->scenarios;
    }

    /**
     * @param Scenario[] $scenarios
     */
    public function setScenarios(array $scenarios): void
    {
        $this->scenarios = $scenarios;
    }

    public function addScenario(Scenario $scenario): void
    {
        # FIXME $scenario->setId($this->scenarioCounter);
        $this->scenarioCounter++;
        $this->scenarios[] = $scenario;
    }

    public function getFailedScenarios(): int
    {
        return $this->failedScenarios;
    }

    public function setFailedScenarios(int $failedScenarios): void
    {
        $this->failedScenarios = $failedScenarios;
    }

    public function addFailedScenario(): void
    {
        $this->failedScenarios++;
    }

    public function getPassedScenarios(): int
    {
        return $this->passedScenarios;
    }

    public function setPassedScenarios(int $passedScenarios): void
    {
        $this->passedScenarios = $passedScenarios;
    }

    public function addPassedScenario(): void
    {
        $this->passedScenarios++;
    }

    public function allPassed(): bool
    {
        return $this->failedScenarios === 0;
    }
}
