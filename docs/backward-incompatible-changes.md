# MFTF 3.0.0 backward incompatible changes

This page highlights backward incompatible changes between releases that have a major impact and require detailed explanation and special instructions to ensure third-party tests continue working with Magento core tests.

## Minimum supported PHP version changes

We changed the minimum PHP version requirement from 7.0 to 7.3. Because of the PHP version requirement change, this MFTF version only supports Magento 2.4 or later.

## Folder structure changes

We removed support to read test modules from the deprecated path `dev/tests/acceptance/tests/functional/Magento/FunctionalTest`. If there are test modules in this path, they should be moved to `dev/tests/acceptance/tests/functional/Magento`. 

## XSD schema changes

-  Files under test modules `ActionGroup`, `Page`, `Section`, `Test` and `Suite` only support a single entity per file. 
-  The `file` attribute from `<module>` has been removed from the suite schema. `<module file=""/>` is no longer supported in suites.
-  Metadata filename format changed to ***`*Meta.xml`***.
-  Only nested assertion syntax will be supported. See the [assertions page](test/assertions.md) for details. Here is an example of the nested assertion syntax:
   ```xml
   <assertEquals stepKey="assertAddressOrderPage">
      <actualResult type="const">$billingAddressOrderPage</actualResult>
      <expectedResult type="const">$shippingAddressOrderPage</expectedResult>
   </assertEquals>
   ```

### Upgrading tests to the new schema

The following table lists the upgrade scripts that are available to upgrade tests to the new schema.

| Script name           | Description                                                                                               |
|-----------------------|-----------------------------------------------------------------------------------------------------------|
|`splitMultipleEntitiesFiles`| Splits files that have multiple entities into multiple files with one entity per file. |
|`upgradeAssertionSchema`| Updates assert actions that uses the old assertion syntax into the new nested syntax.|
|`renameMetadataFiles`| Renames Metadata filenames to `*Meta.xml`.|
|`removeModuleFileInSuiteFiles`| Removes occurrences of `<module file=""/>` from all `<suite>`s.|
|`removeUnusedArguments`| Removes unused arguments from action groups.|
|`upgradeTestSchema`| Replaces relative schema paths to URN in test files.| 

To run the upgrade tests:

1. Run `bin/mftf reset --hard` to remove old generated configurations.
1. Run `bin/mftf build:project` to generate new configurations.
1. Run `bin/mftf upgrade:tests`. [See command page for details](commands/mftf.md#upgradetests).
1. Lastly, try to generate all tests. Tests should all be generated as a result of the upgrades. If not, the most likely issue will be a changed XML schema. Check error messaging and search your codebase for the attributes listed.

## MFTF commands

`--debug` option `NONE` removed for strict schema validation. Ensure there are no schema validation errors in test modules before running MFTF commands.

## MFTF actions

### `executeInSelenium` and `performOn` removed

**Action**: Deprecated actions `executeInSelenium` and `performOn` are removed in favor of new action `helper`.

**Reason**: `executeInSelenium` and `performOn` allowed custom PHP code to be written inline inside of XML files which was difficult to maintain, troubleshoot, and modify.

**Details**: 

The `helper` allows test writers to solve advanced requirements beyond what MFTF offers out of the box. See [custom-helpers](custom-helpers.md) for more information on usage. 

Here is an example of using `helper` in place of `executeSelenium` to achieve same workflow.

Old usage:

```xml
<executeInSelenium function="function ($webdriver) use ($I) {
        $heading = $webdriver->findElement(\Facebook\WebDriver\WebDriverBy::xpath('//div[contains(@class, \'inline-wysiwyg\')]//h2'));
        $actions = new \Facebook\WebDriver\Interactions\WebDriverActions($webdriver);
        $actions->moveToElement($heading, {{TinyMCEPartialHeadingSelection.startX}}, {{TinyMCEPartialHeadingSelection.startY}})
         ->clickAndHold()
         ->moveToElement($heading, {{TinyMCEPartialHeadingSelection.endX}}, {{TinyMCEPartialHeadingSelection.endY}})
         ->release()
         ->perform();
        }" stepKey="selectHeadingTextInTinyMCE"/>
```    

New usage:

```xml
<helper class="\Magento\PageBuilder\Test\Mftf\Helper\SelectText" method="selectText" stepKey="selectHeadingTextInTinyMCE">
    <argument name="context">//div[contains(@class, 'inline-wysiwyg')]//h2</argument>
    <argument name="startX">{{TinyMCEPartialHeadingSelection.startX}}</argument>
    <argument name="startY">{{TinyMCEPartialHeadingSelection.startY}}</argument>
    <argument name="endX">{{TinyMCEPartialHeadingSelection.endX}}</argument>
    <argument name="endY">{{TinyMCEPartialHeadingSelection.endY}}</argument>
</helper>
```

### `pauseExecution` removed

**Action**: `pauseExecution` is removed in favor of `pause`.

**Reason**: `[WebDriver]pauseExecution` is removed in Codeception 3 in favor of `I->pause()`.

**Details**: 

See the [actions page for details](test/actions.md#pause). Here is a usage example:

```xml
<pause stepKey="pauseExecutionKey"/>
```

### Removed assert actions

**Action**: Assert actions `assertInternalType`, `assertNotInternalType` and `assertArraySubset` are removed.

**Reason**: PHPUnit 9 has dropped support for these assertions.

### Updated assert actions

**Action**: The `delta` attribute has been removed from `assertEquals` and `assertNotEquals`. Instead, new assert actions have been introduced:

 -  `assertEqualsWithDelta`
 -  `assertNotEqualsWithDelta` 
 -  `assertEqualsCanonicalizing`
 -  `assertNotEqualsCanonicalizing`
 -  `assertEqualsIgnoringCase`
 -  `assertNotEqualsIgnoringCase`

**Reason**: PHPUnit 9 has dropped support for optional parameters for `assertEquals` and `assertNotEquals` and has introduced these new assertions.

**Details**: 

Usage of `assertEquals` or `assertNotEquals` with a specified `delta`, should be replaced with appropriate assertion from the above list.

### `assertContains` supports only iterable haystacks

**Action**: `assertContains` and `assertNotContains` now only supports iterable haystacks. These assert actions have been added to work with string haystacks:

-  `assertStringContainsString`
-  `assertStringNotContainsString`
-  `assertStringContainsStringIgnoringCase`
-  `assertStringNotContainsStringIgnoringCase`

**Reason**: With PHPUnit 9, `assertContains` and `assertNotContains` only allows iterable haystacks. New assertions have been introduced to support string haystacks.

**Details**: 

Usages of `assertContains` and `assertNotContains` with string haystacks should be replaced with appropriate assertion from the above list.

Usage example for string haystacks:

```xml
<assertStringContainsString stepKey="assertDiscountOnPrice2">
  <actualResult type="const">$grabSimpleProdPrice2</actualResult>
  <expectedResult type="string">$110.70</expectedResult>
</assertStringContainsString>
```

### `formatMoney` removed

**Action**: `formatMoney` has been removed in favor of `formatCurrency`.

**Reason**: PHP 7.4 has deprecated use of `formatMoney`. 

**Details**: Format input to specified currency according to the locale specified. 

Usage example:

```xml
<formatCurrency userInput="1234.56789000" locale="de_DE" currency="USD" stepKey="usdInDE"/>
```
