# Custom Helpers

<div class="bs-callout-warning">
Due to complexity, you should only write new custom helpers as a last resort, after trying to implement your test using built-in actions.
</div>

Custom Helpers allow test writers to write custom test actions to solve advanced requirements beyond what MFTF offers out of the box.

In MFTF version 3.0.0, the following test actions were removed:

*  `<executeInSelenium>`
*  `<performOn>`

These actions were removed because they allowed custom PHP code to be written inline inside of XML files. This code was difficult to read. It had no proper syntax highlighting and no linting. It was difficult to maintain, troubleshoot, and modify.

However, sometimes custom logic beyond what MFTF offers is necessary so we have provided an alternative solution: the `<helper>` action.

## Example

Custom helpers are implemented in PHP files that must be placed in this directory:

```text
<ModuleName>/Test/Mftf/Helper
```

This custom helper selects text on the page with this approach:

1. Move to a very specific X,Y starting position.
1. Click and hold the mouse button down.
1. Move to another specific X,Y position.
1. Release the mouse button.

This functionality is used to select text on the page and cannot be accomplished using built-in test actions.

### PHP File

```php
<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PageBuilder\Test\Mftf\Helper;

use Magento\FunctionalTestingFramework\Helper\Helper;

/**
 * Class SelectText provides an ability to select needed text.
 */
class SelectText extends Helper
{
    /**
     * Select needed text.
     *
     * @param string $context
     * @param int $startX
     * @param int $startY
     * @param int $endX
     * @param int $endY
     * @return void
     */
    public function selectText(string $context, int $startX, int $startY, int $endX, int $endY)
    {
        try {
            /** @var \Magento\FunctionalTestingFramework\Module\MagentoWebDriver $webDriver */
            $webDriver = $this->getModule('\Magento\FunctionalTestingFramework\Module\MagentoWebDriver');

            $contextElement = $webDriver->webDriver->findElement(\Facebook\WebDriver\WebDriverBy::xpath($context));
            $actions = new \Facebook\WebDriver\Interactions\WebDriverActions($webDriver->webDriver);
            $actions->moveToElement($contextElement, $startX, $startY)
                ->clickAndHold()
                ->moveToElement($contextElement, $endX, $endY)
                ->release()
                ->perform();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
```

### Notes about this PHP file

The following details are important about the above file:

1. The `namespace` must match the file location: `namespace Magento\PageBuilder\Test\Mftf\Helper;`
2. The class must `extends Helper` and have the corresponding `use` statement to match.
3. You may access the WebDriver object via `$this->getModule('\Magento\FunctionalTestingFramework\Module\MagentoWebDriver')`.
4. You may implement multiple related methods within the same class.
5. Specify the correct function argument types to match the type of values you want to pass in. In this case, we specified `string $context, int $startX, int $startY, int $endX, int $endY`. In the XML we will match these types.

You should follow the same patterns in any custom helpers that you write yourself, but you may implement any logic or iteration that you need to solve for your use case.

### Referencing in a test

Once you have implemented something like the above PHP file, reference it in a test:

```xml
<helper class="\Magento\PageBuilder\Test\Mftf\Helper\SelectText" method="selectText" stepKey="selectHeadingTextInTinyMCE">
    <argument name="context">//div[contains(@class, 'inline-wysiwyg')]//h2</argument>
    <argument name="startX">{{TinyMCEPartialHeadingSelection.startX}}</argument>
    <argument name="startY">{{TinyMCEPartialHeadingSelection.startY}}</argument>
    <argument name="endX">{{TinyMCEPartialHeadingSelection.endX}}</argument>
    <argument name="endY">{{TinyMCEPartialHeadingSelection.endY}}</argument>
</helper>
```

### Notes about the XML

1. Specify an argument value for every argument that matches our PHP implementation. This allows us to pass other test data to the Custom Helper.
1. The `class` attribute matches the namespace specified in the PHP file.
1. Specify the method from the class via the `method` attribute.
1. If the function has a return value, it will be assigned to the `stepKey` variable. In this case, `$selectHeadingTextInTinyMCE` holds the return value.
1. The types of argument values must match the PHP implementation's expected types.

## Key takeaways

Custom helpers allow you to solve complex use cases such as conditional logic, iteration, or complex WebDriver usage.

With access to the WebDriver object, you have a lot of flexibility available to you. See the [Codeception WebDriver](https://github.com/Codeception/module-webdriver/blob/master/src/Codeception/Module/WebDriver.php) for technical details and functionality available for use.

A custom helper is written in a PHP file and then referenced in test XML, like other actions.

You should only use these as a last resort after trying to implement your test using built-in actions.

## References

[Codeception WebDriver source code](https://github.com/Codeception/module-webdriver/blob/master/src/Codeception/Module/WebDriver.php) - Reference for using the WebDriver Object
