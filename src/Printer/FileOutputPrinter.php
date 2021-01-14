<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Printer;

use Behat\Testwork\Output\Exception\BadOutputPathException;
use Behat\Testwork\Output\Printer\OutputPrinter as OutputPrinterInterface;

class FileOutputPrinter implements OutputPrinterInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $fileNamePrefix;

    /**
     * @var string
     */
    private $resultFileName = '';

    public function __construct(string $fileNamePrefix, string $path)
    {
        $this->fileNamePrefix = $fileNamePrefix;
        $this->setOutputPath($path);
    }

    /**
     * Sets output path.
     *
     * @param string $path
     */
    public function setOutputPath($path): void
    {
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new BadOutputPathException(
                    sprintf(
                        'Output path %s does not exist and could not be created!',
                        $path
                    ),
                    $path
                );
            }
        } else if (!is_dir($path)) {
            throw new BadOutputPathException(
                sprintf(
                    'The argument to `output` is expected to the a directory, but got %s!',
                    $path
                ),
                $path
            );
        }
        $this->path = $path;
    }

    public function setResultFileName(string $resultFileName): void
    {
        $this->resultFileName = $resultFileName;
    }

    public function getResultFileName(): string
    {
        return $this->resultFileName;
    }

    /** @inheritdoc */
    public function getOutputPath(): ?string
    {
        return $this->path;
    }

    /** @inheritdoc */
    public function setOutputStyles(array $styles): void
    {
    }

    /** @inheritdoc */
    public function getOutputStyles(): array
    {
        return [];
    }

    /** @inheritdoc */
    public function setOutputDecorated($decorated): void
    {
    }

    /** @inheritdoc */
    public function isOutputDecorated(): ?bool
    {
        return null;
    }

    /** @inheritdoc */
    public function setOutputVerbosity($level): void
    {
    }

    /** @inheritdoc */
    public function getOutputVerbosity(): int
    {
        return 0;
    }

    /** @inheritdoc */
    public function write($messages, $append = false): void
    {
        $file = $this->getOutputPath() . DIRECTORY_SEPARATOR . $this->fileNamePrefix . $this->resultFileName . '.json';

        if ($append) {
            file_put_contents($file, $messages, FILE_APPEND);
        } else {
            file_put_contents($file, $messages);
        }
    }

    /** @inheritdoc */
    public function writeln($messages = ''): void
    {
        $this->write($messages, true);
    }

    /** @inheritdoc */
    public function flush(): void
    {
    }
}
