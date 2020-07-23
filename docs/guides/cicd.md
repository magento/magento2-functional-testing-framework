# How to use MFTF in CICD

To integrate MFTF tests into your CICD pipeline, it is best to start with the conceptual flow of the pipeline code.

## Concept

The overall workflow that tests should follow is:

-  Obtain a Magento instance + install pre-requisites.
-  Generate the tests.
    -  Set options for single or parallel running.
-  Delegate and run tests and gather test-run artifacts.
    -  Re-run options.
-  Generate the Allure reports from the results.

## Obtain a Magento instance

To start, we need a Magento instance to operate against for test generation and execution.

```bash
git clone https://github.com/magento/magento2
```

or

```bash
composer create-project --repository=https://repo.magento.com/ magento/project-community-edition magento2ce
```

For more information on installing magento see [Install Magento using Composer][].

After installing the Magento instance, set a couple of configurations to the Magento instance:

```bash
bin/magento config:set general/locale/timezone America/Los_Angeles
bin/magento config:set admin/security/admin_account_sharing 1
bin/magento config:set admin/security/use_form_key 0
bin/magento config:set cms/wysiwyg/enabled disabled
```

These set the default state of the Magento instance. If you wish to change the default state of the application (and have updated your tests sufficiently to account for it), this is the step to do it.

If your magento instance has Two-Factor Authentication enabled, see [Configure 2FA][] to configure MFTF tests.

## Install Allure

This is required for generating the report after your test runs. See [Allure][] for details.

## Generate tests

### Single execution

Generate tests based on what you want to run:

```bash
vendor/bin/mftf generate:tests
```

This will generate all tests and a single manifest file under `dev/tests/acceptance/tests/functional/Magento/_generated/testManifest.txt`.

### Parallel execution

To generate all tests for use in parallel nodes:

```bash
vendor/bin/mftf generate:tests --config parallel
```

This generates a folder under `dev/tests/acceptance/tests/functional/Magento/_generated/groups`. This folder contains several `group#.txt` files that can be used later with the `mftf run:manifest` command.

## Delegate and run tests

### Single execution

If you are running on a single node, call:

```bash
vendor/bin/mftf run:manifest dev/tests/acceptance/tests/functional/Magento/_generated/testManifest.txt
```

### Parallel execution

You can optimize your pipeline by running tests in parallel across multiple nodes.

Tests can be split up into roughly equal running groups using `--config parallel`.

You do not want to perform installations on each node again and build it. So, to save time, stash pre-made artifacts from earlier steps and un-stash on the nodes.

The groups can be then distributed on each of the nodes and run separately in an isolated environment.

- Stash artifacts from main node and un-stash on current node.
- Run `vendor/bin/mftf run:manifest <current_group.txt>` on current node.
- Gather artifacts from `dev/tests/acceptance/tests/_output` from current node to main node.

### Rerun options

In either single or parallel execution, to re-run failed tests, simply add the `run:failed` command after executing a manifest:

```bash
vendor/bin/mftf run:failed
```

### Generate Allure report

In the main node, generate reports using your `<path_to_results>` into a desired output path:

```bash
allure generate <path_to_results> -c -o <path_to_output>
```

<!-- Link definitions -->
[Install Magento using Composer]: https://devdocs.magento.com/guides/v2.4/install-gde/composer.html
[Configure 2FA]: ../configure-2fa.md
[Allure]: https://docs.qameta.io/allure/
