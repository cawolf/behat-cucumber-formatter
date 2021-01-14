<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;

class ExampleFeatureContext implements Context
{
    private $hungryCounter = 0;

    /**
     * @When I sum the numbers
     */
    public function passing0(): void
    {
    }

    /**
     * @Then I should have :arg1 as result
     * @Given I have :arg1 cukes
     */
    public function passing1($arg1): void
    {
        $this->hungryCounter += $arg1;
    }

    /**
     * @When I eat :arg1 cukes
     */
    public function eat($arg1): void
    {
        $this->hungryCounter -= $arg1;
    }

    /**
     * @Then Am I hungry? :arg1
     */
    public function hungry($arg1): void
    {
        if ($this->hungryCounter <= 0) {
            throw new RuntimeException("java.lang.AssertionError: expected:<true> but was:<false>\n\tat org.junit.Assert.fail(Assert.java:88)\n\tat org.junit.Assert.failNotEquals(Assert.java:834)\n\tat org.junit.Assert.assertEquals(Assert.java:118)\n\tat org.junit.Assert.assertEquals(Assert.java:144)\n\tat com.github.cukedoctor.example.EatCukesSteps.amIHungry(EatCukesSteps.java:29)\n\tat ✽.Then Am I hungry? \"true\"(src/test/resources/features/eat-cukes.feature:7)\n");
        }
    }

    /**
     * @Given I have numbers :arg1 and :arg2
     */
    public function passing2($arg1, $arg2): void
    {
    }
}
