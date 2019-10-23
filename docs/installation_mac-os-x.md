# Step by Step MFTF Installation Guide for Mac OS X

## Prerequisite {#prerequisite}

- A user account with `sudo` privileges
- Command line / terminal access
- [Brew][brew] or similar package manager tool is installed
- A Magento 2 web server is [installed][magento_install] and [configured][magento_config] for MFTF testing locally or remotely.  admin url, admin credentials and store front url are available and accessible.

## Prepare environment {#prepare-environment}

MFTF requires the following softwares installed and configured on your development environment:

- [PHP version supported by the Magento instance under test][php]
- [Composer 1.3 or later][composer_download]
- [Docker Engine - Community for Mac][docker]
- [Docker Selenium image version compatible with MFTF 3.8.1 or later][docker selenium]
- [Allure CLI (optional for visual test report)][allure docs]
- VNC Viewer (optional for visually see the browser)
  Built in Screen Sharing App for Mac OS X 10.4 or later, or any other VNC Viewers of your choice, for example, [Vnc Viewer][[vnc viewer]].

### Update local repository

Start by checking and updating the local repository lists before installation:

```bash
brew doctor
```

```bash
brew update && brew upgrade
```

### Install and configure PHP

#### Install PHP

PHP has different versions and releases you can use. Pick the version supported by the Magento application under test.  
We use php 7.2 as an example in this section.

```bash
brew install php@7.2
```

Make sure to add `/usr/local/bin` and `/usr/local/sbin` in $PATH environment variable.

#### Configure PHP for MFTF testing

Make the following configuration in `php.ini`:

```bash
vim /usr/local/etc/php/7.2/php.ini
```

- Set the system time zone for PHP
- Set the PHP memory limit to -1  
(Our recommendations is 4G. -1 is unlimited.)

If you have only one `php.ini` file, make the changes in that file. If you have two php.ini files, make the changes in all files. Failure to do so might cause unpredictable performance.


### Install Composer

#### Download the composer installer

MFTF requires Composer 1.3 or later. Please go to [Composer download page][composer_download] for instructions. For example, the following commands download Composer v1.9.0 and verify the installer SHA-384, which you should cross-check [from][composer-SHA-384].

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```

#### [Install composer globally][composer_install]

To install to /usr/local/bin. enter:

```bash
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

### Install Docker

Download and install docker for Mac OS X from [stable channel][docker] if you don't have Docker previously installed.


## Set up an embedded MFTF {#setup-framework}

This is the default setup of the MFTF that you would need to cover your Magento project with functional tests.
It installs the framework using an existing Composer dependency such as `magento/magento2-functional-testing-framework`.
If you want to set up the MFTF as a standalone tool, refer to [Set up a standalone MFTF][].

### Step 1. Build the project {#build-project}

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

### Step 2. Edit environmental settings {#environment-settings}

In the `dev/tests/acceptance/` directory, edit the `.env` file to match your system.

```bash
vim dev/tests/acceptance/.env
```

Specify the following parameters, which are required to launch tests:

- `MAGENTO_BASE_URL` must contain a domain name of the Magento instance that will be tested.
  Example: `MAGENTO_BASE_URL=http://magento.test`

- `MAGENTO_BACKEND_NAME` must contain the relative path for the Admin area.
  Example: `MAGENTO_BACKEND_NAME=admin`

- `MAGENTO_ADMIN_USERNAME` must contain the username required for authorization in the Admin area.
  Example: `MAGENTO_ADMIN_USERNAME=admin`

- `MAGENTO_ADMIN_PASSWORD` must contain the user password required for authorization in the Admin area.
  Example: `MAGENTO_ADMIN_PASSWORD=123123q`

<div class="bs-callout bs-callout-info" markdown="1">
If the `MAGENTO_BASE_URL` contains a subdirectory like `http://magento.test/magento2ce`, specify `MAGENTO_CLI_COMMAND_PATH`.
</div>

Learn more about environmental settings in [Configuration][].

### Step 3. Generate and run tests {#run-tests}

To run [MFTF][mftf] tests, you will need to setup [Java][java] runtime and [Selenium server][selenium server]. You also need Chrome or Firefox browser unless running in headless mode.
Alternatively, you can use [Docker Selenium][] to simplify the setup.

#### Running Docker container with selenium/standalone images

Here is an example running docker selenium for an image with Chrome or Firefox.
Please either mount -v /dev/shm:/dev/shm or use the flag --shm-size=2g to use the host's shared memory.

```bash
docker run -d -p 4444:4444 -p 5900:5900 --shm-size 2g selenium/standalone-firefox-debug:3.8.1-francium
```

```bash
﻿sudo docker run -d -p 4444:4444 -p 5900:5900 -v /dev/shm:/dev/shm selenium/standalone-chrome-debug:3.8.1-francium
```

#### Generate all tests {#generate-all-tests}

```bash
vendor/bin/mftf generate:tests --remove
```

See more commands in [`mftf`][].

#### Run a simple test {#run-test}

To run a single test `AdminLoginTest` generated by previous step, run:

```bash
vendor/bin/mftf run:test AdminLoginTest -k
```

See more commands in [`mftf`][].

To visually see what the browser is doing you will want to run the debug variant of standalone images and run vncviewer during test execution.
You may need to edit `/etc/hosts` file in the container and add an entry for `magento server` like the following line:

```bash
192.168.65.2    magento.test
```

### Step 4. Generate reports {#reports}

During testing, the MFTF generates test reports in `dev/tests/acceptance/tests/_output/allure-results/`.
You can generate visual representations of the report data using [Allure Framework][].

To view the reports in GUI:

- Install Allure

```bash
brew tap qameta/allure
brew install allure
```

- Run the tool to serve the artifacts in `dev/tests/acceptance/tests/_output/allure-results/`:

```bash
allure serve dev/tests/acceptance/tests/_output/allure-results/
```

Learn more about Allure in the [official documentation][allure docs].

## Set up a standalone MFTF

The MFTF is a root level Magento dependency, but it is also available for use as a standalone application.
You may want to use a standalone application when you develop for or contribute to MFTF, which facilitates debugging and tracking changes.
These guidelines demonstrate how to set up and run Magento acceptance functional tests using standalone MFTF.

### Prerequisites

This installation requires a *local* copy of the same version of Magento code as the Magento server to be tested.
This is because MFTF uses the [tests from Magento modules][mftf tests] as well as the `app/autoload.php` file.

### Step 1. Clone the MFTF repository

If you develop or contribute to the MFTF, it makes sense to clone your fork of the MFTF repository.
For contribution guidelines, refer to the [Contribution Guidelines for the Magento Functional Testing Framework][contributing].


```bash
git clone https://github.com/magento/magento2-functional-testing-framework.git
```

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

### Step 5. Remove the MFTF package dependency in Magento

The MFTF uses the Magento `app/autoload.php` file to read Magento modules.
The MFTF dependency in Magento supersedes the standalone registered namespaces unless it is removed at a Composer level.

```bash
composer remove magento/magento2-functional-testing-framework --dev -d <path to the Magento root directory>
```

### Step 6. Run a simple test

Generate and run a single test that will check your logging to the Magento Admin functionality:

```bash
bin/mftf run:test AdminLoginTest
```

You can find the generated test at `dev/tests/functional/tests/MFTF/_generated/default/`.

### Step 7. Generate Allure reports

The standalone MFTF generates Allure reports at `dev/tests/_output/allure-results/`.
Run the Allure server pointing to this directory:

```bash
allure serve dev/tests/_output/allure-results/
```

<!-- Link definitions -->

[`generate:urn-catalog`]: commands/mftf.html#generateurn-catalog
[`MAGENTO_BP`]: configuration.html#magento_bp
[`mftf`]: commands/mftf.html
[allure docs]: https://docs.qameta.io/allure/
[Allure Framework]: http://allure.qatools.ru/
[basic configuration]: configuration.html#basic-configuration
[composer_download]: https://getcomposer.org/download/
[composer_install]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos
[composer_SHA-384]: https://composer.github.io/pubkeys.html
[Configuration]: configuration.html
[contributing]: https://github.com/magento/magento2-functional-testing-framework/blob/develop/.github/CONTRIBUTING.md
[java]: http://www.oracle.com/technetwork/java/javase/downloads/index.html
[mftf tests]: introduction.html#mftf-tests
[php]: https://devdocs.magento.com/guides/v2.3/install-gde/system-requirements-tech.html#php
[PhpStorm]: https://www.jetbrains.com/phpstorm/
[selenium server]: https://www.seleniumhq.org/download/
[Set up a standalone MFTF]: #set-up-a-standalone-mftf
[Find your MFTF version]: introduction.html#find-your-mftf-version
[docker selenium]: https://github.com/SeleniumHQ/docker-selenium
[docker]: https://download.docker.com/mac/static/stable/x86_64/
[magento_install]: https://devdocs.magento.com/guides/v2.3/install-gde/bk-install-guide.html
[magento_config]: magento_configuration.html
[vnc viewer]: https://www.realvnc.com/en/connect/download/vnc/macos/
[brew]: https://brew.sh/
