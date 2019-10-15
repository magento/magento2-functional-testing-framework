# Step by Step MFTF Installation Guide for Ubuntu

## Prerequisite  {#prerequisite}

- A user account with `sudo` privileges
- Command line / terminal access

## Prepare environment  {#prepare-environment}

MFTF requires the following softwares installed and configured on your development environment:

- [PHP version supported by the Magento instance under test][php]
- [Composer 1.3 or later][composer]
- [Docker Engine - Community for Ubuntu][docker]
- [Docker Selenium image version compatible with MFTF 3.8.1 or later][docker selenium]
- [VNC Viewer (optional for visually see the browser)][vnc viewer]
- [Allure CLI (optional for visual test report)][allure]

### Update local repository

Start by updating the local repository lists before installation:

```bash
sudo apt-get update && sudo apt-get upgrade
```

### Install and configure PHP

#### Add the PHP repository

This step is only needed if your system has no PHP previously installed.

```bash
sudo apt-get install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
```

#### Install PHP

PHP has different versions and releases you can use. Pick the version supported by the Magento application under test.  
We use php 7.2 as an example in this section.

```bash
sudo apt-get install php7.2
```

#### Install additional PHP packages required by MFTF

```bash
sudo apt-get install php7.2-mbstring php7.2-curl php7.2-bcmath php7.2-zip php7.2-dom php7.2-gd php7.2-intl php7.2-soap php7.2-mysql
```

#### Configure PHP for MFTF testing

Make the following configuration in `php.ini`:

```bash
sudo vim /etc/php/7.2/cli/php.ini
```

- Set the system time zone for PHP
- Set the PHP memory limit to -1  
(Our recommendations is 4G. -1 is unlimited.)

If you have only one `php.ini` file, make the changes in that file. If you have two php.ini files, make the changes in all files. Failure to do so might cause unpredictable performance.


### Install Composer

MFTF requires Composer 1.3 or later.

#### Download the composer installer

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
```

#### Install composer required packages

```bash
sudo apt-get install curl php-cli php-mbstring git unzip
```

#### Install composer globally

To install to /usr/local/bin. enter:

```bash
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

### Install Docker

`Install Docker` step is only needed if your system has no Docker previously installed or you want to reinstall the latest version.

#### Uninstall old versions

```bash
sudo apt-get remove docker docker-engine docker.io containerd runc
```

#### Download dependencies

```bash
sudo apt-get install apt-transport-https ca-certificates curl gnupg-agent software-properties-common
```

#### Add Docker's official GPG key

```bash
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
```

#### Verify key

Verify that you now have the key with the fingerprint 9DC8 5822 9FC7 DD38 854A E2D8 8D81 803C 0EBF CD88, by searching for the last 8 characters of the fingerprint.

```bash
sudo apt-key fingerprint 0EBFCD88
```

#### Add Docker `stable` repository

```bash
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" 
sudo apt-get update
```

#### Install latest version of Docker Engine - Community and containerd

```bash
sudo apt-get install docker-ce docker-ce-cli containerd.io
```

## Install Magento {#install-magento}

Follow Magento Installation Guide to install Magento either by [Git][magento_install_git] clone or by [Composer][magento_install_composer].

## Prepare Magento  {#prepare-magento}

Configure the following settings in Magento as described below.

### WYSIWYG settings    {#wysiwyg-settings}

A Selenium web driver cannot enter data to fields with WYSIWYG.

To disable the WYSIWYG and enable the web driver to process these fields as simple text areas:

1. Log in to the Magento Admin as an administrator.
2. Navigate to **Stores** > Settings > **Configuration** > **General** > **Content Management**.
3. In the WYSIWYG Options section set the **Enable WYSIWYG Editor** option to **Disabled Completely**.
4. Click **Save Config**.

<div class="bs-callout bs-callout-tip">
When you want to test the WYSIWYG functionality, re-enable WYSIWYG in your test suite.
</div>

### Security settings   {#security-settings}

To enable the **Admin Account Sharing** setting, to avoid unpredictable logout during a testing session, and disable the **Add Secret Key in URLs** setting, to open pages using direct URLs:

1. Navigate to **Stores** > Settings > **Configuration** > **Advanced** > **Admin** > **Security**.
2. Set **Admin Account Sharing** to **Yes**.
3. Set **Add Secret Key to URLs** to **No**.
4. Click **Save Config**.

### Nginx settings {#nginx-settings}

If Nginx Web server is used on your development environment then **Use Web Server Rewrites** setting in **Stores** > Settings > **Configuration** > **Web** > **Search Engine Optimization** must be set to **Yes**.

To be able to run Magento command line commands in tests add the following location block to Nginx configuration file:

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

### Step 2. Edit environmental settings   {#environment-settings}

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

### Step 3. Enable the Magento CLI commands

In the `dev/tests/acceptance` directory, run the following command to enable the MFTF to send Magento CLI commands to your Magento instance.

 ```bash
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess
```

### Step 4. Generate and run tests   {#run-tests}

To run [MFTF][mftf] tests, you will need to setup [Java][java] runtime and [Selenium server][selenium server]. You also need Chrome or Firefox browser unless running in headless mode.
Alternatively, you can use [Docker Selenium][] to simplify the setup.

#### Running Docker container with selenium/standalone images

Here is an example running docker selenium for an image with Chrome or Firefox.
Please either mount -v /dev/shm:/dev/shm or use the flag --shm-size=2g to use the host's shared memory.

```bash
 sudo docker run -d -p 4444:4444 -p 5900:5900 --shm-size 2g selenium/standalone-firefox-debug:3.8.1-francium
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

### Step 5. Generate reports {#reports}

During testing, the MFTF generates test reports in `dev/tests/acceptance/tests/_output/allure-results/`.
You can generate visual representations of the report data using [Allure Framework][].

To view the reports in GUI:

- Install Allure

```bash
curl -o allure-2.7.0.tgz -Ls https://dl.bintray.com/qameta/generic/io/qameta/allure/allure/2.7.0/allure-2.7.0.tgz   
sudo tar -zxvf allure-2.7.0.tgz -C /opt/   
sudo ln -s /opt/allure-2.7.0/bin/allure /usr/bin/allure  
allure --version 
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

This installation requires a local instance of the Magento application.
The MFTF uses the [tests from Magento modules][mftf tests] as well as the `app/autoload.php` file.

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

[`generate:urn-catalog`]: commands/mftf.html#generateurn-catalog
[`MAGENTO_BP`]: configuration.html#magento_bp
[`mftf`]: commands/mftf.html
[allure docs]: https://docs.qameta.io/allure/
[Allure Framework]: http://allure.qatools.ru/
[basic configuration]: configuration.html#basic-configuration
[composer]: https://getcomposer.org/download/
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
[docker]: https://docs.docker.com/install/linux/docker-ce/ubuntu/
[magento_install_composer]: https://devdocs.magento.com/guides/v2.3/install-gde/composer.html
[magento_install_git]: https://devdocs.magento.com/guides/v2.3/install-gde/prereq/dev_install.html
[vnc viewer]: https://www.realvnc.com/en/connect/download/viewer/linux/
[allure]: https://dl.bintray.com/qameta/generic/io/qameta/allure/allure/
