# Using suites

With an increasing number of MFTF tests, it is important to have a mechanism to organize and consolidate them for ease-of-use.

### What is a suite?

A suite is a collection of MFTF tests that are intended to test specific behaviors of Magento. It may contain initialization and clean up steps common to the included test cases. It allows you to include, exclude and/or group tests with preconditions and post conditions.
You can create a suite referencing tests, test groups and modules.

### How is a suite defined?

A suite should be created under `<magento2 root>/dev/tests/acceptance/tests/_suite` if it has cross-module references. If a suite references only a single module, it should be created under `<module>/Test/Mftf/Suite`. The generated tests for each suite are grouped into their own directory under `<magento2 root>/dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/`.

### What is the format of a suite?

A suite is comprised of blocks:

*  `<before>` : executes precondition once per suite run.
*  `<after>`  : executes postcondition once per suite run.
*  `<include>`: includes specific tests/groups/modules in the suite.
*  `<exclude>`: excludes specific tests/groups/modules from the suite.

```xml
<?xml version="1.0" encoding="UTF-8"?>

<suites xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:mftf:Suite/etc/suiteSchema.xsd">
    <suite name="">
        <before>
        </before>
        <after>
        </after>
        <include>
            <test name=""/>
            <group name=""/>
            <module name="" file=""/>
        </include>
        <exclude>
            <test name=""/>
            <group name=""/>
            <module name="" file=""/>
        </exclude>
    </suite>
</suites>
```

### Example

```xml
<suites xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:mftf:Suite/etc/suiteSchema.xsd">
    <suite name="WYSIWYGDisabledSuite">
        <before>
            <magentoCLI stepKey="disableWYSIWYG" command="config:set cms/wysiwyg/enabled disabled" />
        </before>
        <after>
            <magentoCLI stepKey="enableWYSIWYG" command="config:set cms/wysiwyg/enabled enabled" />
        </after>
        <include>
            <module name="Catalog"/>
        </include>
        <exclude>
            <test name="WYSIWYGIncompatibleTest"/>
        </exclude>
    </suite>
</suites>
```

This example declares a suite with name `WYSIWYGDisabledSuite`:

*  Disables WYSIWYG of the Magento instance before running the tests.
*  Runs all tests from the `Catalog` module, except `WYSIWYGIncompatibleTest`
*  Returns the Magento instance back to its original state, by enabling WYSIWYG at the end of testing.

### Using MFTF suite commands

*  Generate all tests within a suite.

    ```bash
    vendor/bin/mftf generate:suite <suiteName> [<suiteName>]
    ```
*  Run all tests within suite.

    ```bash
    vendor/bin/mftf run:group <suiteName> [<suiteName>]
    ```
*  Generates any combination of suites and tests.

    ```bash
    vendor/bin/mftf generate:tests --tests '{"tests":["testName1","testName2"],"suites":{"suite1":["suite_test1"],"suite2":null}}'
    ```
 
### Run specific tests within a suite

If a test is referenced in a suite, it can be run in the suite's context with MFTF `run` command. If a test is referenced in multiple suites, the `run` command will run the test multiple times in all contexts.

```bash
vendor/bin/mftf run:test <testName> [<testName>]
```  

### When to use suites?

Suites are a great way to organize tests which need the Magento environment to be configured in a specific way as a pre-requisite. The conditions are executed once per suite which optimizes test execution time. If you wish to categorize tests solely based on functionality, use group tags instead.
