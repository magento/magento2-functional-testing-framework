Magento Functional Testing Framework Changelog
================================================

2.4.5
-----
### Fixes
* Fixed an issue where `.credentials` was required when using `<createData>` actions with field overrides.

2.4.4
-----
### Fixes
* Fixed an issue where `_CREDS` could not be resolved when used in a suite.

2.4.3
-----
* Customizability
    * Use of `_CREDS` has been extended to `<magentoCLI>` and `<createData>` actions
* Traceability
    * A Test step is now generated and injected in the allure report when a test is reported as `BROKEN`. 

### Fixes
* `static-checks` command now properly returns `1` if any static check failed.
* MFTF Console Printer class correctly utilizes `--steps` and other flags passed directly to `codecept commands`.
* `*source` actions correctly print when using `userInput` or `html` attributes.
* XML Comments should no longer throw an error in parsing when used outside `test/actionGroup`

### GitHub Issues/Pull requests:
* [#703](https://github.com/magento/magento2-functional-testing-framework/pull/403) -- SMALL_CHANGE: Minor change suggested

2.4.2
-----
* Traceability
    * Test action `stepKey`s are now included in both console output and Allure report.
    * XML Comments are now automatically converted into a `<comment>` action.

### Fixes
* Moved `epfremme/swagger-php` dependency to `suggests` block due to a conflict with Magento extensions.

2.4.1
-----
* Traceability
    * XSD Schema validation is now enabled by default in `generate:tests`, `run:test`, `run:failed`, `run:group`
    * `--debug` option for the above commands has been updated to include different debug levels
        * See DevDocs for details

### Fixes
* Fixed an issue where `skipReadiness` attribute would cause false XSD Schema validation errors.

2.4.0
-----
### Enhancements
* Maintainability
    * Added new `mftf static-checks` command to run new static checks against the attached test codebase
        * See DevDocs for details
    * Added new `mftf generate:docs` command that generates documentation about attached test codebase
        * See DevDocs for details
* Traceability
    * Allure reports for tests now contain collapsible sections for `actionGroup`s used in execution.

### Fixes
* Fixed an issue where `magentoCli` would treat `argument="0"` as a null value.
* Fixed an issue where `amOnPage` and `waitForPwaElementVisible` would not utilize the `timeout` attribute correctly when MagentoPwaWebDriver is enabled.
* Fixed an issue where invalid XML characters would cause Allure to throw an exception without a resulting report.
* Fixed `codeception.dist.yml` configuration for keeping previous test run results.
* PHP Notices are no longer thrown when XML is missing non-necessary attributes.
* Removed unusable `fillSecretField` action from schema.

### GitHub Issues/Pull requests:
* [#338](https://github.com/magento/magento2-functional-testing-framework/pull/338) -- Return exit codes of process started by 'run:test', 'run:group' or 'run:failed' command
* [#333](https://github.com/magento/magento2-functional-testing-framework/pull/333) -- Added Nginx specific settings to getting started doc
* [#332](https://github.com/magento/magento2-functional-testing-framework/pull/332) -- executeInSelenium action does not generate proper code
* [#318](https://github.com/magento/magento2-functional-testing-framework/pull/318) -- Reduce cyclomatic complexity in Problem Methods
* [#287](https://github.com/magento/magento2-functional-testing-framework/pull/287) -- Update requirements to include php7.3 support

2.3.14
-----
### Enhancements
* Maintainability
    * `command.php` is now configured with an `idleTimeout` of `60` seconds, which will allow tests to continue execution if a CLI command is hanging indefinitely.

2.3.13
-----
### Enhancements
* Traceability
    * Failed test steps are now marked with a red `x` in the generated Allure report.
    * A failed `suite` `<before>` now correctly causes subsequent tests to marked as `failed` instead of `skipped`.
* Customizability
    * Added `waitForPwaElementVisible` and `waitForPwaElementNotVisible` actions.
* Modularity
    * Added support for parsing of symlinked modules under `vendor`.

### Fixes
* Fixed a PHP Fatal error that occurred if the given `MAGENTO_BASE_URL` responded with anything but a `200`.
* Fixed an issue where a test's `<after>` would run twice with Codeception `2.4.x`
* Fixed an issue where tests using `extends` would not correctly override parent test steps
* Test actions can now send an empty string to parameterized selectors.

### GitHub Issues/Pull requests:
* [#297](https://github.com/magento/magento2-functional-testing-framework/pull/297) -- Allow = to be part of the secret value
* [#267](https://github.com/magento/magento2-functional-testing-framework/pull/267) -- Add PHPUnit missing in dependencies
* [#266](https://github.com/magento/magento2-functional-testing-framework/pull/266) -- General refactor: ext-curl dependency + review of singletones (refactor constructs)
* [#264](https://github.com/magento/magento2-functional-testing-framework/pull/264) -- Use custom Backend domain, refactoring to Executors responsible for calling HTTP endpoints
* [#258](https://github.com/magento/magento2-functional-testing-framework/pull/258) -- Removed unused variables in FunctionCommentSniff.php
* [#256](https://github.com/magento/magento2-functional-testing-framework/pull/256) -- Removed unused variables

2.3.12
-----
### Enhancements
* Fetched latest allure-codeception package

2.3.11
-----
### Fixes
* `mftf run:failed` now correctly regenerates tests that are in suites that were parallelized (`suite` => `suite_0`, `suite_1`)

2.3.10
-----
### Enhancements
* Maintainability
    * Added new `mftf run:failed` commands, which reruns all failed tests from last run configuration.
    
### Fixes
* Fixed an issue where mftf would fail to parse test materials for extensions installed under `vendor`.
* Fixed a Windows compatibility issue around the use of Magento's `ComponentRegistrar` to aggregate paths.
* Fixed an issue where an `element` with no `type` would cause PHP warnings during test runs.

2.3.9
-----
### Fixes
* Logic for parallel execution were updated to split default tests and suites from running in one group.

2.3.8
-----
### Fixes
* `ModuleResolver` will now only scan under `MAGENTO_BP/app/code/...` and `MAGENTO_BP/vendor/...` for `/Test/Mftf` directories.
* Fixed an issue where `Test.xml` files that did not end with `*Test.xml` would not be scanned for duplicates and other XML validation.

2.3.7
-----
### Enhancements
* Traceability
    * Test generation errors output xml filename where they were encountered, as well as xml parent nodes where applicable.
    * Duplicate element detection now outputs parent element where duplicate was found.
* Maintainability
    * Standalone MFTF can now be pointed at a Magento installation folder to generate and execute tests.
        * See DevDocs for more information.
    * MFTF now checks for `test` and `actionGroup` elements that have the same `name` in the same file.
* Customizability
    * Updated prefered syntax for `actionGroup` `argument`s that use `xml.data` (old syntax is still supported)
        * Old: `xml.data`
        * New: `{{xml.data}}`
* Modularity
    * `ModuleResolver` now utilizes each Magento module's `registration.php` to map MFTF test material directories.
### Fixes
* The `waitForPageLoad` action now correctly uses the given `timeout` attribute for all of its checks.
* Firefox compatibility issues in javascript error logging were fixed.
* Fixed an issue where arguments containing `-` would not properly resolve parameterized selectors.
* Fixed an issue where actions using `parameterArray` would not resolve `$persisted.data$` references.
* Fixed an issue where composer installations of Magento would fail to parse MFTF materials under a path `vendor/magento/module-<module>/`

2.3.6
-----
### Enhancements
* Maintainability
    * A `-r` or `--remove` flag has been introduced to `bin/mftf` commands to clear out the contents of the `_generated` folder before generation. This flag has been added to the following commands:
        * `generate:tests`
        * `generate:suite`
        * `run:test`
        * `run:group`
* Customizability
    * Persisted data handling mechanisms have been reworked.
        * All persisted data is now referenced with the single `$` syntax (old syntax is still supported):
            * `$persistedData.field$`
        * Persisted data resolution now starts in its own scope and broadens if no matching `stepKey` was found in the current scope.
        * Added support for referencing `suite` persisted data in tests.
        * Added support for removing data created in between test scopes (`test`, `before/after`, `suite`).
    * An attribute `skipReadiness` has been added to all test actions, allowing the individual test action to completely bypass the `ReadinessExtension` if it is enabled.

### Fixes
* To prevent Allure reporting from collating tests with identical `title`, the `testCaseId` annotation is now automatically prepended to the `title` annotation when tests are generated.
* The `magentoCLI` command now correctly removes `index.php` if it is present in the `MAGENTO_BASE_URL`.
* Invalid XML errors now indicate which XML file caused the error.
* Attempting to `extend` a test that does not exist now skips the generation of the test.
* Fixed an issue where a `suite` would generate invalid PHP if the `before` or `after` contained only `createData` actions.
* Fixed an issue where a selector inside an `actionGroup` would incorrectly append the `actionGroup`'s `stepKey` to the selector.

2.3.5
-----
### Fixes
* Removed `PageReadinessExtension` from default enabled extensions due to Jenkins instability.

2.3.4
-----
### Fixes
* MagentoWebDriver overrides `parent::_after()` function and remaps to `runAfter()`, necessary to solve compatibility issues in Codeception `2.3.x`.

2.3.3
-----
### Fixes
* Defaults in `etc/config/functional.suite.dist.yml` changed: window-size to `1280x1024`, and removed `--ingonito` flag.

2.3.2
-----
### Fixes
* The `executeJs` `function` no longer escapes persisted variables referenced via `$$persisted.key$$`.
* Extending a test no longer fails to generate the parent `test`'s `before`/`after` blocks if the parent was skipped.

2.3.1
-----
### Enhancements  
* Maintainability
    * `mftf build:project` now copies over the `command.php` file into the parent Magento installation, if detected.

2.3.0
-----
### Enhancements  
* Traceability
    * MFTF now outputs generation run-time information, warnings, and errors to an `mftf.log` file. 
    * Overall error messages for various generation errors have been improved. Usage of the `--debug` flag provides file-specific errors for all XML-related errors.
    * Allure Reports now require a unique `story` and `title` combination, to prevent collisions in Allure Report generation.
    * The `features` annotation now ignores user input and defaults to the module the test lives under (for clear Allure organization).
    * The `<group value="skip"/>` annotation has been replaced with a `<skip>` annotation, allowing for nested `IssueId` elements.
    * Tests now require the following annotations: `stories`, `title`, `description`, `severity`.
        * This will be enforced in a future major release.
* Modularity
    * MFTF has been decoupled from MagentoCE:
        * MFTF can now generate and run tests by itself via `bin/mftf` commands.
        * It is now a top level MagentoCE dependency, and no longer relies on supporting files in MagentoCE.
        * It can be used as an isolated dependency for Magento projects such as extensions.
    * `generate:tests` now warns the user if any declared `<page>` has an inconsistent `module` (`Backend` vs `Magento_Backend`)
    * The `--force` flag now completely ignores checking of the Magento Installation, allowing generation of tests without a Magento Instance to be running.
* Customizability
    * Various test materials can now be extended via an `extends="ExistingMaterial"` attribute. This allows for creation of simple copies of any `entity`, `actionGroup`, or `test`, with small modifications.
    * `test` and `actionGroup` deltas can now be provided in bulk via a `before/after` attribute on the `test` or `actionGroup` element. Deltas provided this way do not need individual `before/after` attributes, and are inserted sequentially.
    * Secure and sensitive test data can now be stored and used via a new `.credentials` file, with declaration and usage syntax similar to `.env` file references.
    * A new `<generateDate>` action has been added to allow users to create and use dates according to the given `date` and `format`.
        * See DevDocs for more information on all above `Customizability` features.
* Maintainability
    * New `bin/mftf` commands have been introduced with parity to existing `robo` commands.
        * `robo` commands are still supported, but will be deprecated in a future major release.
    * The `mftf upgrade:tests` command has been introduced, which runs all test upgrade scripts against the provided path.
        * A new upgrade script was created to replace all test material schema paths to instead use a URN path.
    * The `mftf generate:urn-catalog` command has been introduced to create a URN catalog in PHPStorm to support the above upgrade.
    * A warning is now shown on generation if a page's url is referenced without specifying the url (`{{page}}` vs `{{page.url}}`).
    * An error is now thrown if any test materials contain any overriding element (eg different `<element>`s in a `<section>` with the same `name`)
        * This previously would cause the last read element to override the previous, causing a silent but potentially incorrect test addition.
    * Test distribution algorithm for `--config parallel` has been enhanced to take average step length into account.

### Fixes
* `_after` hook of tests now executes if a non test-related failure causes the test to error.
* Fixed periods in Allure Report showing up as `•`.
* Fixed Windows incompatibility of relative paths in various files.
* Suites will no longer generate if they do not contain any tests.
* Fixed an issue in generation where users could not use javascript variables in `executeJS` actions.
* Fixed an issue in generation where entity replacement in action-groups replaced all entities with the first reference found.
* Fixed an issue in generation where `createData` actions inside `actionGroups` could not properly reference the given `createDataKey`.
* Fixed an issue where `suites` could not generate if they included an `actionGroup` with two arguments.
* Fixed an issue in generation where calling the same entity twice (with different parameters) would replace both calls with the first resolved value.
* The `magentoCLI` action now correctly executes the given command if the `MAGENTO_BASE_URL` contains `index.php` after the domain (ex `https://magento.instance/index.php`)
* The `stepKey` attribute can no longer be an empty.
* Variable substitution has been enabled for `regex` and `command` attributes in test actions.

### GitHub Issues/Pull requests:
* [#161](https://github.com/magento/magento2-functional-testing-framework/pull/161) -- MAGETWO-46837: Implementing extension to wait for readiness metrics.
* [#72](https://github.com/magento/magento2-functional-testing-framework/issues/72) -- declare(strict_types=1) causes static code check failure (fixed in [#154](https://github.com/magento/magento2-functional-testing-framework/pull/154))

2.2.0
-----
### Enhancements  
* Traceability
    * Javascript errors are now logged and reported in test output.
    * Test failures are no longer overwritten by failures in an `<after>` hook.
    * Tests will no longer execute an `<after>` hook twice if a failure triggered in the `<after>` hook.
    * Tests marked with `<group value="skip">` will now appear in generated Allure reports.
        * Along with the above, the `robo group` command no longer omits the `skip` group (skipped tests are picked up but not fully executed).
* Modularity
    * MFTF no longer relies on relative pathing to determine its path to tests or Magento (favoring composer information if available).
    * Tests and test materials are now read in from Magento modules as well as extensions in addition to `dev/tests/acceptance`.
        * See DevDocs `Getting Started` for details on expected paths and merge order.
* Customizability
    * Creation of Suites is now supported
        * `<suite>` can include tests via `name`, module, or `<group>` tags.
        * Consolidation of preconditions can be achieved via use of `<before/after>` tags in a `<suite>`
            * All normal test actions are supported
            * Data returned from actions is not available for reference in subsequent tests (`createData` or `grab` actions).
        * `robo generate:tests` generates all suites and tests, and can be given a JSON configuration to generate specific test/suites.
        * See MFTF Devdocs "Suite" page for more details.
    * `<deleteData>` may now be called against a `url` instead of a stepKey reference.
    * `<dragAndDrop>` may now be given an additional `x/y` offset.
    * `<executeJS>` now returns a variable based on what the executed script returns.
    * Added `<element>` `type="block"`.
    * `<page>` elements may now be blank (contain no child sections).
* Maintainability
    * `robo generate:tests --config parallel` now accepts a `--lines` argument, for grouping and sorting based on test length.
    * `robo generate:tests` now checks for:
        * Duplicate step keys within an `actionGroup`.
        * Ambiguous or invalid `stepKey` references (in merge files).
    * `robo generate:tests` now suppresses warnings by default. The command now accepts a `--verbose` flag to show full output including warnings.

### Fixes
* Exception message for the `<conditionalClick>` action now correctly references the `selector` given.
* Usage of multiple parameterized elements in a `selector` now correctly resolves all element references.
* Usage of multiple uniqueness references on the same entity now generate correctly.
* Persisted entity references are correctly interpolated with `<page>` url of `type="admin"`.
* Metadata that contains 2 or more params in its `url` now correctly resolve parameters.
* Arguments can now be passed to `x` and `y` attributes in `actionGroup`.
* Arguments can now be passed to nested `<assert*>` action elements.
* The `<seeInField>` action can now be used to assert against empty strings.
* Empty `<data>` elements within an `<entity>` now generate correctly.
* Mapping of the `<magentoCLI>` to the custom command has been fixed.

### GitHub Issues/Pull requests:
* [#89](https://github.com/magento/magento2-functional-testing-framework/pull/89) -- Add ability to use array entities as arguments.
* [#68](https://github.com/magento/magento2-functional-testing-framework/issues/68) -- Excessive double quotes are being generated in WaitForElementChange method arguments (fixed in [#103](https://github.com/magento/magento2-functional-testing-framework/pull/103))
* [#31](https://github.com/magento/magento2-functional-testing-framework/issues/31) -- Can't run tests without a store having "default" store code (fixed in [#86](https://github.com/magento/magento2-functional-testing-framework/pull/86))

2.1.2
-----
### Enhancements
* Added support for PHP version 7.2

2.1.1
-----
### Enhancements
* Modularity
    * MFTF now supports the existence of tests as composer package dependencies or as living alongside core modules. Supported paths:
        * `magento2ce/vendor/[vendor]/[module]/Test/Acceptance`
        * `magento2ce/app/code/[vendor]/[module]/Test/Acceptance`
* Maintainability
    * Robo command `generate:tests` now accepts a `--nodes` argument that specifies the number of test manifest files to generate for parallel configuration.

### Fixes
* Data returned by `grab` and `createData` actions can now be used in `<actionGroup>` test steps by their `stepKey` reference.
* Fixed an issue where `<requiredEntity>` elements inside `<entity>` data xml would overwrite one another when merged.
* Fixed an issue where `<object>` elements inside `<operation>` metadata xml would overwrite one another when merged.
* Nested assertion syntax now correctly allows for values passed in to resolve `{{entity.data}}` references.
* Test action `<selectMultiOption>` now correctly resolves entity references passed in to `filterSelector` and `optionSelector`.
* The robo command `generate:tests --force` no longer requires a `MAGENTO_BASE_URL` to be defined in the `.env` file.
* All `feature` and `story` annotations now live under in the method and not class level in output test php.
    * This is to work around a bug with the Allure-Codeception adapter version `1.2.6`, which was a dependency update in MFTF `2.1.0`.

2.1.0
-----
### Enhancements
* Traceability
    * Severity in `<annotation>` tags now properly reflect Magento severity values. 
* Modularity
    * Added ability to pass in simple values to actionGroups via addition of `type` attribute in actionGroup `<argument>` declaration.
        * For examples, see devdocs article on actionGroups.
    * Merging resolution now depends on Magento instance's installed modules. This also means merging order now follows the expected module merging order.
* Customizability
    * Added `<assertArrayIsSorted>` action. This action takes in an array of data and asserts that the array is properly sorted, according to the provided `sortOrder`
    * Added `<selectMultipleOptions>` action. This is a variation of `<searchAndSelectOptions>` that is given a `filterSelector`, `optionSelector`, and an `<array>` of options to select.
        * For a working sample, see `SearchAndMultiselectActionGroup.xml` under `Catalog` in magento2ce.
    * Test actions that deal with `<url...>` now utilize and grab the page's full url, not just the `/path?query#fragment` portion.
    * All `<assert...>` actions support a clearer, more readable nested syntax.
        * Both old and new syntax are supported. See devdocs `Assertions` article for examples.
    * Added support for overriding a data-entity's `field` values during test runtime, prior to persistence via `<createData>` actions.
    * Added `removeBackend="true"` attribute to `<operation>`. Only applicable to `operation` definitions of `type="adminFormKey"`, attribute prevents pre-pending of `MAGENTO_BACKEND_NAME` to the `url` specified.
        * Specific to use case where `adminFormKey` operations don't follow `MAGENTO_BASE_URL/MAGENTO_BACKEND_NAME/MAGENTO_BACKEND_NAME/API_URL` format.
* Readability
    * Data Entities used in tests (`<test>`, `<page>`, `<section>`, `<element>`, `<data>`) now require alphanumeric naming.
* Maintainability
    * Documentation for all test actions have been added to XML schema. Turning on documentation hinting will display relevant information while writing test XML.
    * All references to `.env` file contents are now resolved at test runtime, as opposed to generation.

### Fixes
* Fixed an issue with using the character `-` in parameterized selector references.
    * Users should now be able to use any characters except for `'` when providing a `'stringLiteral'` to a parameterized selector/url.
* Fixed an issue where entity substitution was not enabled in all `<assert...>` test actions.

### GitHub Issues/Pull requests:
* [#37](https://github.com/magento/magento2-functional-testing-framework/issues/37) -- Unable to make API requests using self signed certificate to HTTPS domain (fixed in [#39](https://github.com/magento/magento2-functional-testing-framework/pull/39))

2.0.3
-----
### Enhancements
* Readability
    * Added the ability to refer to `custom_attribute` data in persisted entities via `key` instead of index.
        * ex. `url_key` in category entity: `$category.custom_attributes[3][value]$` and `$category.custom_attributes[url_key]$` are both valid.
* Maintainability
    * Added check for duplicate `stepKey` attributes at test generation. This check is scoped to `<testAction>` tags within a single `<test>` tag in a single file. 

### Fixes
* Fixed inability to use `<actionGroup>` with `<arguments>` in test hooks.
* Fixed inability to use `0` as data in an entity.
* Fixed an issue where using `<annotation>` tag of `<useCaseId>` would cause test generation failures.
* Fixed an issue where the `<closeAdminNotification>` action could not be used twice in in a `<test>`.
* Fixed an issue where specifying duplicate test actions in test delta's would cause generation errors.
* Fixed an issue where test failure screenshots were being taken at the end of the test hook, as opposed to at the point of failure.
* Operation `metadata` with an `auth` of type `adminFormKey` will now automatically append specified `MAGENTO_BACKEND_NAME` if necessary.

2.0.2
-----

### Enhancements
* Customizability
    * Added the `<magentoCLI>` test action. Action takes the given `command=""` and passes it for execution in Magento Environment.
        * Note: Installation step to enable above action has been added. See `Step 5` in the MFTF `Getting Started` article.
* Maintainability
    * Tests now run actions declared in `<after>` hook in both successful and failed test runs.

### Fixes
* Fixed inability to use `[]` characters within selector/url parameters.
* Fixed a bug where the `<formatMoney>` action did not return a variable for test use.
* Fixed a bug where the `<waitForLoadingMaskToDisappear>` action could not be used twice in an `<actionGroup>`.

2.0.1
-----

### Fixes
 * Fixed an issue with `group` annotation.

2.0.0
-----

### Enhancements
* Modularity
    * Replaced the `<loginAsAdmin>` test action with the action group `LoginAsAdmin`.
    * Added the `.env` file variable `CUSTOM_MODULE_PATHS` which can be used to point to any custom extensions that you may want to write tests against.
    * Added the `<page area="..">` property to distinguish between admin and storefront.
    * Added support for `SectionName.elementName` references in any `function` attributes.
* Customizability
    * Changed page objects where `area="admin"` to prepend the `MAGENTO_BACKEND_NAME` value from the `.env` file.
    * Added support for HTTP requests that do not require authentication.
* Readability
    * Renamed `<config>` XML root nodes to match the content they contain, e.g. `<tests>` or `<pages>`.
    * Renamed all instances of the word *Cest* with *Test*. The *Cest* name will no longer be used in the MFTF project.
* Maintainability
    * Removed the `returnVariable` property from any test actions that return values. Instead, the `stepKey` property will be used as the name of the variable and be referenced as before.

### Fixes
* Fixed the `unselectOption.parameterArray` property to work as expected.
* Fixed a crash if you had a system environment variable set with the same name as any variable in the `.env` file.
* Fixed any actions that refer to *CurrentUrl*, such as `<seeInCurrentUrl>`, to now look at the full webdriver address.
* Fixed the `<waitForPageLoad>` test action to not assume that you always want to dismiss UI notifications.

1.0.0
------

### Core features

* **Traceability** for clear logging and reporting capabilities
* **Modularity** to run tests based on modules/extensions installed
* **Customizability** to have an ability to customize existed tests
* **Readability** using clear declarative XML test steps
* **Maintainability** based on simple test creation and overall structure

### Supported systems

#### Operation systems

* Windows 10
* macOS Sierra

#### Browser

* Chrome (Latest) with ChromeDriver Latest

### Known issues

* Support for Firefox is currently incomplete. This will be resolved to support Firefox 57 (Quantum) and latest Gecko driver in next minor release.
* `MAGENTO_BASE_URL` in _.env_ file must have `/` at the end. Example: http://magento.com/
