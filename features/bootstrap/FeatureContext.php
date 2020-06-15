<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var string */
    private $phpBin;

    /** @var \Symfony\Component\Process\Process */
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
    public static function cleanTestFolders()
    {
        if (is_dir($dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat')) {
            self::clearDirectory($dir);
        }
    }

    /**
     * Clears a complete directory by path.
     *
     * @param string $path
     */
    private static function clearDirectory($path)
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
     */
    public function prepareTestFolders()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behat' . DIRECTORY_SEPARATOR .
            md5(microtime() . rand(0, 10000));
        $this->reportsDir = $dir . DIRECTORY_SEPARATOR . 'reports';

        // create directories
        mkdir(
            sprintf(
                '%1$s%2$sfeatures%2$sbootstrap%2$si18n',
                $dir,
                DIRECTORY_SEPARATOR
            ),
            0777,
            true
        );
        mkdir($dir . DIRECTORY_SEPARATOR . 'junit');
        mkdir($this->reportsDir);

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
        $phpFinder = new \Symfony\Component\Process\PhpExecutableFinder();
        if (false === $php = $phpFinder->find()) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }
        $this->workingDir = $dir;
        $this->phpBin = $php;
        $this->process = new Symfony\Component\Process\Process(null);
        $this->process->setTimeout(20);
    }

    /**
     * @Given I have the following feature:
     * @param PyStringNode $string
     */
    public function iHaveTheFollowingFeature(PyStringNode $string)
    {
        $this->iHaveTheFollowingFeatureFileStoredIn('feature.feature', 'default', $string);
    }

    /**
     * @Given I have the following feature file :fileName stored in :subDirectory:
     */
    public function iHaveTheFollowingFeatureFileStoredIn($fileName, $subDirectory = '', PyStringNode $string)
    {
        $filePath = $this->workingDir . '/features' . (!empty($subDirectory) ? '/' . $subDirectory : '') . '/' . $fileName;
        if (!empty($subDirectory) && !file_exists($subDirectory)) {
            mkdir(dirname($filePath), 0777, true);
        }
        file_put_contents($filePath, $string->getRaw());
    }

    /**
     * @Given I have the enabled the "resultFilePerSuite" option
     */
    public function iHaveTheEnabledTheResultFilePerSuiteOption()
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
    public function iRunBehatWithTheConverterAndNoSpecificSuiteIsSpecified()
    {
        $this->runBehatWithConverter();
    }

    /**
     * @When I run behat with the converter
     */
    public function iRunBehatWithTheConverter()
    {
        $this->runBehatWithConverter('-s default');
    }

    protected function runBehatWithConverter($extraParameters = '')
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
     */
    public function theResultFileWillBe(PyStringNode $string)
    {
        $reportFiles = $this->generatedReportFiles();

        $expected = json_decode($string->getRaw(), true);
        $actual = json_decode(file_get_contents(sprintf($reportFiles[0])), true);

        PHPUnit_Framework_Assert::assertEquals(
            self::removeDynamics($expected),
            self::removeDynamics($actual)
        );
    }

    /**
     * @Then there should be :featureCount features in the report :reportName
     * @Then there should be :featureCount feature in the report :reportName
     */
    public function thereShouldBeFeaturesInTheReport(int $featureCount, string $reportName)
    {
        $reportFiles = $this->generatedReportFiles($reportName);

        $reportData = json_decode(file_get_contents(sprintf($reportFiles[0])), true);
        PHPUnit_Framework_Assert::assertCount($featureCount, $reportData);
    }

    /**
     * @Then :count result file should be generated
     * @Then :count result files should be generated
     */
    public function resultFileShouldBeGenerated(int $count)
    {
        $reportFiles = $this->generatedReportFiles();
        PHPUnit_Framework_Assert::assertCount($count, $reportFiles);
    }

    /**
     * Removes the dynamic parts of a result, like the feature path and durations.
     *
     * @param array $array
     * @return array
     */
    private static function removeDynamics(array $array)
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

    private function writeBehatConfigForTests(string $dir, array $extraOptions = [])
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
