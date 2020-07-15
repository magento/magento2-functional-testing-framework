# Extend action groups

Extending an action group doesn't affect the existing action group.

In this example we add a `<click>` command to check the checkbox that our extension added with a new action group for the simple product creation form.

## Starting action group

<!-- {% raw %} -->

```xml
<actionGroup name="AdminFillSimpleProductFormActionGroup">
    <arguments>
        <argument name="category"/>
        <argument name="simpleProduct"/>
    </arguments>
    <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="navigateToProductIndex"/>
    <click selector="{{AdminProductGridActionSection.addProductToggle}}" stepKey="clickAddProductDropdown"/>
    <click selector="{{AdminProductGridActionSection.addSimpleProduct}}" stepKey="clickAddSimpleProduct"/>
    <fillField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="fillName"/>
    <fillField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="fillSKU"/>
    <fillField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="fillPrice"/>
    <fillField userInput="{{simpleProduct.quantity}}" selector="{{AdminProductFormSection.productQuantity}}" stepKey="fillQuantity"/>
    <searchAndMultiSelectOption selector="{{AdminProductFormSection.categoriesDropdown}}" parameterArray="[{{category.name}}]" stepKey="searchAndSelectCategory"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSection"/>
    <fillField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="fillUrlKey"/>
    <click selector="{{AdminProductFormActionSection.saveButton}}" stepKey="saveProduct"/>
    <seeElement selector="{{AdminProductMessagesSection.successMessage}}" stepKey="assertSaveMessageSuccess"/>
    <seeInField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="assertFieldName"/>
    <seeInField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="assertFieldSku"/>
    <seeInField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="assertFieldPrice"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSectionAssert"/>
    <seeInField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="assertFieldUrlKey"/>
</actionGroup>
```

## Extend file

```xml
<actionGroup name="AdminFillSimpleProductFormWithMyExtensionActionGroup" extends="AdminFillSimpleProductFormActionGroup">
    <!-- This will be added after the step "fillQuantity" on line 12 in the above test. -->
    <click selector="{{MyExtensionSection.myCheckbox}}" stepKey="clickMyCheckbox" after="fillQuantity"/>
</actionGroup>
```

## Resultant action group

Note that there are now two action groups below.

```xml
<actionGroup name="AdminFillSimpleProductFormActionGroup">
    <arguments>
        <argument name="category"/>
        <argument name="simpleProduct"/>
    </arguments>
    <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="navigateToProductIndex"/>
    <click selector="{{AdminProductGridActionSection.addProductToggle}}" stepKey="clickAddProductDropdown"/>
    <click selector="{{AdminProductGridActionSection.addSimpleProduct}}" stepKey="clickAddSimpleProduct"/>
    <fillField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="fillName"/>
    <fillField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="fillSKU"/>
    <fillField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="fillPrice"/>
    <fillField userInput="{{simpleProduct.quantity}}" selector="{{AdminProductFormSection.productQuantity}}" stepKey="fillQuantity"/>
    <searchAndMultiSelectOption selector="{{AdminProductFormSection.categoriesDropdown}}" parameterArray="[{{category.name}}]" stepKey="searchAndSelectCategory"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSection"/>
    <fillField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="fillUrlKey"/>
    <click selector="{{AdminProductFormActionSection.saveButton}}" stepKey="saveProduct"/>
    <seeElement selector="{{AdminProductMessagesSection.successMessage}}" stepKey="assertSaveMessageSuccess"/>
    <seeInField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="assertFieldName"/>
    <seeInField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="assertFieldSku"/>
    <seeInField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="assertFieldPrice"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSectionAssert"/>
    <seeInField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="assertFieldUrlKey"/>
</actionGroup>
<actionGroup name="AdminFillSimpleProductFormWithMyExtensionActionGroup">
    <arguments>
        <argument name="category"/>
        <argument name="simpleProduct"/>
    </arguments>
    <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="navigateToProductIndex"/>
    <click selector="{{AdminProductGridActionSection.addProductToggle}}" stepKey="clickAddProductDropdown"/>
    <click selector="{{AdminProductGridActionSection.addSimpleProduct}}" stepKey="clickAddSimpleProduct"/>
    <fillField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="fillName"/>
    <fillField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="fillSKU"/>
    <fillField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="fillPrice"/>
    <fillField userInput="{{simpleProduct.quantity}}" selector="{{AdminProductFormSection.productQuantity}}" stepKey="fillQuantity"/>

    <click selector="{{MyExtensionSection.myCheckbox}}" stepKey="clickMyCheckbox"/>

    <searchAndMultiSelectOption selector="{{AdminProductFormSection.categoriesDropdown}}" parameterArray="[{{category.name}}]" stepKey="searchAndSelectCategory"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSection"/>
    <fillField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="fillUrlKey"/>
    <click selector="{{AdminProductFormActionSection.saveButton}}" stepKey="saveProduct"/>
    <seeElement selector="{{AdminProductMessagesSection.successMessage}}" stepKey="assertSaveMessageSuccess"/>
    <seeInField userInput="{{simpleProduct.name}}" selector="{{AdminProductFormSection.productName}}" stepKey="assertFieldName"/>
    <seeInField userInput="{{simpleProduct.sku}}" selector="{{AdminProductFormSection.productSku}}" stepKey="assertFieldSku"/>
    <seeInField userInput="{{simpleProduct.price}}" selector="{{AdminProductFormSection.productPrice}}" stepKey="assertFieldPrice"/>
    <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSectionAssert"/>
    <seeInField userInput="{{simpleProduct.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="assertFieldUrlKey"/>
</actionGroup>
```

<!-- {% endraw %} -->