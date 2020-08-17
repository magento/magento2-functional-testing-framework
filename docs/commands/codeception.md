# CLI commands: vendor/bin/codecept

<div class="bs-callout bs-callout-warning" markdown="1">
We do not recommend using Codeception commands directly as they can break MFTF basic workflow.
All the Codeception commands you need are wrapped using the [mftf tool][].

To run the Codeception testing framework commands directly, change your directory to the `<Magento root>`.
</div>

## Usage examples

Run all the generated tests:

```bash
vendor/bin/codecept run functional -c dev/tests/acceptance/codeception.yml
```

Run all tests without the `<group value="skip"/>` [annotation][]:

```bash
vendor/bin/codecept run functional --skip-group skip  -c dev/tests/acceptance/codeception.yml
```

Run all tests with the `<group value="example"/>` [annotation][] but with no `<group value="skip"/>`:

```bash
vendor/bin/codecept run functional --group example --skip-group skip -c dev/tests/acceptance/codeception.yml
```

## `codecept run`

`codecept run` runs the test suites:

```bash
vendor/bin/codecept run
```

<div class="bs-callout bs-callout-info">
The following documentation corresponds to Codeception 4.1.4.
</div>

```bash
Full reference:

Arguments:
   suite                 suite to be tested
   test                  test to be run

Options:
    -o, --override=OVERRIDE                    Override config values (multiple values allowed)
    -e, --ext=EXT                              Run with extension enabled (multiple values allowed)
        --report                               Show output in compact style
        --html[=HTML]                          Generate html with results [default: "report.html"]
        --xml[=XML]                            Generate JUnit XML Log [default: "report.xml"]
        --phpunit-xml[=PHPUNIT-XML]            Generate PhpUnit XML Log [default: "phpunit-report.xml"]
        --tap[=TAP]                            Generate Tap Log [default: "report.tap.log"]
        --json[=JSON]                          Generate Json Log [default: "report.json"]
        --colors                               Use colors in output
        --no-colors                            Force no colors in output (useful to override config file)
        --silent                               Only outputs suite names and final results
        --steps                                Show steps in output
    -d, --debug                                Show debug and scenario output
        --bootstrap[=BOOTSTRAP]                Execute custom PHP script before running tests. Path can be absolute or relative to current working directory [default: false]
        --no-redirect                          Do not redirect to Composer-installed version in vendor/codeception
        --coverage[=COVERAGE]                  Run with code coverage
        --coverage-html[=COVERAGE-HTML]        Generate CodeCoverage HTML report in path
        --coverage-xml[=COVERAGE-XML]          Generate CodeCoverage XML report in file
        --coverage-text[=COVERAGE-TEXT]        Generate CodeCoverage text report in file
        --coverage-crap4j[=COVERAGE-CRAP4J]    Generate CodeCoverage report in Crap4J XML format
        --coverage-phpunit[=COVERAGE-PHPUNIT]  Generate CodeCoverage PHPUnit report in path
        --no-exit                              Don't finish with exit code
    -g, --group=GROUP                          Groups of tests to be executed (multiple values allowed)
    -s, --skip=SKIP                            Skip selected suites (multiple values allowed)
    -x, --skip-group=SKIP-GROUP                Skip selected groups (multiple values allowed)
        --env=ENV                              Run tests in selected environments. (multiple values allowed)
    -f, --fail-fast                            Stop after first failure
        --no-rebuild                           Do not rebuild actor classes on start
        --seed=SEED                            Define random seed for shuffle setting
        --no-artifacts                         Don't report about artifacts
    -h, --help                                 Display this help message
    -q, --quiet                                Do not output any message
    -V, --version                              Display this application version
        --ansi                                 Force ANSI output
        --no-ansi                              Disable ANSI output
    -n, --no-interaction                       Do not ask any interactive question
    -c, --config[=CONFIG]                      Use custom path for config
    -v|vv|vvv, --verbose                       Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

<!-- Link definitions -->

[mftf tool]: mftf.md
[annotation]: ../test/annotations.md