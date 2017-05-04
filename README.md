# Welcome to the example of Codeception + Robo + Allure!

### Prerequisites
* **Codeception**, **Allure** and **Robo** are PHP based applications installed via **Composer**, so you will need to have **Composer** installed in order to run the following. Please visit the [Composer](https://getcomposer.org/) homepage for installation instructions.
* Some settings need to be adjusted to meet the build environment settings in the appropriate `XXX.suite.yml` file in the `[PROJECT_ROOT]/tests/` directory: `[PROJECT_ROOT]/tests/XXXXXXX.suite.yml`

### Installation
* Open a Terminal Window. CD to the Project Directory. Run the following command to install the project dependencies:
    ```
    cd [LOCATION_OF_GITHUB_REPO]
    composer install
    ```

### Configuration
* In order to adjust the settings for the Framework you will need to copy the `.dist.` settings files and make your adjustments based on your unique Setup. These files are listed in the `.gitignore` file so they will only effect your Setup. You can run the following Robo command to copy the necassary files or follow the [Optional] instructions listed below.
    ```
    robo clone:files
    ```

* Configure the following `.env` variables according to the Magento application being tested.
    ```
    MAGENTO_BASE_URL=http://magento.loc/index.php
    
    MAGENTO_BACKEND_NAME=admin
    MAGENTO_ADMIN_USERNAME=admin
    MAGENTO_ADMIN_PASSWORD=123123q
    
    DB_DSN=''
    DB_USERNAME=''
    DB_PASSWORD=''
    ```

    * **[Optional]** Create .env file by copying existing .env.example file at project root directory.

        ```
        cp .env.example .env
        ```

    * **[Optional]** If you wish to customize entire test suite locally, you can create codeception.yml by copying existing codeception.dist.yml, and make change in codeception.yml.
        ```
        cp codeception.dist.yml codeception.yml
        ```

    * **[Optional]** If you wish to customize acceptance test suite locally, you can create acceptance.suite.yml by copying existing acceptance.suite.dist.yml, and make change in acceptance.suite.yml.
        ```
        cp acceptance.suite.dist.yml acceptance.suite.yml
        ```

### Running the Tests
* Build the project:
    ```
    vendor/bin/codecept build
    ```

* **You will need to install Allure's CLI tool to generate the reports, please visit this page for instructions**: http://wiki.qatools.ru/display/AL/Allure+Commandline.

* Next you will need to start a Selenium server so we can run the tests (This will vary based on your local setup).

* Then open a New Terminal Window.

* Kick off the entire E2E Test Suite run the following command:

    ```
    robo test
    ```

* To kick off some example tests with 2 test cases run the following command:

    ```
    robo example
    ```

### Testing using Robo

* You can run the following test suites using robo:

  * Run the tests marked with **@group chrome**:  `robo chrome`
  * Run the tests marked with **@group firefox**:  `robo chrome`
  * Run the tests marked with **@group phantomjs**:  `robo phantomjs`

### Allure + Robo
* You can generate an Allure report, open an Allure report or both using robo:
  * Generate a report from **[PROJECT_ROOT]/tests/_output/allure-results/**: `robo allure:generate`
  * Open a generate report from **[PROJECT_ROOT]/tests/_output/allure-report**: `robo allure:open`
  * Generate a report and open it: `robo allure:report`

### Testing Environments
* You can run a subset of Tests by editing a command in the file `RoboFile.php` or by running `codecept` directly:

    ```codecept run --env chrome```

    ```codecept run --env firefox```

    ```codecept run --env phantomjs```

    ```codecept run --env chrome --group slow```

### Testing Groups
* You can run or exclude subsets of Tests using the `--group` and `--skip-group` codeception flags in the Terminal (IF you add the `@env` tag to a Test you HAVE to include the `--env ZZZZ` flag in your `codecept` command):
    * ```codecept run acceptance --env ZZZZ --group XXXX --skip-group YYYY```
        * *skip*
        * *slow*
        * *example*
        * *sample*
        * *admin-direct-access*
        * *nav-menu-access*
        * *sampleData*
        * *nav-menu*
        * *add*
        * *fields*
        * *catalog*
        * *configurable*
        * *customer*
        * *sales*
        * *orders*

### RoboFile.php

* Edit the following command to change the Tests that the command `robo test` executes:

    ```
    $this->_exec('codecept run --env chrome');
    ```

### TROUBLESHOOTING
* TimeZone Error - http://stackoverflow.com/questions/18768276/codeception-datetime-error
* TimeZone List - http://php.net/manual/en/timezones.america.php
* System PATH - Make sure you have `vendor/bin/` and `vendor/` listed in your system path so you can run the  `codecept` and `robo` commands directly:

    `sudo nano /etc/private/paths`