<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

class Scenario
{
    /**
     * @var Feature
     */
    private $feature;

    /**
     * @var string
     */
    private $keyword = '';

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var Example[]
     */
    private $examples = [];

    /**
     * @var mixed
     */
    private $name;

    /**
     * @var mixed
     */
    private $line;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var mixed
     */
    private $loopCount;

    /**
     * @var bool
     */
    private $passed;

    /**
     * @var Step[]
     */
    private $steps;

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getType(): string
    {
        return mb_strtolower($this->type, 'UTF-8');
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Example[]
     */
    public function getExamples(): array
    {
        return $this->examples;
    }

    /**
     * @param Example[] $examples
     */
    public function setExamples(array $examples): void
    {
        $this->examples = $examples;
    }

    public function getFeature(): Feature
    {
        return $this->feature;
    }

    public function setFeature(Feature $feature): void
    {
        $this->feature = $feature;
    }

    public function getId(): string
    {
        return sprintf(
            '%s;%s%s',
            $this->getFeature()->getId(),
            preg_replace('/\s/', '-', mb_strtolower($this->getName(), 'UTF-8')),
            $this->getType() === 'scenario_outline' ? '---' . $this->getLine() : ''
        );
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getLoopCount(): int
    {
        return $this->loopCount;
    }

    public function setLoopCount(int $loopCount): void
    {
        $this->loopCount = $loopCount;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function setPassed(bool $passed): void
    {
        $this->passed = $passed;
    }

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param Step[] $steps
     */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    public function addStep(Step $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return float|int
     */
    public function getLoopSize()
    {
        return $this->loopCount > 0 ? count($this->steps) / $this->loopCount : count($this->steps);
    }
}
