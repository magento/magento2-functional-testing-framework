# Troubleshooting

Having a little trouble with the MFTF? See some common errors and fixes below.

## AcceptanceTester class issues

If you see the following error:

```terminal
AcceptanceTester class doesn't exist in suite folder.
Run the 'build' command to generate it
```

### Reason

Something went wrong during the `mftf build:project` command that prevented the creation of the AcceptanceTester class.

### Solution

This issue is fixed in the MFTF 2.5.0.

In versions of the MFTF lower than 2.5.0 you should:

1. Open the functional.suite.yml file at:

   ```terminal
   <magento root directory>/dev/tests/acceptance/tests/functional.suite.yml
   ```
1. Add quotation marks (`"`) around these values:

    1. `%SELENIUM_HOST%`
    1. `%SELENIUM_PORT%`
    1. `%SELENIUM_PROTOCOL%`
    1. `%SELENIUM_PATH%`
    
1. Run the `vendor/bin/mftf build:project` command again.
1. You should see the AcceptanceTester class is created at:

   ```terminal
   <magento root directory>/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/AcceptanceTester.php
   ```

## WebDriver issues

Troubleshoot your WebDriver issues on various browsers.

### PhantomJS

You are unable to upload file input using the MFTF actions and are seeing the following exception:

```terminal
[Facebook\WebDriver\Exception\NoSuchDriverException]
No active session with ID e56f9260-b366-11e7-966b-db3e6f35d8e1
```

#### Reason

Use of PhantomJS is not supported by the MFTF.

#### Solution

For headless browsing, the [Headless Chrome][]{:target="\_blank"} has better compatibility with the MFTF.

<!-- Link Definitions -->
[Headless Chrome]: https://developers.google.com/web/updates/2017/04/headless-chrome
