<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Renderer;

interface RendererInterface
{
    /**
     * Renders the internal array representation of the JSON result.
     */
    public function render(): void;

    /**
     * Returns the JSON result as array or string representation.
     *
     * @return array|string
     */
    public function getResult(bool $asString = true);

    /**
     * Returns the JSON result as an array or string representation for a suite with a given name.
     *
     * @return array|string
     */
    public function getResultForSuite(string $suiteName, bool $asString = true);
}
