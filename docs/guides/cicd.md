# How to use MFTF in CICD

If you want to integrate MFTF tests into your CICD pipeline, then it's best to start with the conceptual flow of the pipeline code.

## Concept

The overall workflow that tests should follow is thus:

* Obtain Magento instance + install pre-requisites
* Generate tests for running
    * Options for single/parallel running
* Delegate and run tests + gather test run artifacts
    * Re-run options
* Generate Allure report from aggregate

### Obtain Magento instance

To start, we need a Magento instance to operate against for generation and execution.

```
$ git clone https://github.com/magento/magento2
or
$ composer create-project --repository=https://repo.magento.com/ magento/project-community-edition magento2ce
```

For more information on installing magento see [Install Magento using Composer].

After installing the Magento instance, you need to set a couple of configurations to the magento instance:

```
$ bin/magento config:set general/locale/timezone America/Los_Angeles
$ bin/magento config:set admin/security/admin_account_sharing 1
$ bin/magento config:set admin/security/use_form_key 0
$ bin/magento config:set cms/wysiwyg/enabled disabled
```

These help set the state for the `default` state of the Magento instance. If you are wanting to change the default state of the application (and have merged into the tests sufficiently to account for it), this is the step in which you would do it.

#### Install allure

This will be required to generate the report after your test runs. See [Allure] for details.


### Generate tests

#### Single execution

Simply generate tests based on what you want to run:

```
$ vendor/bin/mftf generate:tests
```

This will generate all tests, and a single manifest file under `dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/testManifest.txt`

#### Parallel execution

To generate all tests for use in parallel nodes:

```
$ vendor/bin/mftf generate:tests --config parallel
```

This will generate a folder under `dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/groups`. This folder contains several `group#.txt` files that can be used later with the `mftf run:manifest` command.

### Delegate and run tests

#### Single execution
If you are running on a single node, this step is simply to call:

```
$ vendor/bin/mftf run:manifest dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/testManifest.txt
```

#### Parallel execution
To run MFTF tests in parallel, you will need to clone the contents of the current node and duplicate them depending on how many nodes you have available for use.

* Clone contents of current node as a baseline
* For each `groups/group#.txt` file:
    * Set a node's contents to the baseline
    * Run `vendor/bin/mftf run:manifest <current_group.txt>`
    * Gather artifacts from `dev/tests/acceptance/tests/_output` from current node to master

#### Rerun options
In either single or parallel execution, to re-run failed tests simply add a `run:failed` command after executing a manifest:

```
$ vendor/bin/mftf run:failed
```

### Generate Allure report

In the master node, simply generate using your `<path_to_results>` into a desired output path

```
$ allure generate <path_to_results> -c -o <path_to_output>
```

<!-- Link definitions -->
[Install Magento using Composer]: https://devdocs.magento.com/guides/v2.3/install-gde/composer.html
[Allure]: https://docs.qameta.io/allure/
