<?php

namespace Vanare\BehatCucumberJsonFormatter\Tests\Renderer;

use Vanare\BehatCucumberJsonFormatter\Node;
use Vanare\BehatCucumberJsonFormatter\Renderer\JsonRenderer;
use Vanare\BehatCucumberJsonFormatter\Formatter\FormatterInterface;

class JsonRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $exampleRow;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $example;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $step;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scenario;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $suite;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $feature;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormatterInterface
     */
    protected $formatter;

    /** @inheritdoc */
    public function setUp()
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
    public function renderShouldNotFailsIfWeGaveEmptyScenariosList()
    {
        $this->feature
            ->expects(self::any())
            ->method('getScenarios')
            ->will(self::returnValue(null))
        ;

        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();
    }

    /**
     * @test
     */
    public function renderShouldGenerateValidStructure()
    {
        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();
        $result = $renderer->getResult(false);

        self::assertTrue(is_array($result));
        self::assertEquals(1, count($result));

        /*
         * Run through structure
         */

        // Suite
        $suite = array_pop($result);
        self::assertTrue(is_array($suite));
        self::assertEquals(2, count($suite));

        // Feature
        $feature = array_pop($suite);
        $keys = ['uri', 'id', 'keyword', 'name', 'line', 'description', 'elements', 'tags'];
        self::assertArrayHasKeys($keys, $feature);
        self::assertTrue(is_array($feature['elements']));
        self::assertEquals(2, count($feature['elements']));
        self::assertEquals(2, count($feature['tags']));

        // Scenario
        $scenario = array_pop($feature['elements']);
        $keys = ['id', 'keyword', 'name', 'line', 'type', 'steps', 'tags'];
        self::assertArrayHasKeys($keys, $scenario);
        self::assertTrue(is_array($scenario['steps']));
        self::assertTrue(is_array($scenario['examples']));
        self::assertEquals(3, count($scenario['steps']));
        self::assertEquals(2, count($scenario['examples']));
        self::assertEquals(2, count($scenario['tags']));

        // Step
        $step = array_pop($scenario['steps']);
        $keys = ['keyword', 'name', 'line', 'match', 'result'];
        self::assertArrayHasKeys($keys, $step);

        // Example
        $example = array_pop($scenario['examples']);
        $keys = ['keyword', 'name', 'line', 'description', 'id', 'rows'];
        self::assertArrayHasKeys($keys, $example);
        self::assertTrue(is_array($example['rows']));
        self::assertEquals(2, count($example['rows']));

        // ExampleRow
        $row = array_pop($example['rows']);
        $keys = ['cells', 'line', 'id'];
        self::assertArrayHasKeys($keys, $row);
    }

    /**
     * @test
     */
    public function getResultShouldReturnValidJsonString()
    {
        $this->generateMockStructure();

        $renderer = $this->createRenderer();
        $renderer->render();

        self::assertJson($renderer->getResult());
    }

    /**
     * @return JsonRenderer
     */
    protected function createRenderer()
    {
        return new JsonRenderer($this->formatter);
    }

    /**
     *
     */
    protected function generateMockStructure()
    {
        $this->example
            ->expects(self::any())
            ->method('getRows')
            ->will(self::returnValue([
                $this->exampleRow,
                $this->exampleRow,
            ]));

        $this->scenario
            ->expects(self::any())
            ->method('getSteps')
            ->will(self::returnValue([
                $this->step,
                $this->step,
                $this->step,
            ]));

        $this->scenario
            ->expects(self::any())
            ->method('getExamples')
            ->will(self::returnValue([
                $this->example,
                $this->example,
            ]));

        $this->scenario
            ->expects(self::any())
            ->method('getTags')
            ->will(self::returnValue([
                 'tag1',
                 'tag2'
             ]));

        $this->feature
            ->expects(self::any())
            ->method('getScenarios')
            ->will(self::returnValue([
                $this->scenario,
                $this->scenario,
            ]));

        $this->feature
            ->expects(self::any())
            ->method('getTags')
            ->will(self::returnValue([
                'tag1',
                'tag2'
            ]));

        $this->suite
            ->expects(self::any())
            ->method('getFeatures')
            ->will(self::returnValue([
                $this->feature,
                $this->feature,
            ]));

        $this->formatter
            ->expects(self::any())
            ->method('getSuites')
            ->will(self::returnValue([
                $this->suite,
            ]));
    }

    /**
     * @param array  $keys
     * @param array  $array
     * @param string $message
     */
    protected function assertArrayHasKeys(array $keys, array $array, $message = '')
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $array, $message);
        }
    }
}
