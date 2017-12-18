Magento Functional Testing Framework Changelog
================================================

1.1.0
-----

### Added
* Added the `CUSTOM_MODULE_PATHS` env variable. This can be used to target paths, such as extensions, that are outside of the Magento directory for test generation.

### Changed
* The `waitForPageLoad` action will no longer close admin notification modals. These notifications can be closed with the `closeAdminNotification` action.

### Removed
* Removed the `returnVariable` attribute from all actions. Instead, the variable name will be the same as the `stepKey` for the action that it originated from.
* Removed the `variable` attribute from all actions. Variables can now be referenced via the php style syntax `{$stepKeyHere}`

### Fixed
* Fixed a crash that could occur if a system level variable collided names with the .env file.
* Fixed incorrect generation of the `unselectOption` when a `parameterArray` attribute is used.

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
