<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Renderer;

use Vanare\BehatCucumberJsonFormatter\Formatter\FormatterInterface;
use Vanare\BehatCucumberJsonFormatter\Node;

class JsonRenderer implements RendererInterface
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @var array
     */
    protected $result = [];

    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /** @inheritdoc */
    public function render(): void
    {
        $suites = $this->formatter->getSuites();

        if (is_array($suites)) {
            foreach ($suites as $suite) {
                $this->result[$suite->getName()] = $this->processSuite($suite);
            }
        }
    }

    /** @inheritdoc */
    public function getResult(bool $asString = true)
    {
        if ($asString) {
            $mergedResults = [];
            foreach ($this->result as $result) {
                $mergedResults = array_merge($mergedResults, $result);
            }
            return json_encode($mergedResults);
        }

        return $this->result;
    }

    public function getResultForSuite(string $suiteName, bool $asString = true)
    {
        $result = $this->result[$suiteName] ?? null;

        if ($asString) {
            return json_encode($result);
        }
        return $result;
    }

    /**
     * @param Node\Suite $suite
     */
    protected function processSuite(Node\Suite $suite): array
    {
        $currentSuite = [];

        if (is_array($suite->getFeatures())) {
            foreach ($suite->getFeatures() as $feature) {
                array_push($currentSuite, $this->processFeature($feature));
            }
        }

        return $currentSuite;
    }

    /**
     * @param Node\Feature $feature
     */
    protected function processFeature(Node\Feature $feature): array
    {
        $currentFeature = [
            'uri' => $feature->getUri(),
            'id' => $feature->getId(),
            'keyword' => $feature->getKeyword(),
            'name' => $feature->getName(),
            'line' => $feature->getLine(),
            'description' => $feature->getDescription() ?: '',
        ];

        if ($feature->getTags()) {
            $currentFeature['tags'] = $this->processTags($feature->getTags());
        }

        if ($feature->getScenarios()) {
            $currentFeature['elements'] = [];
            foreach ($feature->getScenarios() as $scenario) {
                array_push($currentFeature['elements'], $this->processScenario($scenario));
            }
        }

        return $currentFeature;
    }

    /**
     * @param Node\Scenario $scenario
     */
    protected function processScenario(Node\Scenario $scenario): array
    {
        $currentScenario = [
            'id' => $scenario->getId(),
            'keyword' => $scenario->getKeyword(),
            'name' => $scenario->getName(),
            'line' => $scenario->getLine(),
            'description' => $scenario->getDescription(),
            'type' => $scenario->getType(),
            'steps' => [],
        ];

        if ($scenario->getTags()) {
            $currentScenario['tags'] = $this->processTags($scenario->getTags());
        }

        if ($scenario->getSteps()) {
            foreach ($scenario->getSteps() as $step) {
                array_push($currentScenario['steps'], $this->processStep($step));
            }
        }

        if ($scenario->getExamples()) {
            $currentScenario['examples'] = [];
            foreach ($scenario->getExamples() as $example) {
                array_push($currentScenario['examples'], $this->processExample($example));
            }
        }

        return $currentScenario;
    }

    /**
     * @param Node\Step $step
     */
    protected function processStep(Node\Step $step): array
    {
        return [
            'keyword' => $step->getKeyword(),
            'name' => $step->getName(),
            'line' => $step->getLine(),
            'match' => $step->getMatch(),
            'result' => $step->getProcessedResult(),
        ];
    }

    /**
     * @param Node\Example $example
     */
    protected function processExample(Node\Example $example): array
    {
        $currentExample = [
            'keyword' => $example->getKeyword(),
            'name' => $example->getName(),
            'line' => $example->getLine(),
            'description' => $example->getDescription(),
            'id' => $example->getId(),
            'rows' => [],
        ];

        if (is_array($example->getRows())) {
            foreach ($example->getRows() as $row) {
                array_push($currentExample['rows'], $this->processExampleRow($row));
            }
        }

        return $currentExample;
    }

    /**
     * @param Node\ExampleRow $exampleRow
     */
    protected function processExampleRow(Node\ExampleRow $exampleRow): array
    {
        return [
            'cells' => $exampleRow->getCells(),
            'id' => $exampleRow->getId(),
            'line' => $exampleRow->getLine(),
        ];
    }

    protected function processTags(array $tags): array
    {
        $result = [];

        foreach ($tags as $tag) {
            $result[] = [
                'name' => sprintf('@%s', $tag),
            ];
        }

        return $result;
    }
}
