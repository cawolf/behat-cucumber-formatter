<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Tests\Node;

use PHPUnit\Framework\TestCase;
use Vanare\BehatCucumberJsonFormatter\Node;

class FeatureTest extends TestCase
{
    /**
     * @test
     */
    public function getId(): void
    {
        $name = 'This is a test name, test name for awesome feature';
        $expectedId = 'this-is-a-test-name,-test-name-for-awesome-feature';

        $feature = $this->createFeature();
        $feature->setName($name);

        self::assertEquals($expectedId, $feature->getId());
    }

    /**
     * @test
     */
    public function getUri(): void
    {
        $file = 'features/one_passing_one_failing.feature';

        $feature = $this->createFeature();
        $feature->setFile($file);

        self::assertEquals($file, $feature->getUri());
    }

    protected function createFeature(): Node\Feature
    {
        return new Node\Feature();
    }
}
