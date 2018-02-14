Magento Functional Testing Framework Changelog
================================================

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
