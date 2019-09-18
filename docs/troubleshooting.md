# Troubleshooting

Having a little trouble with the MFTF? See some common errors and fixes below.

## AcceptanceTester class issues

If you see the following error:

```terminal
AcceptanceTester class doesn't exist in suite folder.
Run the 'build' command to generate it
```

#### Reason

Something went wrong during the `mftf build:project` command that prevented the creation of the AcceptanceTester class.

#### Solution

This issue is fixed in MFTF 2.5.0.

In versions of MFTF lower than 2.5.0 you should:

1. Open the functional.suite.yml file at:
    ```terminal
    <magento root directory>/dev/tests/acceptance/tests/functional.suite.yml
    ```
2. Add quotation marks (`"`) around these values:
    1. `%SELENIUM_HOST%`
    2. `%SELENIUM_PORT%`
    3. `%SELENIUM_PROTOCOL%`
    4. `%SELENIUM_PATH%`
3. Run the `vendor/bin/mftf build:project` command again.
4. You should see the AcceptanceTester class is created at:
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

Use of PhantomJS is not actually supported by the MFTF.

#### Solution

For headless browsing, the [Headless Chrome][]{:target="\_blank"} has better compatibility with the MFTF.

### Chrome

You are seeing an "unhandled inspector error" exception:

```terminal
[Facebook\WebDriver\Exception\UnknownServerException]
unknown error: undhandled inspector error: {"code":-32601, "message":
"'Network.deleteCookie' wasn't found"} ....
```

![Screenshot with the exception](./img/trouble-chrome232.png)

#### Reason

Chrome v62 is in the process of being rolled out, and it causes an error with ChromeDriver v2.32+.

#### Solution

Use [ChromeDriver 74.0.3729.6+][]{:target="\_blank"} and [Selenium Server Standalone v3.9+][]{:target="\_blank"} in order to execute tests in Google Chrome v62+.

### Firefox

Tests that use the `moveMouseOver` action cause an error when run locally.

#### Reason

There's a compatibility issue with Codeception's `moveMouseOver` function and GeckoDriver with Firefox.

#### Solution

None yet. Solving this problem is dependent on a GeckoDriver fix.

<!-- Link Definitions -->
[Headless Chrome]: https://developers.google.com/web/updates/2017/04/headless-chrome
[ChromeDriver 74.0.3729.6+]: https://chromedriver.storage.googleapis.com/index.html?path=2.33/
[Selenium Server Standalone v3.9+]: http://www.seleniumhq.org/download/
