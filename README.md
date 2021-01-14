![release](https://github.com/cawolf/behat-cucumber-formatter/workflows/release/badge.svg)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/cawolf/behat-cucumber-formatter)
![GitHub](https://img.shields.io/github/license/cawolf/behat-cucumber-formatter)

# Behat Cucumber Json Formatter

*Note*: this is a fork of [Vanare/behat-cucumber-formatter](https://github.com/Vanare/behat-cucumber-formatter). As the original project seems unmaintained and there is no possibility to contact the owner, I publish the library under my handle. Many thanks to the original team of Vanare for starting this great library!

This is Behat extension for generating json reports for [Cucumber Test Result Plugin](https://github.com/jenkinsci/cucumber-testresult-plugin/) which provides graphs over time and drill down to individual results using the standard Jenkins test reporting mechanism.

## Requirements

- PHP 7.3 or higher

- Behat 3.x

## Installation

### Installation via Composer:

```
$ composer require --dev cawolf/behat-cucumber-json-formatter
```

## Usage

Setup extension by specifying your `behat.yml`:

```
default:
    extensions:
        Vanare\BehatCucumberJsonFormatter\Extension:
            fileNamePrefix: report
            resultFilePerSuite: true
            outputDir: %paths.base%/build/tests
```

Then you can run:

```
bin/behat -f cucumber_json
```

### Available options:

- `fileNamePrefix`: Filename prefix of generated report
- `outputDir`: Generated report will be placed in this directory
- `fileName` _(optional)_: Filename of generated report - current feature name will be used by default.
Only applicable when `resultFilePerSuite` is not enabled.
- `resultFilePerSuite` _(optional)_: The default behaviour is to generate a single report named `all.json`.
If this option is set to `true`, a report will be created per behat suite.

## Licence

MIT Licence
