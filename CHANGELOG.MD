 * MFTF Version 1.0 - Changelog
 * Initial commit of MFTF v1.0.0
 
 * Core Features:
   * **Traceability** for clear logging and reporting capabilities
   * **Modularity** to run tests based on modules/extensions installed
   * **Customizability** to have an ability to customize existed tests
   * **Readability** using clear declarative XML test steps
   * **Maintainability** based on simple test creation and overall structure
 
 * Supported Systems:
   * OS
     * Windows 10
     * OSX (Sierra)  
   * Browser
     * Chrome (Latest) with ChromeDriver Latest
 * Known Issues:
   * Support for Firefox is curently incomplete. This will be resolved to support Firefox 57 (Quantum) and latest Gecko driver in next minor release.
   * MAGENTO_BASE_URL in .env file must have final "/" e.g. "http://magento.com/"