# Welcome to the example of Codeception + Robo + Allure!

### Prerequisites
* **Codeception**, **Allure** and **Robo** are PHP based applications installed via **Composer**, so you will need to have **Composer** installed in order to run the following. Please visit the [Composer](https://getcomposer.org/) homepage for installation instructions.

* Some settings need to be adjusted to meet the build environment settings in the appropriate `XXX.suite.yml` file in the `[PROJECT_ROOT]/tests/` directory: `[PROJECT_ROOT]/tests/XXXXXXX.suite.yml`


##### [PROJECT_ROOT]/tests/acceptance.suite.yml
* Edit the following section of code to set the Storefront URL:
    ```
    ...
    url: "http://127.0.0.1:32769"
    ...
    ```

##### [PROJECT_ROOT]/tests/_support/AcceptanceTester.php
* Edit the following section of code at the bottom of the `AcceptanceTester.php` file to set the **Admin Credentials**:

    ```
    ...
    public function loginAsAnExistingAdmin() {
        $I = $this;
        $I->fillField('login[username]', 'admin');
        $I->fillField('login[password]', 'admin123');
        $I->click('Sign in');
        $I->closeAdminNotification();
    }
    ...
    ```

### Running the Tests
* Open a Terminal Window. CD to the Project Directory. Run the following command to install the project dependencies:

    ```
    cd [LOCATION_OF_GITHUB_REPO]
    composer install
    ```

* **You will need to install Allure's CLI tool to generate the reports, please visit this page for instructions**: http://wiki.qatools.ru/display/AL/Allure+Commandline

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


### RoboFile.php

Edit the following command to change the Tests that the command `robo test` executes:

    ...
    $this->_exec('codecept run --env chrome');
    ...



#### TROUBLESHOOTING
* TimeZone Error - http://stackoverflow.com/questions/18768276/codeception-datetime-error
* TimeZone List - http://php.net/manual/en/timezones.america.php
* System PATH - Make sure you have `vendor/bin/` and `vendor/` listed in your system path so you can run the  `codecept` and `robo` commands directly:

    `sudo nano /etc/private/paths`