Magento Functional Testing Framework Changelog
================================================

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
