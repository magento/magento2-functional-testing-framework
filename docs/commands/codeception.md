# CLI commands: vendor/bin/codecept

<div class="bs-callout bs-callout-warning" markdown="1">
We do not recommend using Codeception commands directly as they can break the MFTF basic workflow.
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
The following documentation corresponds to Codeception 2.3.8.
</div>

```bash
Full reference:

Arguments:
   suite                 suite to be tested
   test                  test to be run

Options:
   -o, --override=OVERRIDE Override config values (multiple values allowed)
   --config (-c)         Use custom path for config
   --report              Show output in compact style
   --html                Generate html with results (default: "report.html")
   --xml                 Generate JUnit XML Log (default: "report.xml")
   --tap                 Generate Tap Log (default: "report.tap.log")
   --json                Generate Json Log (default: "report.json")
   --colors              Use colors in output
   --no-colors           Force no colors in output (useful to override config file)
   --silent              Only outputs suite names and final results
   --steps               Show steps in output
   --debug (-d)          Show debug and scenario output
   --coverage            Run with code coverage (default: "coverage.serialized")
   --coverage-html       Generate CodeCoverage HTML report in path (default: "coverage")
   --coverage-xml        Generate CodeCoverage XML report in file (default: "coverage.xml")
   --coverage-text       Generate CodeCoverage text report in file (default: "coverage.txt")
   --coverage-phpunit    Generate CodeCoverage PHPUnit report in file (default: "coverage-phpunit")
   --no-exit             Do not finish with exit code
   --group (-g)          Groups of tests to be executed (multiple values allowed)
   --skip (-s)           Skip selected suites (multiple values allowed)
   --skip-group (-x)     Skip selected groups (multiple values allowed)
   --env                 Run tests in selected environments. (multiple values allowed, environments can be merged with ',')
   --fail-fast (-f)      Stop after first failure
   --help (-h)           Display this help message.
   --quiet (-q)          Do not output any message.
   --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
   --version (-V)        Display this application version.
   --ansi                Force ANSI output.
   --no-ansi             Disable ANSI output.
   --no-interaction (-n) Do not ask any interactive question.
```

<!-- Link definitions -->

[mftf tool]: mftf.md
[annotation]: ../test/annotations.md