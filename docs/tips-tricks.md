# Tips and Tricks

Sometimes, little changes can make a big difference in your project. Here are some test writing tips to keep everything running smoothly.

## Actions and action groups

### Use parameterized selectors in action groups with argument references

Clarity and readability are important factors in good test writing.
Having to parse through unreadable code can be time consuming. Save time by writing clearly.
The good example clearly shows what the selector arguments refer to.
In the bad example we see two parameters being passed into the selector with little clue as to their purpose.

**Why?** The next person maintaining the test or extending it may not be able to understand what the parameters are referencing.

<span style="color:green">
Good
</span>

<!-- {% raw %} -->

```xml
<test>
    <actionGroup ref="VerifyOptionInProductStorefront" stepKey="verifyConfigurableOption" after="AssertProductInStorefrontProductPage">
        <argument name="attributeCode" value="$createConfigProductAttribute.default_frontend_label$"/>
        <argument name="optionName" value="$createConfigProductAttributeOption1.option[store_labels][1][label]$"/>
    </actionGroup>
</test>

<actionGroup name="VerifyOptionInProductStorefront">
    <arguments>
        <argument name="attributeCode" type="string"/>
        <argument name="optionName" type="string"/>
    </arguments>
    <seeElement selector="{{StorefrontProductInfoMainSection.attributeOptionByAttributeID(attributeCode, optionName)}}" stepKey="verifyOptionExists"/>
</actionGroup>
```

<span style="color:red">
Bad
</span>

```xml
<test>
    <seeElement selector="{{StorefrontProductInfoMainSection.attributeOptionByAttributeID($createConfigProductAttribute.default_frontend_label$, $createConfigProductAttributeOption1.option[store_labels][1][label]$)}}" stepKey="verifyOptionExists"/>
</test>
```

### Perform the most critical actions first in the `<after>` block

Perform non-browser driving actions first. These are more likely to succeed as no UI is involved.
In the good example, `magentoCLI` and `deleteData` are run first to ensure a proper state.
In the bad example, we perform some heavy UI steps first.

**Why?** If something goes wrong there, then the critical `magentoCLI` commands may not get a chance to run, leaving Magento configured incorrectly for any upcoming tests.

<span style="color:green">
Good:
</span>

```xml
<after>
    <magentoCLI command="indexer:set-mode" arguments="schedule" stepKey="setIndexerMode"/>
    <magentoCLI command="config:set catalog/frontend/flat_catalog_category 0" stepKey="setFlatCatalogCategory"/>
    <deleteData createDataKey="category" stepKey="deleteCategory"/>
    <deleteData createDataKey="createSimpleProduct" stepKey="deleteSimpleProduct"/>
    <actionGroup ref="AdminDeleteStoreViewActionGroup" stepKey="deleteStoreViewEn">
        <argument name="customStore" value="customStoreEN"/>
    </actionGroup>
    <actionGroup ref="AdminDeleteStoreViewActionGroup" stepKey="deleteStoreViewFr">
        <argument name="customStore" value="customStoreFR"/>
    </actionGroup>
    <actionGroup ref="logout" stepKey="logout"/>
</after>
```

<span style="color:red">
Bad:
</span>

```xml
<after>
    <actionGroup ref="AdminDeleteStoreViewActionGroup" stepKey="deleteStoreViewEn">
        <argument name="customStore" value="customStoreEN"/>
    </actionGroup>
    <actionGroup ref="AdminDeleteStoreViewActionGroup" stepKey="deleteStoreViewFr">
        <argument name="customStore" value="customStoreFR"/>
    </actionGroup>
    <deleteData createDataKey="category" stepKey="deleteCategory"/>
    <deleteData createDataKey="createSimpleProduct" stepKey="deleteSimpleProduct"/>
    <magentoCLI command="config:set catalog/frontend/flat_catalog_category 0" stepKey="setFlatCatalogCategory"/>
    <magentoCLI command="indexer:set-mode" arguments="schedule" stepKey="setIndexerMode"/>
    <actionGroup ref="logout" stepKey="logout"/>
</after>
```

### When to use see vs. seeElement

Use `see` and `seeElement` wisely.
If you need to see some element and verify that the text inside is shown correctly, use the `see` action.
If you need to verify that element present on page, use `seeElement`.
But never use `seeElement` and build a xPath which contains the expected text.

**Why?** For `see` it will output something similar to this:
`Failed asserting that any element by #some_selector contains text "some_text"`
And for `seeElement` it will output something like this:
`Element by #some_selector is not visible.`
There is a subtle distinction: The first is a failure but it is the desired result: a 'positive failure'.
The second is a proper result of the action.

<span style="color:green">
Good:
</span>

```xml
<see selector="//div[@data-element='content']//p" userInput="SOME EXPECTED TEXT" stepKey="seeSlide1ContentStorefront"/>
```

<span style="color:red">
Bad:
</span>

```xml
<seeElement selector="//div[@data-element='content']//p[.='SOME EXPECTED TEXT']" stepKey="seeSlide1ContentStorefront"/>
```

### Always specify a default value for action group arguments

Whenever possible, specify a `defaultValue` for action group arguments.

<span style="color:green">
GOOD:
</span>

```xml
<actionGroup name="StorefrontAssertProductImagesOnProductPageActionGroup">
    <arguments>
        <argument name="productImage" type="string" defaultValue="Magento_Catalog/images/product/placeholder/image.jpg" />
    </arguments>
    <waitForElementNotVisible selector="{{StorefrontProductMediaSection.gallerySpinner}}" stepKey="waitGallerySpinnerDisappear" />
    <seeElement selector="{{StorefrontProductMediaSection.gallery}}" stepKey="seeProductGallery" />
    <seeElement selector="{{StorefrontProductMediaSection.productImage(productImage)}}" stepKey="seeProductImage" />
    <click selector="{{StorefrontProductMediaSection.productImage(productImage)}}" stepKey="openFullscreenImage" />
    <waitForPageLoad stepKey="waitForGalleryLoaded" />
    <seeElement selector="{{StorefrontProductMediaSection.productImageFullscreen(productImage)}}" stepKey="seeFullscreenProductImage" />
    <click selector="{{StorefrontProductMediaSection.closeFullscreenImage}}" stepKey="closeFullScreenImage" />
    <waitForPageLoad stepKey="waitForGalleryDisappear" />
</actionGroup>
```

<span style="color:red">
BAD:
</span>

```xml
<actionGroup name="StorefrontAssertProductImagesOnProductPageActionGroup">
    <arguments>
        <argument name="productImage" type="string" />
    </arguments>
    <waitForElementNotVisible selector="{{StorefrontProductMediaSection.gallerySpinner}}" stepKey="waitGallerySpinnerDisappear" />
    <seeElement selector="{{StorefrontProductMediaSection.gallery}}" stepKey="seeProductGallery" />
    <seeElement selector="{{StorefrontProductMediaSection.productImage(productImage)}}" stepKey="seeProductImage" />
    <click selector="{{StorefrontProductMediaSection.productImage(productImage)}}" stepKey="openFullscreenImage" />
    <waitForPageLoad stepKey="waitForGalleryLoaded" />
    <seeElement selector="{{StorefrontProductMediaSection.productImageFullscreen(productImage)}}" stepKey="seeFullscreenProductImage" />
    <click selector="{{StorefrontProductMediaSection.closeFullscreenImage}}" stepKey="closeFullScreenImage" />
    <waitForPageLoad stepKey="waitForGalleryDisappear" />
</actionGroup>
```

### Build tests from action groups

Build your tests using action groups, even if an action group contains a single action.

**Why?** For extension developers, this will make it easier to extend or customize tests.
Extending a single action group will update all tests that use this group.
This improves maintainability as multiple instances of a failure can be fixed with a single action group update.

<span style="color:green">
GOOD:
</span>

```xml
<test name="NavigateClamberWatchEntityTest">
    <annotations>
        <!--some annotations-->
    </annotations>

    <actionGroup ref="StorefrontOpenProductPageActionGroup" stepKey="openProductPage">
        <argument name="productUrl" value="{{ClamberWatch.url_key}}" />
    </actionGroup>
    <actionGroup ref="StorefrontAssertProductNameOnProductPageActionGroup" stepKey="assertProductName">
        <argument name="productName" value="{{ClamberWatch.name}}" />
    </actionGroup>
    <actionGroup ref="StorefrontAssertProductSkuOnProductPageActionGroup" stepKey="assertProductSku">
        <argument name="productSku" value="{{ClamberWatch.sku}}" />
    </actionGroup>
    <actionGroup ref="StorefrontAssertProductPriceOnProductPageActionGroup" stepKey="assertProductPrice">
        <argument name="productPrice" value="{{ClamberWatch.price}}" />
    </actionGroup>
    <actionGroup ref="StorefrontAssertProductImagesOnProductPageActionGroup" stepKey="assertProductImage">
        <argument name="productImage" value="{{ClamberWatch.image}}" />
    </actionGroup>
</test>
```

<span style="color:red">
BAD:
</span>

```xml
<test name="NavigateClamberWatchEntityTest">
    <annotations>
                <!--some annotations-->
    </annotations>

    <amOnPage url="{{StorefrontProductPage.url(ClamberWatch.url_key)}}" stepKey="openProductPage"/>
    <see selector="{{StorefrontProductInfoMainSection.productName}}" userInput="{{ClamberWatch.name}}" stepKey="seeProductName" />
    <see selector="{{StorefrontProductInfoMainSection.productSku}}" userInput="{{ClamberWatch.sku}}" stepKey="seeProductSku" />
    <see selector="{{StorefrontProductInfoMainSection.price}}" userInput="{{ClamberWatch.price}}" stepKey="seeProductPrice" />
    <waitForElementNotVisible selector="{{StorefrontProductMediaSection.gallerySpinner}}" stepKey="waitGallerySpinnerDisappear" />
    <seeElement selector="{{StorefrontProductMediaSection.gallery}}" stepKey="seeProductGallery" />
    <seeElement selector="{{StorefrontProductMediaSection.productImage(ClamberWatch.productImage)}}" stepKey="seeProductImage" />
    <click selector="{{StorefrontProductMediaSection.productImage(ClamberWatch.productImage)}}" stepKey="openFullscreenImage" />
    <waitForPageLoad stepKey="waitForGalleryLoaded" />
    <seeElement selector="{{StorefrontProductMediaSection.productImageFullscreen(ClamberWatch.productImage)}}" stepKey="seeFullscreenProductImage" />
</test>
```

### Use descriptive stepKey names

Make `stepKeys` values as descriptive as possible.
Do not use numbers to make a `stepKey` unique.

**Why?** This helps with readability and clarity.

<span style="color:green">
GOOD:
</span>

```xml
<click selector="{{StorefrontNavigationSection.topCategory(SimpleSubCategory.name)}}" stepKey="clickSimpleSubCategoryLink" />
<waitForPageLoad stepKey="waitForSimpleSubCategoryPageLoad" />
<click selector="{{StorefrontCategoryMainSection.productLinkByHref(SimpleProduct.urlKey)}}" stepKey="clickSimpleProductLink" />
<waitForPageLoad stepKey="waitForSimpleProductPageLoad" />

<!-- Perform some actions / Assert product page -->

<click selector="{{StorefrontNavigationSection.topCategory(CustomCategory.name)}}" stepKey="clickCustomCategoryLink" />
<waitForPageLoad stepKey="waitForCustomCategoryPageLoad" />
<click selector="{{StorefrontCategoryMainSection.productLinkByHref(CustomSimpleProduct.urlKey)}}" stepKey="clickCustomSimpleProductLink" />
<waitForPageLoad stepKey="waitForCustomSimpleProductPageLoad" />
```

<span style="color:red">
BAD:
</span>

```xml
<click selector="{{StorefrontNavigationSection.topCategory(SimpleSubCategory.name)}}" stepKey="clickCategoryLink1" />
<waitForPageLoad stepKey="waitForPageLoad1" />
<click selector="{{StorefrontCategoryMainSection.productLinkByHref(SimpleProduct.urlKey)}}" stepKey="clickProductLink1" />
<waitForPageLoad stepKey="waitForPageLoad2" />

<!-- Perform some actions / Assert product page -->

<click selector="{{StorefrontNavigationSection.topCategory(CustomCategory.name)}}" stepKey="clickCategoryLink2" />
<waitForPageLoad stepKey="waitForPageLoad3" />
<click selector="{{StorefrontCategoryMainSection.productLinkByHref(CustomSimpleProduct.urlKey)}}" stepKey="clickProductLink2" />
<waitForPageLoad stepKey="waitForPageLoad4" />
```

**Exception:**

Use numbers within `stepKeys` when order is important, such as with testing sort order.

```xml
<createData entity="BasicMsiStock1" stepKey="createCustomStock1"/>
<createData entity="BasicMsiStock2" stepKey="createCustomStock2"/>
<createData entity="BasicMsiStock3" stepKey="createCustomStock3"/>
<createData entity="BasicMsiStock4" stepKey="createCustomStock4"/>
```

## Selectors

### Use contains() around text()

When possible, use `contains(text(), 'someTextHere')` rather than `text()='someTextHere'`.
`contains()` ignores whitespace while `text()` accounts for it.

**Why?**
If you are comparing text within a selector and have an unexpected space, or a blank line above or below the string, `text()` will fail while the `contains(text())` format will catch it.
In this scenario `text()` is more exacting. Use it when you need to be very precise about what is getting compared.

<span style="color:green">
GOOD:
</span>

`//span[contains(text(), 'SomeTextHere')]`

<span style="color:red">
BAD:
</span>

`//span[text()='SomeTextHere']`

### Build selectors in proper order

When building selectors for form elements, start with the parent context of the form element.
Then specify the element `name` attribute in your selector to ensure the correct element is targeted.
To build a selector for an input, use the pattern: `{{section_selector}} {{input_selector}}` or for a button: `{{section_selector}} {{button_selector}}`

**Why?** Traversing the DOM takes a finite amount of time and reducing the scope of the selector makes the selector lookup as efficient as possible.

Example:

```xml
<div class="admin__field _required" data-bind="css: $data.additionalClasses, attr: {'data-index': index}, visible: visible" data-index="name">
    <div class="admin__field-label" data-bind="visible: $data.labelVisible">
        <span data-bind="attr: {'data-config-scope': $data.scopeLabel}, i18n: label" data-config-scope="[STORE VIEW]">Product Name</span>
    </div>
    <div class="admin__field-control" data-bind="css: {'_with-tooltip': $data.tooltip, '_with-reset': $data.showFallbackReset && $data.isDifferedFromDefault}">
        <input class="admin__control-text" type="text" name="product[name]" aria-describedby="notice-EXNI71H" id="EXNI71H" maxlength="255" data-bind="
            attr: {
                name: inputName,
                placeholder: placeholder,
                maxlength: 255}"/>
    </div>
</div>
```

<span style="color:green">
GOOD:
</span>

```xml
<element name="productName" type="input" selector="*[data-index='product-details'] input[name='product[name]']"/>
```

<span style="color:red">
BAD:
</span>

```xml
<element name="productName" type="input" selector=".admin__field[data-index=name] input"/>
```

## General tips

### Use data references to avoid hardcoded values

If you need to run a command such as  `<magentoCLI command="config:set" />`, do not hardcode paths and values to the command.
Rather, create an appropriate `ConfigData.xml` file, which contains the required parameters for running the command.
It will simplify the future maintanence of tests.

 <span style="color:green">
GOOD:
</span>

```xml
<magentoCLI command="config:set {{StorefrontCustomerCaptchaLength3ConfigData.path}} {{StorefrontCustomerCaptchaLength3ConfigData.value}}" stepKey="setCaptchaLength" />
```

 <span style="color:red">
BAD:
</span>

```xml
<magentoCLI command="config:set customer/captcha/length 3" stepKey="setCaptchaLength" />
```

For example:
[This test][] refers to this [Data file][].

### Use descriptive variable names

Use descriptive variable names to increase readability.
**Why?** It makes the code easier to follow and update.

 <span style="color:green">
GOOD:
</span>

```xml
<element name="storeName" type="checkbox" selector="//label[contains(text(),'{{storeName}}')]" parameterized="true"/>
```

<span style="color:red">
BAD:
</span>

```xml
<element name="storeName" type="checkbox" selector="//label[contains(text(),'{{var1}}')]" parameterized="true"/>
```

### Use proper checkbox actions

When working with input type `checkbox`, do not use the `click` action; use `checkOption` or `uncheckOption` instead.
**Why?** A click does not make it clear what the ending state will be; it will simply toggle the current state. Using the proper actions will ensure the expected state of the checkbox.

<span style="color:green">
GOOD:
</span>

```xml
<checkOption selector="{{ProductInWebsitesSection.website('Second Website')}}" stepKey="selectSecondWebsite"/>
<uncheckOption selector="{{ProductInWebsitesSection.website('Second Website')}}" stepKey="unselectSecondWebsite"/>
```

<span style="color:red">
BAD:
</span>

```xml
<click selector="{{ProductInWebsitesSection.website('Second Website')}}" stepKey="selectSecondWebsite"/>
<click selector="{{ProductInWebsitesSection.website('Second Website')}}" stepKey="unselectSecondWebsite"/>
```

<!--{% endraw %}-->

<!-- Link Definitions -->
[This test]: https://github.com/magento/magento2/blob/2.3/app/code/Magento/Captcha/Test/Mftf/Test/StorefrontCaptchaRegisterNewCustomerTest.xml#L24
[Data file]: https://github.com/magento/magento2/blob/2.3/app/code/Magento/Captcha/Test/Mftf/Data/CaptchaConfigData.xml
