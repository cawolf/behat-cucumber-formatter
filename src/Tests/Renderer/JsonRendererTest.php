<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Tests\Renderer;

use PHPUnit\Framework\TestCase;
use Vanare\BehatCucumberJsonFormatter\Node;
use Vanare\BehatCucumberJsonFormatter\Renderer\JsonRenderer;
use Vanare\BehatCucumberJsonFormatter\Formatter\FormatterInterface;

class JsonRendererTest extends TestCase
{
    protected $exampleRow;

    protected $example;

    protected $step;

    protected $scenario;

    protected $suite;

    protected $feature;

    /**
     * @var FormatterInterface
     */
    protected $formatter;

    public function setUp(): void
    {
        $this->step = $this->getMockBuilder(Node\Step::class)->getMock();
        $this->example = $this->getMockBuilder(Node\Example::class)->getMock();
        $this->exampleRow = $this->getMockBuilder(Node\ExampleRow::class)->getMock();
        $this->scenario = $this->getMockBuilder(Node\Scenario::class)->getMock();
        $this->suite = $this->getMockBuilder(Node\Suite::class)->getMock();
        $this->feature = $this->getMockBuilder(Node\Feature::class)->getMock();
        $this->formatter = $this->getMockBuilder(FormatterInterface::class)->getMock();
    }

    /**
     * @test
     */
    public function renderShouldNotFailsIfWeGaveEmptyScenariosList(): void
    {
        $this->feature
            ->method('getScenarios')
            ->willReturn(null)
        ;

        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();
        self::assertNotEmpty($renderer->getResult(false));
    }

    /**
     * @test
     */
    public function renderShouldGenerateValidStructure(): void
    {
        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();
        $result = $renderer->getResult(false);

        self::assertIsArray($result);
        self::assertCount(1, $result);

        /*
         * Run through structure
         */

        // Suite
        $suite = array_pop($result);
        self::assertIsArray($suite);
        self::assertCount(2, $suite);

        // Feature
        $feature = array_pop($suite);
        $keys = ['uri', 'id', 'keyword', 'name', 'line', 'description', 'elements', 'tags'];
        self::assertArrayHasKeys($keys, $feature);
        self::assertIsArray($feature['elements']);
        self::assertCount(2, $feature['elements']);
        self::assertCount(2, $feature['tags']);

        // Scenario
        $scenario = array_pop($feature['elements']);
        $keys = ['id', 'keyword', 'name', 'line', 'description', 'type', 'steps', 'tags'];
        self::assertArrayHasKeys($keys, $scenario);
        self::assertIsArray($scenario['steps']);
        self::assertIsArray($scenario['examples']);
        self::assertCount(3, $scenario['steps']);
        self::assertCount(2, $scenario['examples']);
        self::assertCount(2, $scenario['tags']);

        // Step
        $step = array_pop($scenario['steps']);
        $keys = ['keyword', 'name', 'line', 'match', 'result'];
        self::assertArrayHasKeys($keys, $step);

        // Example
        $example = array_pop($scenario['examples']);
        $keys = ['keyword', 'name', 'line', 'description', 'id', 'rows'];
        self::assertArrayHasKeys($keys, $example);
        self::assertIsArray($example['rows']);
        self::assertCount(2, $example['rows']);

        // ExampleRow
        $row = array_pop($example['rows']);
        $keys = ['cells', 'line', 'id'];
        self::assertArrayHasKeys($keys, $row);
    }

    /**
     * @test
     */
    public function getResultShouldReturnValidJsonString(): void
    {
        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();

        self::assertJson($renderer->getResult());
    }

    protected function createRenderer(): JsonRenderer
    {
        return new JsonRenderer($this->formatter);
    }

    protected function generateMockStructure(): void
    {
        $this->example
            ->method('getRows')
            ->willReturn(
                [$this->exampleRow, $this->exampleRow,]
            );

        $this->scenario
            ->method('getSteps')
            ->willReturn(
                [$this->step, $this->step, $this->step,]
            );

        $this->scenario
            ->method('getExamples')
            ->willReturn(
                [$this->example, $this->example,]
            );

        $this->scenario
            ->method('getTags')
            ->willReturn(
                ['tag1', 'tag2']
            );

        $this->feature
            ->method('getScenarios')
            ->willReturn(
                [$this->scenario, $this->scenario,]
            );

        $this->feature
            ->method('getTags')
            ->willReturn(
                ['tag1', 'tag2']
            );

        $this->suite
            ->method('getFeatures')
            ->willReturn(
                [$this->feature, $this->feature,]
            );

        $this->formatter
            ->method('getSuites')
            ->willReturn(
                [$this->suite,]
            );
    }

    protected static function assertArrayHasKeys(array $keys, array $array): void
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $array);
        }
    }
}
