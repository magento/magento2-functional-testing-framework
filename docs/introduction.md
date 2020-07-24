# Introduction to the Magento Functional Testing Framework

<div class="bs-callout bs-callout-info" markdown="1">
This documentation is for MFTF 3.0, which was release in conjunction with Magento 2.4.
MFTF 3.0 is a major update and introduces many new changes and fixes.
MFTF 2 docs can be found [here][].
</div>

[Find your version] of MFTF.

The Magento Functional Testing Framework (MFTF) is a framework used to perform automated end-to-end functional testing.

## Goals

-  To facilitate functional testing and minimize the effort it takes to perform regression testing.
-  Enable extension developers to provide functional tests for their extensions.
-  Ensure a common standard of quality between Magento, extension developers and system integrators.

MFTF also focuses on

-  **Traceability** for clear logging and reporting capabilities.
-  **Modularity** to run tests based on installed modules and extensions.
-  **Customizability** for existing tests.
-  **Readability** using clear and declarative XML test steps.
-  **Maintainability** based on simple test creation and overall structure.

## Audience

-  **Contributors**: Tests build confidence about the results of changes introduced to the platform.
-  **Extension Developers**: Can adjust expected behaviour according to their customizations.
-  **System Integrators**: MFTF coverage provided out-of-the-box with Magento is solid base for Acceptance / Regression Tests.

## MFTF tests

MFTF supports two different locations for storing the tests and test artifacts:

-  `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/` is the location of local, customized tests.
-  `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/` is location of tests provided by Magento and vendors.

If you installed Magento with Composer, please refer to `vendor/magento/<module_dir>/Test/Mftf/` for examples.

### Directory Structure

The file structure under both cases is the same:

```tree
Test
└── Mftf
    ├── ActionGroup
    │   └── ...
    ├── Data
    │   └── ...
    ├── Metadata
    │   └── ...
    ├── Page
    │   └── ...
    ├── Section
    │   └── ...
    └── Test
        └── ...
```

## Use cases

-  Contributor: changes the core behaviour, fixing the annoing bug.
   He wants to have automated "supervisor" which is going to verify his work continuously across the stages of bug fixing. Finally, when fix is done - Functional Test is also proof of work done.
-  Extension Developer: offers extension that changes core behaviour.
   He can easily write new tests to make sure that after enabling the feature, Magento behaves properly. Everything with just extending existing tests. As a result he don't need to write coverage from scratch.
-  Integration Agency: maintains Client's e-commerce.
   They are able to customize tests delivered with Magento core to follow customizations implemented to Magento. After each upgrade they can just run the MFTF tests to know that no regression was introduced.

## MFTF output

-  Generated PHP Codeception tests
-  Codeception results and console logs
-  Screenshots and HTML failure report
-  Allure formatted XML results
-  Allure report dashboard of results

## Find your MFTF version

There are two options to find out your MFTF version:

-  using the MFTF CLI
-  using the Composer CLI

All the Command Line commands needs to be executed from `<magento_root>`

### MFTF CLI

```bash
vendor/bin/mftf --version
```

### Composer CLI

```bash
composer show magento/magento2-functional-testing-framework
```

## Contents of dev/tests/acceptance

```tree
tests
      _data                       // Additional files required for tests (e.g. pictures, CSV files for import/export, etc.)
      _output                     // The directory is generated during test run. It contains testing reports.
      _suite                      // Test suites.
      _bootstrap.php              // The script that executes essential initialization routines.
      functional.suite.dist.yml   // The Codeception functional test suite configuration (generated while running 'bin/mftf build:project')
utils                           // The test-running utilities.
.env.example                    // Example file for environmental settings.
.credentials.example            // Example file for credentials to be used by the third party integrations (generated while running 'bin/mftf build:project'; should be filled with the appropriate credentials in the corresponding sandboxes).
.gitignore                      // List of files ignored by git.
.htaccess.sample                // Access settings for the Apache web server to perform the Magento CLI commands.
codeception.dist.yml            // Codeception configuration (generated while running 'bin/mftf build:project')
```

## MFTF output

-  Generated PHP Codeception tests
-  Codeception results and console logs
-  Screenshots and HTML failure report
-  Allure formatted XML results
-  Allure report dashboard of results

## MFTF tests

MFTF supports three different locations for storing the tests and test artifacts:
-  `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/` is the directory to create new tests.
-  `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/` is the directory with the out of the box tests (fetched by the Composer).
-  `<magento_root>/dev/tests/acceptance/tests/functional/<vendor_name>/<module_name>/` is used to store tests that depend on multiple modules.

All tests and test data from these locations are merged in the order indicated in the above list.

Directories immediately following the above paths will use the same format, and sub-directories under each category are supported.

```tree
<Path>
├── ActionGroup
│   └── ...
├── Data
│   └── ...
├── Metadata
│   └── ...
├── Page
│   └── ...
├── Section
│   └── ...
├── Suite
│   └── ...
└── Test
    └── ...
```

## MFTF on Github

Follow the [MFTF project] and [contribute on Github].

<!-- Link definitions -->
[contribute on Github]: https://github.com/magento/magento2-functional-testing-framework/blob/master/.github/CONTRIBUTING.md
[MFTF project]: https://github.com/magento/magento2-functional-testing-framework
[Find your version]: #find-your-mftf-version
[here]: ../v2/docs/introduction.html
