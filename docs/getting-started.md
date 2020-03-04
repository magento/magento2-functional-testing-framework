# Getting started

<div class="bs-callout bs-callout-info" markdown="1">
[Find your MFTF version][] of the MFTF.
The latest Magento 2.3.x release supports MFTF 2.5.3.
The latest Magento 2.2.x release supports MFTF 2.4.5.
</div>

## Prepare environment {#prepare-environment}

Make sure that you have the following software installed and configured on your development environment:

-  [PHP version supported by the Magento instance under test][php]
-  [Composer 1.3 or later][composer]
-  [Java 1.8 or later][java]
-  [Selenium Server Standalone 3.1 or later][selenium server] and [ChromeDriver 2.33 or later][chrome driver] or other webdriver in the same directory

<div class="bs-callout bs-callout-tip" markdown="1">
[PhpStorm] supports [Codeception test execution][], which is helpful when debugging.
</div>

## Install Magento {#install-magento}

Use instructions below to install Magento.

### Step 1. Clone the `magento2` source code repository {#clone-magento}

```bash
git clone https://github.com/magento/magento2.git
```

or

```bash
git clone git@github.com:magento/magento2.git
```

### Step 2. Install dependencies {#install-dependencies}

Checkout the Magento version that you are going to test.

```bash
cd magento2/
```

```bash
git checkout 2.3-develop
```

Install the Magento application.

```bash
composer install
```

## Prepare Magento {#prepare-magento}

Configure the following settings in Magento as described below.

### WYSIWYG settings {#wysiwyg-settings}

A Selenium web driver cannot enter data to fields with WYSIWYG.

To disable the WYSIWYG and enable the web driver to process these fields as simple text areas:

1. Log in to the Magento Admin as an administrator.
2. Navigate to **Stores** > Settings > **Configuration** > **General** > **Content Management**.
3. In the WYSIWYG Options section set the **Enable WYSIWYG Editor** option to **Disabled Completely**.
4. Click **Save Config**.

or via command line:

```bash
bin/magento config:set cms/wysiwyg/enabled disabled
```

Clean the cache after changing the configuration values:

```bash
bin/magento cache:clean config full_page
```

<div class="bs-callout bs-callout-tip">
When you want to test the WYSIWYG functionality, re-enable WYSIWYG in your test suite.
</div>

### Security settings {#security-settings}

To enable the **Admin Account Sharing** setting, to avoid unpredictable logout during a testing session, and disable the **Add Secret Key in URLs** setting, to open pages using direct URLs:

1. Navigate to **Stores** > Settings > **Configuration** > **Advanced** > **Admin** > **Security**.
2. Set **Admin Account Sharing** to **Yes**.
3. Set **Add Secret Key to URLs** to **No**.
4. Click **Save Config**.

or via command line:

```bash
bin/magento config:set admin/security/admin_account_sharing 1
```

```bash
bin/magento config:set admin/security/use_form_key 0
```

Clean the cache after changing the configuration values:

```bash
bin/magento cache:clean config full_page
```

### Webserver configuration {#web-server-configuration}

The MFTF does not support executing CLI commands if your web server points to `<MAGE_ROOT_DIR>/pub` directory as recommended in the [Installation Guide][Installation Guide docroot]. For the MFTF to execute the CLI commands, the web server must point to the Magento root directory.

### Nginx settings {#nginx-settings}

If the Nginx Web server is used on your development environment, then **Use Web Server Rewrites** setting in **Stores** > Settings > **Configuration** > **General** > **Web** > **Search Engine Optimization** must be set to **Yes**.

To be able to run Magento command line commands in tests, add the following location block to the Nginx configuration file in the Magento root directory:

```conf
location ~* ^/dev/tests/acceptance/utils($|/) {
  root $MAGE_ROOT;
  location ~ ^/dev/tests/acceptance/utils/command.php {
      fastcgi_pass   fastcgi_backend;
      fastcgi_index  index.php;
      fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
      include        fastcgi_params;
  }
}
```

## Set up an embedded MFTF {#setup-framework}

This is the default setup of the MFTF that you would need to cover your Magento project with functional tests.
It installs the framework using an existing Composer dependency such as `magento/magento2-functional-testing-framework`.
If you want to set up the MFTF as a standalone tool, refer to [Set up a standalone MFTF][].

Install the MFTF.

```bash
composer install
```

### Step 1. Build the project   {#build-project}

In the Magento project root, run:

```bash
vendor/bin/mftf build:project
```

If you use PhpStorm, generate a URN catalog:

```bash
vendor/bin/mftf generate:urn-catalog .idea/
```

If the file does not exist, add the `--force` option to create it:

```bash
vendor/bin/mftf generate:urn-catalog --force .idea/
```

See [`generate:urn-catalog`][] for more details.

<div class="bs-callout bs-callout-tip" markdown="1">
You can simplify command entry by adding the  absolute  path to the `vendor/bin` directory path to your PATH environment variable.
After adding the path, you can run `mftf` without having to include `vendor/bin`.
</div>

### Step 2. Edit environmental settings   {#environment-settings}

In the `magento2/dev/tests/acceptance/` directory, edit the `.env` file to match your system.

```bash
vim dev/tests/acceptance/.env
```

Specify the following parameters, which are required to launch tests:

-  `MAGENTO_BASE_URL` must contain a domain name of the Magento instance that will be tested.
  Example: `MAGENTO_BASE_URL=http://magento.test`

-  `MAGENTO_BACKEND_NAME` must contain the relative path for the Admin area.
  Example: `MAGENTO_BACKEND_NAME=admin`

-  `MAGENTO_ADMIN_USERNAME` must contain the username required for authorization in the Admin area.
  Example: `MAGENTO_ADMIN_USERNAME=admin`

-  `MAGENTO_ADMIN_PASSWORD` must contain the user password required for authorization in the Admin area.
  Example: `MAGENTO_ADMIN_PASSWORD=123123q`

<div class="bs-callout bs-callout-info" markdown="1">
If the `MAGENTO_BASE_URL` contains a subdirectory like `http://magento.test/magento2ce`, specify `MAGENTO_CLI_COMMAND_PATH`.
</div>

Learn more about environmental settings in [Configuration][].

### Step 3. Enable the Magento CLI commands

In the Magento project root, run the following command to enable the MFTF to send Magento CLI commands to your Magento instance.

 ```bash
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess
```

### Step 4. Generate and run tests   {#run-tests}

To run tests, you need a running Selenium server and [`mftf`][] commands.

#### Run the Selenium server {#selenium-server}

Run the Selenium server in terminal.
For example, the following commands run the Selenium server for Google Chrome:

```bash
cd <path_to_directory_with_selenium_server_and_webdriver>/
```

```bash
java -Dwebdriver.chrome.driver=chromedriver -jar selenium-server-standalone-3.14.0.jar
```

#### Generate and run all tests {#run-all-tests}

```bash
vendor/bin/mftf generate:tests
```

```bash
cd dev/tests/acceptance
```

```bash
vendor/bin/codecept run functional -c dev/tests/acceptance/codeception.yml
```

See more commands in [`codecept`][].

#### Run a simple test {#run-test}

To clean up the previously generated tests, and then generate and run a single test `AdminLoginTest`, run:

```bash
vendor/bin/mftf run:test AdminLoginTest --remove
```

See more commands in [`mftf`][].

### Step 5. Generate reports {#reports}

During testing, the MFTF generates test reports in CLI.
You can generate visual representations of the report data using [Allure Framework][].
To view the reports in GUI:

-  [Install Allure][]
-  Run the tool to serve the artifacts in `dev/tests/acceptance/tests/_output/allure-results/`:

```bash
allure serve dev/tests/acceptance/tests/_output/allure-results/
```

Learn more about Allure in the [official documentation][allure docs].

## Set up a standalone MFTF

The MFTF is a root level Magento dependency, but it is also available for use as a standalone application.
You may want to use a standalone application when you develop for or contribute to MFTF, which facilitates debugging and tracking changes.
These guidelines demonstrate how to set up and run Magento acceptance functional tests using standalone MFTF.

### Prerequisites

This installation requires a local instance of the Magento application.
The MFTF uses the [tests from Magento modules][mftf tests] as well as the `app/autoload.php` file.

### Step 1. Clone the MFTF repository

If you develop or contribute to the MFTF, it makes sense to clone your fork of the MFTF repository.
For contribution guidelines, refer to the [Contribution Guidelines for the Magento Functional Testing Framework][contributing].

### Step 2. Install the MFTF

```bash
cd magento2-functional-testing-framework
```

```bash
composer install
```

### Step 3. Build the project

```bash
bin/mftf build:project
```

### Step 4. Edit environment settings

In the `dev/.env` file, define the [basic configuration][] and [`MAGENTO_BP`][] parameters.

### Step 5. Enable the Magento CLI commands {#add-cli-commands}

Copy the `etc/config/command.php` file into your Magento installation at `<magento root directory>/dev/tests/acceptance/utils/`.
Create the `utils/` directory, if you didn't find it.

### Step 6. Remove the MFTF package dependency in Magento

The MFTF uses the Magento `app/autoload.php` file to read Magento modules.
The MFTF dependency in Magento supersedes the standalone registered namespaces unless it is removed at a Composer level.

```bash
composer remove magento/magento2-functional-testing-framework --dev -d <path to the Magento root directory>
```

### Step 7. Run a simple test

Generate and run a single test that will check your logging to the Magento Admin functionality:

```bash
bin/mftf run:test AdminLoginTest
```

You can find the generated test at `dev/tests/functional/tests/MFTF/_generated/default/`.

### Step 8. Generate Allure reports

The standalone MFTF generates Allure reports at `dev/tests/_output/allure-results/`.
Run the Allure server pointing to this directory:

```bash
allure serve dev/tests/_output/allure-results/
```

<!-- Link definitions -->

[`codecept`]: commands/codeception.html
[`generate:urn-catalog`]: commands/mftf.html#generateurn-catalog
[`MAGENTO_BP`]: configuration.html#magento_bp
[`mftf`]: commands/mftf.html
[allure docs]: https://docs.qameta.io/allure/
[Allure Framework]: http://allure.qatools.ru/
[basic configuration]: configuration.html#basic-configuration
[chrome driver]: https://sites.google.com/a/chromium.org/chromedriver/downloads
[Codeception Test execution]: https://blog.jetbrains.com/phpstorm/2017/03/codeception-support-comes-to-phpstorm-2017-1/
[composer]: https://getcomposer.org/download/
[Configuration]: configuration.html
[contributing]: https://github.com/magento/magento2-functional-testing-framework/blob/develop/.github/CONTRIBUTING.md
[install Allure]: https://github.com/allure-framework/allure2#download
[java]: http://www.oracle.com/technetwork/java/javase/downloads/index.html
[mftf tests]: introduction.html#mftf-tests
[php]: https://devdocs.magento.com/guides/v2.3/install-gde/system-requirements-tech.html#php
[PhpStorm]: https://www.jetbrains.com/phpstorm/
[selenium server]: https://www.seleniumhq.org/download/
[Set up a standalone MFTF]: #set-up-a-standalone-mftf
[test suite]: suite.html
[Find your MFTF version]: introduction.html#find-your-mftf-version
[Installation Guide docroot]: https://devdocs.magento.com/guides/v2.3/install-gde/tutorials/change-docroot-to-pub.html
