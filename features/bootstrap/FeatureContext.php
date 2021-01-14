<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var string */
    private $phpBin;

    /** @var Process */
    private $process;

    /** @var string */
    private $workingDir;

    /** @var string */
    private $reportsDir;

    /** @var bool */
    protected $resultFilePerSuiteEnabled;

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     */
    public static function cleanTestFolders(): void
    {
        if (is_dir($dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat')) {
            self::clearDirectory($dir);
        }
    }

    /**
     * Clears a complete directory by path.
     */
    private static function clearDirectory(string $path): void
    {
        $files = scandir($path);
        array_shift($files);
        array_shift($files);
        foreach ($files as $file) {
            $file = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                self::clearDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }

    /**
     * Prepares test folders in the temporary directory.
     *
     * @BeforeScenario
     * @throws Exception
     */
    public function prepareTestFolders(): void
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR .
            md5(microtime() . random_int(0, 10000));
        $this->reportsDir = $dir . DIRECTORY_SEPARATOR . 'reports';

        // create directories
        if (!mkdir(
                $concurrentDirectory = sprintf(
                    '%1$s%2$sfeatures%2$sbootstrap%2$si18n',
                    $dir,
                    DIRECTORY_SEPARATOR
                ),
                0777,
                true
            ) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!mkdir($concurrentDirectory = $dir . DIRECTORY_SEPARATOR . 'junit') && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!mkdir($concurrentDirectory = $this->reportsDir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $this->writeBehatConfigForTests($dir);

        // copy context
        copy(
            sprintf(
                'features%1$sbootstrap%1$sExampleFeatureContext.php',
                DIRECTORY_SEPARATOR
            ),
            sprintf(
                '%1$s%2$sfeatures%2$sbootstrap%2$sExampleFeatureContext.php',
                $dir,
                DIRECTORY_SEPARATOR
            )
        );

        // setup variables
        $phpFinder = new PhpExecutableFinder();
        if (false === $php = $phpFinder->find()) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }
        $this->workingDir = $dir;
        $this->phpBin = $php;
        $this->process = new Process(null);
        $this->process->setTimeout(20);
    }

    /**
     * @Given I have the following feature:
     * @param PyStringNode $string
     */
    public function iHaveTheFollowingFeature(PyStringNode $string): void
    {
        $this->iHaveTheFollowingFeatureFileStoredIn('feature.feature', 'default', $string);
    }

    /**
     * @Given I have the following feature file :fileName stored in :subDirectory:
     */
    public function iHaveTheFollowingFeatureFileStoredIn(string $fileName, string $subDirectory, PyStringNode $string): void
    {
        $filePath = $this->workingDir . '/features' . (!empty($subDirectory) ? '/' . $subDirectory : '') . '/' . $fileName;
        if (!empty($subDirectory) && !file_exists($subDirectory) && !mkdir(
                $concurrentDirectory = dirname($filePath),
                0777,
                true
            ) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        file_put_contents($filePath, $string->getRaw());
    }

    /**
     * @Given I have the enabled the "resultFilePerSuite" option
     */
    public function iHaveTheEnabledTheResultFilePerSuiteOption(): void
    {
        // manipulate the behat config
        $this->resultFilePerSuiteEnabled = true;
        $this->writeBehatConfigForTests($this->workingDir, [
            'resultFilePerSuite' => 'true'
        ]);
    }

    /**
     * @When I run behat with the converter and no specific suite is specified
     */
    public function iRunBehatWithTheConverterAndNoSpecificSuiteIsSpecified(): void
    {
        $this->runBehatWithConverter();
    }

    /**
     * @When I run behat with the converter
     */
    public function iRunBehatWithTheConverter(): void
    {
        $this->runBehatWithConverter('-s default');
    }

    protected function runBehatWithConverter(string $extraParameters = null): void
    {
        $this->process->setWorkingDirectory($this->workingDir);
        $this->process->setCommandLine(
            sprintf(
                '%s %s -c %s %s --no-interaction -f cucumber_json',
                $this->phpBin,
                escapeshellarg(BEHAT_BIN_PATH),
                $this->workingDir . DIRECTORY_SEPARATOR . 'behat.yml',
                !empty($extraParameters) ? $extraParameters : ''
            )
        );
        // Don't reset the LANG variable on HHVM, because it breaks HHVM itself
        if (!defined('HHVM_VERSION')) {
            $env = $this->process->getEnv();
            $env['LANG'] = 'en'; // Ensures that the default language is en, whatever the OS locale is.
            $this->process->setEnv($env);
        }
        $this->process->run();
    }

    /**
     * @Then the result file will be:
     * @param PyStringNode $string
     * @throws JsonException
     */
    public function theResultFileWillBe(PyStringNode $string): void
    {
        $reportFiles = $this->generatedReportFiles();

        $expected = json_decode($string->getRaw(), true, 512, JSON_THROW_ON_ERROR);
        $actual = json_decode(file_get_contents(sprintf($reportFiles[0])), true, 512, JSON_THROW_ON_ERROR);

        Assert::assertEquals(
            self::removeDynamics($expected),
            self::removeDynamics($actual)
        );
    }

    /**
     * @Then there should be :featureCount features in the report :reportName
     * @Then there should be :featureCount feature in the report :reportName
     * @throws JsonException
     */
    public function thereShouldBeFeaturesInTheReport(int $featureCount, string $reportName): void
    {
        $reportFiles = $this->generatedReportFiles($reportName);

        $reportData = json_decode(file_get_contents(sprintf($reportFiles[0])), true, 512, JSON_THROW_ON_ERROR);
        Assert::assertCount($featureCount, $reportData);
    }

    /**
     * @Then :count result file should be generated
     * @Then :count result files should be generated
     */
    public function resultFileShouldBeGenerated(int $count): void
    {
        $reportFiles = $this->generatedReportFiles();
        Assert::assertCount($count, $reportFiles);
    }

    /**
     * Removes the dynamic parts of a result, like the feature path and durations.
     */
    private static function removeDynamics(array $array): array
    {
        foreach ($array as &$feature) {
            $feature['uri'] = 'features/features.feature';
            foreach ($feature['elements'] as &$scenario) {
                foreach ($scenario['steps'] as &$step) {
                    $step['result']['duration'] = 12345;
                }
            }
        }
        return $array;
    }

    private function generatedReportFiles($reportName = 'report*.json'): array
    {
        return glob(
            sprintf(
                '%1$s%2$sreports%2$s%3$s',
                $this->workingDir,
                DIRECTORY_SEPARATOR,
                $reportName
            )
        );
    }

    private function writeBehatConfigForTests(string $dir, array $extraOptions = []): void
    {
        // create configuration
        $reportsDir = $this->reportsDir;
        $content = <<<EOF
default:
    suites:
        default:
            paths:
                - "$dir/features/default"
            contexts:
                - ExampleFeatureContext
        othersuite:
            paths:
                - "$dir/features/othersuite"
            contexts:
                - ExampleFeatureContext
    extensions:
        Vanare\BehatCucumberJsonFormatter\Extension:
            fileNamePrefix: report-
            outputDir: "$reportsDir"
EOF;
        $content .= implode("", array_map(function ($key, $value) {
            return "\n            $key: $value";
        }, array_keys($extraOptions), $extraOptions));

        file_put_contents($dir . DIRECTORY_SEPARATOR . 'behat.yml', $content);
    }
}
