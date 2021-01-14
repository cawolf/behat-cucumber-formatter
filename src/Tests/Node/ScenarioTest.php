<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Tests\Node;

use PHPUnit\Framework\TestCase;
use Vanare\BehatCucumberJsonFormatter\Node;

class ScenarioTest extends TestCase
{

    private const FEATURE_ID = 'test-feature';

    /**
     * @test
     */
    public function getId(): void
    {
        $name = 'This is a test name, test name for awesome feature';
        $expectedId = sprintf('%s;this-is-a-test-name,-test-name-for-awesome-feature', static::FEATURE_ID);

        $scenario = $this->createScenario();
        $scenario->setName($name);

        static::assertEquals($expectedId, $scenario->getId());
    }

    protected function createScenario(): Node\Scenario
    {

        $feature = $this
            ->getMockBuilder(Node\Feature::class)
            ->getMock();

        $feature
            ->method('getId')
            ->willReturn(static::FEATURE_ID);

        $scenario = new Node\Scenario();
        $scenario->setFeature($feature);

        return $scenario;
    }
}
