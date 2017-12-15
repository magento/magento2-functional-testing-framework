Magento Functional Testing Framework Changelog
================================================

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