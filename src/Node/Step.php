<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Node;

use Behat\Behat\Definition\Definition;
use Behat\Behat\Tester\Result\StepResult;

class Step
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array
     */
    private $match = [ 'location' => '' ];

    /**
     * @var int
     */
    private $duration = 0;

    /**
     * @var array
     */
    public static $resultLabels = [
        StepResult::FAILED => 'failed',
        StepResult::PASSED => 'passed',
        StepResult::SKIPPED => 'skipped',
        StepResult::PENDING => 'pending',
        StepResult::UNDEFINED => 'pending',
    ];

    /**
     * @var string
     */
    private $keyword = '';

    /**
     * @var mixed
     */
    private $text;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var int
     */
    private $line;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var mixed
     */
    private $resultCode;

    /**
     * @var string
     */
    private $exception = '';

    /**
     * @var mixed
     */
    private $output;

    /**
     * @var ?Definition
     */
    private $definition;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMatch(): array
    {
        return $this->match;
    }

    public function setMatch(array $match): void
    {
        $this->match = $match;
    }

    public function getProcessedResult(): array
    {
        $status = StepResult::SKIPPED;

        if (!empty(static::$resultLabels[$this->getResultCode()])) {
            $status = static::$resultLabels[$this->getResultCode()];
        }

        $result = [
            'status' => $status,
            'duration' => $this->getDuration() * 1000 * 1000000,
        ];
        if ($this->getException()) {
            $result['error_message'] = $this->getException();
        }

        return $result;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getException(): string
    {
        return $this->exception;
    }

    public function setException(string $exception): void
    {
        $this->exception = $exception;
    }

    public function getDefinition(): ?Definition
    {
        return $this->definition;
    }

    public function setDefinition(?Definition $definition): void
    {
        $this->definition = $definition;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }

    /**
     * @param mixed $resultCode
     */
    public function setResultCode($resultCode)
    {
        $this->resultCode = $resultCode;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration($duration): void
    {
        $this->duration = (int) $duration;
    }
}
