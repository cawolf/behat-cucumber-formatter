<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter\Tests\Printer;

use Behat\Testwork\Output\Exception\BadOutputPathException;
use PHPUnit\Framework\TestCase;
use Vanare\BehatCucumberJsonFormatter\Printer\FileOutputPrinter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class FileOutputPrinterTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    protected $validRoot;

    public function setUp(): void
    {
        $this->validRoot = vfsStream::setup('root', 0775);
    }

    /**
     * @test
     */
    public function setOutputPathExisted(): void
    {
        $path = $this->validRoot->url();

        $printer = $this->createPrinter($path);

        self::assertEquals($path, $printer->getOutputPath());
    }

    /**
     * @test
     */
    public function setOutputPathNotExisted(): void
    {
        $path = $this->validRoot->url().'/build_666';

        $printer = $this->createPrinter($path);

        self::assertEquals($path, $printer->getOutputPath());
        self::assertEquals(0755, $this->validRoot->getChild('build_666')->getPermissions());
    }

    /**
     * @test
     */
    public function setOutputPathShouldRaiseExceptionIfPathCanNotBeCreated(): void
    {
        vfsStream::newDirectory('secured_folder', 0000)->at($this->validRoot);

        $path = $this->validRoot->getChild('secured_folder')->url().'/build_666';

        $this->expectException(BadOutputPathException::class);

        $this->createPrinter($path);
    }

    /**
     * @test
     */
    public function setOutputPathShouldRaiseExceptionIfPathIsNotADirectory(): void
    {
        vfsStream::newFile('file.exe', 0755)->at($this->validRoot);

        $path = $this->validRoot->getChild('file.exe')->url();

        $this->expectException(BadOutputPathException::class);

        $this->createPrinter($path);
    }

    /**
     * @test
     */
    public function write(): void
    {
        $messages = 'Messages will be here';

        $printer = $this->createPrinter($this->validRoot->url());
        $printer->write($messages);

        /** @var vfsStreamStructureVisitor $visitor */
        $visitor = vfsStream::inspect(new vfsStreamStructureVisitor());
        $expectedStructure = $visitor->getStructure();

        // Assert that string was written
        self::assertEquals(['root' => ['testprefix.json' => $messages]], $expectedStructure);
    }

    /**
     * @test
     */
    public function setResultFileNameAndWriteLn(): void
    {
        $messages = 'Messages will be here';

        $printer = $this->createPrinter($this->validRoot->url());
        $printer->setResultFileName('agreatsuffix');
        $printer->writeln($messages);

        /** @var vfsStreamStructureVisitor $visitor */
        $visitor = vfsStream::inspect(new vfsStreamStructureVisitor());
        $expectedStructure = $visitor->getStructure();

        // Assert that string was written
        self::assertEquals(['root' => ['testprefixagreatsuffix.json' => $messages]], $expectedStructure);
    }

    /**
     * @test
     */
    public function inheritedUnusedInterface(): void
    {
        $printer = $this->createPrinter($this->validRoot->url());
        $printer->setOutputStyles([]);
        self::assertEquals([], $printer->getOutputStyles());
        $printer->setOutputDecorated(false);
        self::assertNull($printer->isOutputDecorated());
        $printer->setOutputVerbosity(0);
        self::assertEquals(0, $printer->getOutputVerbosity());
        $printer->flush();
    }

    protected function createPrinter($path): FileOutputPrinter
    {
        return new FileOutputPrinter('testprefix', $path);
    }
}
