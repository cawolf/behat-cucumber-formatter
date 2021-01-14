<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

class Suite
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array
     */
    private $features = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    /**
     * @param mixed $feature
     */
    public function addFeature($feature): void
    {
        $this->features[] = $feature;
    }

    public function getFilenameForReport(): string
    {
        return $this->getName();
    }
}
