<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Formatter;

use Behat\Behat\EventDispatcher\Event as BehatEvent;
use Behat\Behat\Tester\Result;
use Behat\Testwork\Counter\Timer;
use Behat\Testwork\EventDispatcher\Event as TestworkEvent;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Result\TestResults;
use Vanare\BehatCucumberJsonFormatter\Node;
use Vanare\BehatCucumberJsonFormatter\Printer\FileOutputPrinter;
use Vanare\BehatCucumberJsonFormatter\Renderer\JsonRenderer;
use Vanare\BehatCucumberJsonFormatter\Renderer\RendererInterface;

class Formatter implements FormatterInterface
{
    /** @var array */
    private $parameters;

    /** @var Timer */
    private $timer;

    /** @var FileOutputPrinter */
    private $printer;

    /** @var RendererInterface */
    private $renderer;

    /** @var Node\Suite[] */
    private $suites;

    /** @var Node\Suite */
    private $currentSuite;

    /** @var int */
    private $featureCounter = 1;

    /** @var Node\Feature */
    private $currentFeature;

    /** @var Node\Scenario */
    private $currentScenario;

    /** @var bool */
    private $resultFilePerSuite = false;

    public function __construct(string $fileNamePrefix, string $outputDir)
    {
        $this->renderer = new JsonRenderer($this);
        $this->printer = new FileOutputPrinter($fileNamePrefix, $outputDir);
        $this->timer = new Timer();
    }

    /** @inheritdoc */
    public static function getSubscribedEvents(): array
    {
        return [
            TestworkEvent\ExerciseCompleted::BEFORE => 'onBeforeExercise',
            TestworkEvent\ExerciseCompleted::AFTER => 'onAfterExercise',
            TestworkEvent\SuiteTested::BEFORE => 'onBeforeSuiteTested',
            TestworkEvent\SuiteTested::AFTER => 'onAfterSuiteTested',
            BehatEvent\FeatureTested::BEFORE => 'onBeforeFeatureTested',
            BehatEvent\FeatureTested::AFTER => 'onAfterFeatureTested',
            BehatEvent\ScenarioTested::BEFORE => 'onBeforeScenarioTested',
            BehatEvent\ScenarioTested::AFTER => 'onAfterScenarioTested',
            BehatEvent\OutlineTested::BEFORE => 'onBeforeOutlineTested',
            BehatEvent\OutlineTested::AFTER => 'onAfterOutlineTested',
            BehatEvent\StepTested::BEFORE => 'onBeforeStepTested',
            BehatEvent\StepTested::AFTER => 'onAfterStepTested',
        ];
    }

    /** @inheritdoc */
    public function setFileName($fileName): void
    {
        $this->printer->setResultFileName($fileName);
    }

    /** @inheritdoc */
    public function setResultFilePerSuite(bool $enabled): void
    {
        $this->resultFilePerSuite = $enabled;
    }

    /** @inheritdoc */
    public function getDescription(): string
    {
        return 'Cucumber style formatter';
    }

    /** @inheritdoc */
    public function getOutputPrinter(): OutputPrinter
    {
        return $this->printer;
    }

    /** @inheritdoc */
    public function setParameter($name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /** @inheritdoc */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /** @inheritdoc */
    public function getSuites(): array
    {
        return $this->suites;
    }

    /**
     * Triggers before running tests.
     */
    public function onBeforeExercise(TestworkEvent\BeforeExerciseCompleted $event): void
    {
        $this->timer->start();
    }

    /**
     * Triggers after running tests.
     */
    public function onAfterExercise(TestworkEvent\ExerciseCompleted $event): void
    {
        $this->timer->stop();

        $this->renderer->render();

        if ($this->resultFilePerSuite) {
            foreach ($this->suites as $suite) {
                $this->printer->setResultFileName($suite->getFilenameForReport());
                $suiteResult = $this->renderer->getResultForSuite($suite->getName());
                $this->printer->write($suiteResult);
            }
            return;
        }

        if (!$this->printer->getResultFileName()) {
            $this->printer->setResultFileName('all');
        }

        $this->printer->write($this->renderer->getResult());
    }

    /**
     * @param TestworkEvent\BeforeSuiteTested $event
     */
    public function onBeforeSuiteTested(TestworkEvent\BeforeSuiteTested $event): void
    {
        $this->currentSuite = new Node\Suite();
        $this->currentSuite->setName($event->getSuite()->getName());
    }

    /**
     * @param TestworkEvent\SuiteTested $event
     */
    public function onAfterSuiteTested(TestworkEvent\SuiteTested $event): void
    {
        $this->suites[] = $this->currentSuite;
    }

    /**
     * @param BehatEvent\BeforeFeatureTested $event
     */
    public function onBeforeFeatureTested(BehatEvent\BeforeFeatureTested $event): void
    {
        $feature = new Node\Feature();
        # FIXME $feature->setId($this->featureCounter);
        ++$this->featureCounter;
        $feature->setName($event->getFeature()->getTitle());
        $feature->setDescription($event->getFeature()->getDescription());
        $feature->setTags($event->getFeature()->getTags());
        $feature->setFile($event->getFeature()->getFile());
        $feature->setLine($event->getFeature()->getLine());
        $feature->setKeyword($event->getFeature()->getKeyword());
        $this->currentFeature = $feature;
    }

    /**
     * @param BehatEvent\AfterFeatureTested $event
     */
    public function onAfterFeatureTested(BehatEvent\AfterFeatureTested $event): void
    {
        $this->currentSuite->addFeature($this->currentFeature);
    }

    /**
     * @param BehatEvent\BeforeScenarioTested $event
     */
    public function onBeforeScenarioTested(BehatEvent\BeforeScenarioTested $event): void
    {
        $fullTitle = explode("\n", $event->getScenario()->getTitle());
        if (count($fullTitle) > 1) {
            $title = array_shift($fullTitle);
        } else {
            $title = implode("\n", $fullTitle);
        }
        $description = implode("\n", $fullTitle);

        $scenario = new Node\Scenario();
        $scenario->setName($title);
        $scenario->setDescription($description);
        $scenario->setTags($event->getScenario()->getTags());
        $scenario->setLine($event->getScenario()->getLine());
        $scenario->setType($event->getScenario()->getNodeType());
        $scenario->setKeyword($event->getScenario()->getKeyword());
        $scenario->setFeature($this->currentFeature);
        $this->currentScenario = $scenario;
    }

    /**
     * @param BehatEvent\AfterScenarioTested $event
     */
    public function onAfterScenarioTested(BehatEvent\AfterScenarioTested $event): void
    {
        $scenarioPassed = $event->getTestResult()->isPassed();

        if ($scenarioPassed) {
            $this->currentFeature->addPassedScenario();
        } else {
            $this->currentFeature->addFailedScenario();
        }

        $this->currentScenario->setPassed($event->getTestResult()->isPassed());
        $this->currentFeature->addScenario($this->currentScenario);
    }

    /**
     * @param BehatEvent\BeforeOutlineTested $event
     */
    public function onBeforeOutlineTested(BehatEvent\BeforeOutlineTested $event): void
    {
        $scenario = new Node\Scenario();
        $scenario->setName($event->getOutline()->getTitle());
        $scenario->setTags($event->getOutline()->getTags());
        $scenario->setLine($event->getOutline()->getLine());
        $scenario->setType('scenario_outline');
        $scenario->setKeyword($event->getOutline()->getKeyword());
        $scenario->setFeature($this->currentFeature);
        $this->currentScenario = $scenario;
    }

    /**
     * @param BehatEvent\AfterOutlineTested $event
     */
    public function onAfterOutlineTested(BehatEvent\AfterOutlineTested $event): void
    {
        /** @var TestResults $testResults */
        $testResults = $event->getTestResult();
        $stepCount = count($event->getOutline()->getSteps());
        foreach ($testResults as $i => $testResult) {
            $example = clone $this->currentScenario;

            // use correct line number of example row
            $line = $event->getOutline()->getExampleTable()->getRowLine($i + 1);
            $example->setLine($line);

            // remove all steps and attach only steps for that example row
            $steps = array_slice($example->getSteps(), $i * $stepCount, $stepCount);
            $example->setSteps($steps);

            $scenarioPassed = $testResult->isPassed();

            if ($scenarioPassed) {
                $this->currentFeature->addPassedScenario();
            } else {
                $this->currentFeature->addFailedScenario();
            }

            $example->setPassed($scenarioPassed);
            $this->currentFeature->addScenario($example);
        }
    }

    /**
     * @param BehatEvent\BeforeStepTested $event
     */
    public function onBeforeStepTested(BehatEvent\BeforeStepTested $event): void
    {
        $this->timer->start();
    }

    /**
     * @param BehatEvent\AfterStepTested $event
     */
    public function onAfterStepTested(BehatEvent\AfterStepTested $event): void
    {
        $this->timer->stop();

        /** @var Result\ExecutedStepResult $result */
        $result = $event->getTestResult();

        if ($result instanceof Result\UndefinedStepResult || $result instanceof Result\SkippedStepResult) {
            return;
        }

        $step = new Node\Step();
        $step->setKeyword($event->getStep()->getKeyword());
        $step->setName($event->getStep()->getText());
        $step->setLine($event->getStep()->getLine());
        $step->setArguments($event->getStep()->getArguments());
        $step->setResult($result);
        $step->setResultCode($result->getResultCode());
        $step->setDuration($this->timer->getSeconds());


        $match = ['location' => $result->getStepDefinition()->getPath()];
        $arguments = [];
        foreach ($result->getSearchResult()->getMatchedArguments() as $argument) {
            $a = new \stdClass();
            $a->val = (string) $argument;
            $arguments[] = $a;
        }
        if ($arguments) {
            $match['arguments'] = $arguments;
        }

        $step->setMatch($match);

        $this->processStep($step, $result);

        $this->currentScenario->addStep($step);
    }

    /** @inheritdoc */
    public function getName(): string
    {
        return 'cucumber_json';
    }

    /**
     * @param Node\Step  $step
     * @param TestResult $result
     */
    protected function processStep(Node\Step $step, TestResult $result): void
    {
        // Pended
        if (is_a($result, Result\UndefinedStepResult::class)) {
            return;
        }

        // Skipped
        if (is_a($result, Result\SkippedStepResult::class)) {
            /** @var Result\SkippedStepResult $result */
            $step->setDefinition($result->getStepDefinition());

            return;
        }

        // Failed or passed
        if (is_a($result, Result\ExecutedStepResult::class)) {
            /** @var Result\ExecutedStepResult $result */
            $step->setDefinition($result->getStepDefinition());
            $exception = $result->getException();
            if ($exception) {
                $step->setException($exception->getMessage());
            }

            return;
        }
    }
}
