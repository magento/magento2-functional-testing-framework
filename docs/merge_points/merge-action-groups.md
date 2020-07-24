# Merge action groups

An action group is a set of individual actions working together as a group.
These action groups can be shared between tests and they also can be modified to your needs.

In this example we add a `<click>` command to check the checkbox that our extension adds to the simple product creation form.

## Starting test

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

## File to merge

```xml
<actionGroup name="AdminFillSimpleProductFormActionGroup">
    <!-- This will be added after the step "fillQuantity" in the above test. -->
    <click selector="{{MyExtensionSection.myCheckbox}}" stepKey="clickMyCheckbox" after="fillQuantity"/>
</actionGroup>
```

## Resultant test

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
    <!-- Merged line here -->
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