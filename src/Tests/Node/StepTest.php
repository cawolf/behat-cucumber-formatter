<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Tests\Node;

use PHPUnit\Framework\TestCase;
use Vanare\BehatCucumberJsonFormatter\Node\Step;
use Behat\Testwork\Tester\Result\TestResult;

class StepTest extends TestCase
{
    /**
     * @test
     */
    public function getProcessedResultReturnsPassedStructure(): void
    {
        // Arrange
        $passedResult = $this->getMockBuilder(TestResult::class)->getMock();

        // Act
        $resultCode = TestResult::PASSED;
        $step = $this->createStep($passedResult, $resultCode);
        $result = $step->getProcessedResult();

        // Assert
        static::assertIsArray($result);
        static::assertArrayHasKey('status', $result);
        static::assertEquals(Step::$resultLabels[$resultCode], $result['status']);
        static::assertArrayHasKey('duration', $result);
    }

    /**
     * @test
     */
    public function getProcessedResultReturnsFailedStructure(): void
    {
        // Arrange
        $failedResult = $this->getMockBuilder(TestResult::class)->getMock();

        // Act
        $resultCode = TestResult::FAILED;
        $step = $this->createStep($failedResult, $resultCode);
        $result = $step->getProcessedResult();

        // Assert
        static::assertIsArray($result);
        static::assertArrayHasKey('status', $result);
        static::assertEquals(Step::$resultLabels[$resultCode], $result['status']);
        static::assertArrayHasKey('duration', $result);
    }

    protected function createStep(TestResult $result, $resultCode): Step
    {
        $step = new Step();
        $step->setResult($result);
        $step->setResultCode($resultCode);

        return $step;
    }
}
