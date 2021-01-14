<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Formatter;

use Behat\Testwork\Output\Formatter as FormatterOutputInterface;
use Vanare\BehatCucumberJsonFormatter\Node\Suite;

interface FormatterInterface extends FormatterOutputInterface
{
    /**
     * Returns all executed suites.
     *
     * @return Suite[]
     */
    public function getSuites(): array;

    /**
     * Set a fixed filename, which will override the current feature filename.
     */
    public function setFileName(string $fileName);
}
