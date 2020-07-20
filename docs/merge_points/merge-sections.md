# Merge sections

Sections can be merged together to cover your extension.

In this example we add another selector to the section on the products page section.

## Starting section

<!-- {% raw %} -->

```xml
<section name="AdminProductsPageSection">
    <element name="addProductButton" type="button" selector="//button[@id='add_new_product-button']"/>
    <element name="checkboxForProduct" type="button" selector="//*[contains(text(),'{{args}}')]/parent::td/preceding-sibling::td/label[@class='data-grid-checkbox-cell-inner']" parameterized="true"/>
    <element name="actions" type="button" selector="//div[@class='col-xs-2']/div[@class='action-select-wrap']/button[@class='action-select']"/>
    <element name="delete" type="button" selector="//*[contains(@class,'admin__data-grid-header-row row row-gutter')]//*[text()='Delete']"/>
    <element name="ok" type="button" selector="//button[@data-role='action']//span[text()='OK']"/>
    <element name="deletedSuccessMessage" type="button" selector="//*[@class='message message-success success']"/>
</section>
```

## File to merge

```xml
<section name="AdminProductsPageSection">
    <!-- myExtensionElement will simply be added to the page -->
    <element name="myExtensionElement" type="button" selector="input.myExtension"/>
</section>
```

## Resultant section

```xml
<section name="AdminProductsPageSection">
    <element name="addProductButton" type="button" selector="//button[@id='add_new_product-button']"/>
    <element name="checkboxForProduct" type="button" selector="//*[contains(text(),'{{args}}')]/parent::td/preceding-sibling::td/label[@class='data-grid-checkbox-cell-inner']" parameterized="true"/>
    <element name="actions" type="button" selector="//div[@class='col-xs-2']/div[@class='action-select-wrap']/button[@class='action-select']"/>
    <element name="delete" type="button" selector="//*[contains(@class,'admin__data-grid-header-row row row-gutter')]//*[text()='Delete']"/>
    <element name="ok" type="button" selector="//button[@data-role='action']//span[text()='OK']"/>
    <element name="deletedSuccessMessage" type="button" selector="//*[@class='message message-success success']"/>
    <!-- New element merged -->
    <element name="myExtensionElement" type="button" selector="input.myExtension"/>
</section>
```

<!-- {% endraw %} -->