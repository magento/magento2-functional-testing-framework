# Configuration

The `*.env` file provides additional configuration for the Magento Functional Testing Framework (MFTF).
To run MFTF on your Magento instance, specify the basic configuration values.
Advanced users can create custom configurations based on requirements and environment.

## Basic configuration

These basic configuration values are __required__ and must be set by the user before MFTF can function correctly.

### MAGENTO_BASE_URL

The root URL of the Magento application under test.

Example:

```conf
MAGENTO_BASE_URL=http://magento2.vagrant251
```

<div class="bs-callout bs-callout-info" markdown="1">
If the `MAGENTO_BASE_URL` contains a subdirectory (like `http://magento.test/magento2ce`), specify [`MAGENTO_CLI_COMMAND_PATH`][].
</div>

### MAGENTO_BACKEND_NAME

The path to the Magento Admin page.

Example:

```conf
MAGENTO_BACKEND_NAME=admin_12346
```

### MAGENTO_BACKEND_BASE_URL

(Optional) If you are running the Admin Panel on a separate domain, specify this value:

Example:

```conf
MAGENTO_BACKEND_BASE_URL=https://admin.magento2.test
```

### MAGENTO_ADMIN_USERNAME

The username that tests can use to access the Magento Admin page

Example:

```conf
MAGENTO_ADMIN_USERNAME=admin
```

### MAGENTO_ADMIN_PASSWORD

The password that tests will use to log in to the Magento Admin page.

Example:

```conf
MAGENTO_ADMIN_PASSWORD=1234reTyt%$7
```

## Advanced configuration

Depending on the environment you use, you may need to configure MFTF more precisely by setting additional configuration parameters.
This section describes available configuration parameters and their default values (where applicable).

### DEFAULT_TIMEZONE

Sets a default value for the `timezone` attribute of a [`generateDate` action][generateDate].
This value is applied when a test step does not specify a time zone.
For the complete list of available time zones, refer to [List of Supported Timezones][timezones].

Default: `America/Los_Angeles`.

Example:

```conf
DEFAULT_TIMEZONE=UTC
```

### SELENIUM

The `SELENIUM_*` values form the URL of a custom Selenium server for running testing.

Default Selenium URL: `http://127.0.0.1:4444/wd/hub`

And the default configuration:

```conf
SELENIUM_HOST=127.0.0.1
SELENIUM_PORT=4444
SELENIUM_PROTOCOL=http
SELENIUM_PATH=/wd/hub
```

<div class="bs-callout bs-callout-warning" markdown="1">
`SELENIUM_*` values are required if you are running Selenium on an external system.
If you change the configuration of the external Selenium server, you must update these values.
</div>

#### SELENIUM_HOST

Override the default Selenium server host.

Example:

```conf
SELENIUM_HOST=user:pass@ondemand.saucelabs.com
```

#### SELENIUM_PORT

Override the default Selenium server port.

Example:

```conf
SELENIUM_PORT=443
```

#### SELENIUM_PROTOCOL

Override the default Selenium server protocol.

Example:

```conf
SELENIUM_PROTOCOL=https
```

#### SELENIUM_PATH

Override the default Selenium server path.

Example:

```conf
SELENIUM_PATH=/wd/hub
```

### MAGENTO_RESTAPI

These `MAGENTO_RESTAPI_*` values are optional and can be used in cases when your Magento instance has a different API path than the one in `MAGENTO_BASE_URL`.

```conf
MAGENTO_RESTAPI_SERVER_HOST
MAGENTO_RESTAPI_SERVER_PORT
```

#### MAGENTO_RESTAPI_SERVER_HOST

The protocol and the host of the REST API server path.

Example:

```conf
MAGENTO_RESTAPI_SERVER_HOST=http://localhost
```

#### MAGENTO_RESTAPI_SERVER_PORT

The port part of the API path.

Example:

```conf
MAGENTO_RESTAPI_SERVER_PORT=5000
```

### \*_BP

Settings to override base paths for the framework.
You can use it when MFTF is applied as a separate tool.
For example, when you need to place MFTF and the Magento codebase in separate projects.

```conf
MAGENTO_BP
TESTS_BP
FW_BP
TESTS_MODULES_PATH
```

#### MAGENTO_BP

The path to a local Magento codebase.
It enables the [`bin/mftf`][mftf] commands such as `run` and `generate` to parse all modules of the Magento codebase for MFTF tests.

```conf
MAGENTO_BP=~/magento2/
```

#### TESTS_BP

BP is an acronym for _Base Path_.
The path to where MFTF supplementary files are located in the Magento codebase.

Example:

```conf
TESTS_BP=~/magento2ce/dev/tests/acceptance
```

#### FW_BP

The path to MFTF.
FW_BP is an acronym for _FrameWork Base Path_.

Example:

```conf
FW_BP=~/magento/magento2-functional-testing-framework
```

### TESTS_MODULE_PATH

The path to where the MFTF modules mirror Magento modules.

Example:

```conf
TESTS_MODULE_PATH=~/magento2/dev/tests/acceptance/tests/functional/Magento
```

### MODULE_ALLOWLIST

Use for a new module.
When adding a new directory at `tests/functional/Magento`, add the directory name to `MODULE_ALLOWLIST` to enable MFTF to process it.

Example:

```conf
MODULE_ALLOWLIST=Magento_Framework,Magento_ConfigurableProductWishlist,Magento_ConfigurableProductCatalogSearch
```

### MAGENTO_CLI_COMMAND_PATH

Path to the Magento CLI command entry point.

Default: `dev/tests/acceptance/utils/command.php`.
It points to `MAGENTO_BASE_URL` + `dev/tests/acceptance/utils/command.php`

Modify the default value:

-  for non-default Magento installation
-  when using a subdirectory in the `MAGENTO_BASE_URL`

Example: `dev/tests/acceptance/utils/command.php`

### BROWSER

Override the default browser performing the tests.

Default: Chrome

Example:

```conf
BROWSER=firefox
```

### CREDENTIAL_VAULT_ADDRESS

The Api address for a vault server.

Default: http://127.0.0.1:8200

Example:

```conf
# Default api address for local vault dev server
CREDENTIAL_VAULT_ADDRESS=http://127.0.0.1:8200
```

### CREDENTIAL_VAULT_SECRET_BASE_PATH

Vault secret engine base path.

Default: secret

Example:

```conf
# Default base path for kv secret engine in local vault dev server
CREDENTIAL_VAULT_SECRET_BASE_PATH=secret
```

### CREDENTIAL_AWS_SECRETS_MANAGER_REGION

The region that AWS Secrets Manager is located.

Example:

```conf
# Region of AWS Secrets Manager
CREDENTIAL_AWS_SECRETS_MANAGER_REGION=us-east-1
```

### CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE

The profile used to connect to AWS Secrets Manager.

Example:

```conf
# Profile used to connect to AWS Secrets Manager.
CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE=default
```

### VERBOSE_ARTIFACTS

Determines if passed tests should still have all their Allure artifacts. These artifacts include `.txt` attachments for `dontSee` actions and `createData` actions.

If enabled, all tests will have all of their normal Allure artifacts.

If disabled, passed tests will have their Allure artifacts trimmed. Failed tests will still contain all their artifacts.

This is set `false` by default.

```conf
VERBOSE_ARTIFACTS=true
```

### ENABLE_BROWSER_LOG

Enables addition of browser logs to Allure steps

```conf
ENABLE_BROWSER_LOG=true
```

### SELENIUM_CLOSE_ALL_SESSIONS

Forces MFTF to close all Selenium sessions after running a suite.

Use this if you're having issues with sessions hanging in an MFTF suite.

```conf
SELENIUM_CLOSE_ALL_SESSIONS=true
```

### BROWSER_LOG_BLOCKLIST

Blocklists types of browser log entries from appearing in Allure steps.

Denoted in browser log entry as `"SOURCE": "type"`.

```conf
BROWSER_LOG_BLOCKLIST=other,console-api
```

### WAIT_TIMEOUT

Global MFTF configuration for the default amount of time (in seconds) that a test will wait while loading a page.

```conf
WAIT_TIMEOUT=30
```

### ENABLE_PAUSE

Enables the ability to pause test execution at any point, and enter an interactive shell where you can try commands in action.
When pause is enabled, MFTF will generate pause() command in _failed() hook so that test will pause execution when failed. 

```conf
ENABLE_PAUSE=true
```

### REMOTE_STORAGE_AWSS3_DRIVER

The remote storage driver. To enable AWS S3, use `aws-s3`.

Example:

```conf
REMOTE_STORAGE_AWSS3_DRIVER=aws-s3
```

### REMOTE_STORAGE_AWSS3_REGION

The region of S3 bucket.

Example:

```conf
REMOTE_STORAGE_AWSS3_REGION=us-west-2
```

### REMOTE_STORAGE_AWSS3_BUCKET

The name of S3 bucket.

Example:

```conf
REMOTE_STORAGE_AWSS3_BUCKET=my-test-bucket
```

### REMOTE_STORAGE_AWSS3_PREFIX

The optional prefix inside S3 bucket.

Example:

```conf
REMOTE_STORAGE_AWSS3_PREFIX=local
```

### MAGENTO_ADMIN_WEBAPI_TOKEN_LIFETIME

The lifetime (in seconds) of Magento Admin WebAPI token; if token is older than this value a refresh attempt will be made just before the next WebAPI call.

Example:

```conf
MAGENTO_ADMIN_WEBAPI_TOKEN_LIFETIME=10800
```

<!-- Link definitions -->

[`MAGENTO_CLI_COMMAND_PATH`]: #magento_cli_command_path
[generateDate]: test/actions.md#generatedate
[mftf]: commands/mftf.md
[timezones]: http://php.net/manual/en/timezones.php
