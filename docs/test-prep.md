# Preparing a test for MFTF

This tutorial demonstrates the process of converting a raw functional test into a properly abstracted test file, ready for publishing.

## The abstraction process

When first writing a test for a new piece of code such as a custom extension, it is likely that values are hardcoded for the specific testing environment while in development. To make the test more generic and easier for others to update and use, we need to abstract the test.
The general process:

1. Convert the manual test to a working, hard-coded test.
1. Replace hardcoded selectors to a more flexible format such as [parameterized selectors][].
1. Convert hardcoded form values and other data to data entities.
1. Convert [actions][] into [action groups][].

## The manual test

Manual tests are just that: A series of manual steps to be run.

```xml
<!-- Navigate to Catalog -> Products page (or just open by link) -->
<!-- Fill field "Name" with "Simple Product %unique_value%" -->
<!-- Fill field "SKU" with "simple_product_%unique_value%" -->
<!-- Fill field "Price" with "500.00" -->
<!-- Fill field "Quantity" with "100" -->
<!-- Fill field "Weight" with "100" -->
<!-- Click "Save" button -->
<!-- See success save message "You saved the product." -->
<!-- Navigate to Catalog -> Products page (or just open by link) -->
<!-- See created product is in grid -->
<!-- See "Name" in grid is valid -->
<!-- See "SKU" in grid is valid -->
<!-- See "Price" in grid is valid -->
<!-- See "Quantity" in grid is valid -->
<!-- Open Storefront Product Page and verify "Name", "SKU", "Price" -->
```

## The raw test

This test works just fine. But it will only work if everything referenced in the test stays exactly the same. This neither reusable nor extensible.
Hardcoded selectors make it impossible to reuse sections in other action groups and tasks. They can also be brittle. If Magento happens to change a `class` or `id` on an element, the test will fail.

Some data, like the SKU in this example, must be unique for every test run. Hardcoded values will fail here. [Data entities][] allow for `suffix` and `prefix` for ensuring unique data values.

For our example, we have a test that creates a simple product. Note the hardcoded selectors, data values and lack of action groups. We will focus on the "product name".

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->

<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="CreateSimpleProductTest">
        <annotations>
            <features value="Catalog"/>
            <stories value="Create Product"/>
            <title value="Admin should be able to create simple product."/>
            <description value="Admin should be able to create simple product."/>
            <severity value="MAJOR"/>
            <group value="Catalog"/>
            <group value="alex" />
        </annotations>
        <before>
            <!-- Login to Admin panel -->
            <amOnPage url="admin" stepKey="openAdminPanelPage" />
            <fillField selector="#username" userInput="admin" stepKey="fillLoginField" />
            <fillField selector="#login" userInput="123123q" stepKey="fillPasswordField" />
            <click selector="#login-form .action-login" stepKey="clickLoginButton" />
        </before>
        <after>
            <!-- Logout from Admin panel -->
        </after>

        <!-- Navigate to Catalog -> Products page (or just open by link) -->
        <amOnPage url="admin/catalog/product/index" stepKey="openProductGridPage" />

        <!-- Click "Add Product" button -->
        <click selector="#add_new_product-button" stepKey="clickAddProductButton" />
        <waitForPageLoad stepKey="waitForNewProductPageOpened" />

        <!-- Fill field "Name" with "Simple Product %unique_value%" -->
  -----><fillField selector="input[name='product[name]']" userInput="Simple Product 12412431" stepKey="fillNameField" />

        <!-- Fill field "SKU" with "simple_product_%unique_value%" -->
        <fillField selector="input[name='product[sku]']" userInput="simple-product-12412431" stepKey="fillSKUField" />

        <!-- Fill field "Price" with "500.00" -->
        <fillField selector="input[name='product[price]']" userInput="500.00" stepKey="fillPriceField" />

        <!-- Fill field "Quantity" with "100" -->
        <fillField selector="input[name='product[quantity_and_stock_status][qty]']" userInput="100" stepKey="fillQtyField" />

        <!-- Fill field "Weight" with "100" -->
        <fillField selector="input[name='product[weight]']" userInput="100" stepKey="fillWeightField" />

        ...
    </test>
</tests>
```

## Extract the CSS selectors

First we will extract the hardcoded CSS selector values into variables.
For instance: `input[name='product[name]']` becomes `{{AdminProductFormSection.productName}}`.
In this example `AdminProductFormSection` refers to the `<section>` in the XML file which contains an `<element>` node named `productName`. This element contains the value of the selector that was previously hardcoded: `input[name='product[name]']`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->

<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="CreateSimpleProductTest">
        <annotations>
            <features value="Catalog"/>
            <stories value="Create Product"/>
            <title value="Admin should be able to create simple product."/>
            <description value="Admin should be able to create simple product."/>
            <severity value="MAJOR"/>
            <group value="Catalog"/>
            <group value="alex" />
        </annotations>
        <before>
        <before>
            <!-- Login to Admin panel -->
            <amOnPage url="admin" stepKey="openAdminPanelPage" />
            <fillField selector="#username" userInput="admin" stepKey="fillLoginField" />
            <fillField selector="#login" userInput="123123q" stepKey="fillPasswordField" />
            <click selector="#login-form .action-login" stepKey="clickLoginButton" />
        </before>
        <after>
            <!-- Logout from Admin panel -->
        </after>

        <!-- Navigate to Catalog -> Products page (or just open by link) -->
        <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="openProductGridPage" />

        <!-- Click "Add Product" button -->
        <click selector="{{AdminProductGridActionSection.addProductBtn}}" stepKey="clickAddProductButton" />
        <waitForPageLoad stepKey="waitForNewProductPageOpened" />

        <!-- Fill field "Name" with "Simple Product %unique_value%" -->
  -----><fillField selector="{{AdminProductFormSection.productName}}" userInput="Simple Product 12412431" stepKey="fillNameField" />
        <!-- Fill field "SKU" with "simple_product_%unique_value%" -->
        <fillField selector="{{AdminProductFormSection.productSku}}" userInput="simple-product-12412431" stepKey="fillSKUField" />

        <!-- Fill field "Price" with "500.00" -->
        <fillField selector="{{AdminProductFormSection.productPrice}}" userInput="500.00" stepKey="fillPriceField" />

        <!-- Fill field "Quantity" with "100" -->
        <fillField selector="{{AdminProductFormSection.productQuantity}}" userInput="100" stepKey="fillQtyField" />

        <!-- Fill field "Weight" with "100" -->
        <fillField selector="{{AdminProductFormSection.productWeight}}" userInput="100" stepKey="fillWeightField" />
        ...
    </test>
</tests>
```

## The section file

We abstract these selector values to a file named `AdminProductFormSection.xml` which is kept in the `Section` folder.
Within this file, there can be multiple `<section>` nodes which contains data for different parts of the test.
Here we are interested in `<section name="AdminProductFormSection">`, where we are keeping our extracted values from above.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->
<sections xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:mftf:Page/etc/SectionObject.xsd">
--> <section name="AdminProductFormSection">
        <element name="attributeSet" type="select" selector="div[data-index='attribute_set_id'] .admin__field-control"/>
        <element name="attributeSetFilter" type="input" selector="div[data-index='attribute_set_id'] .admin__field-control input" timeout="30"/>
        <element name="attributeSetFilterResult" type="input" selector="div[data-index='attribute_set_id'] .action-menu-item._last" timeout="30"/>
        <element name="attributeSetFilterResultByName" type="text" selector="//label/span[text() = '{{var}}']" timeout="30" parameterized="true"/>
  -----><element name="productName" type="input" selector="input[name='product[name]']"/>
        <element name="RequiredNameIndicator" type="text" selector=" return window.getComputedStyle(document.querySelector('._required[data-index=name]&gt;.admin__field-label span'), ':after').getPropertyValue('content');"/>
        <element name="RequiredSkuIndicator" type="text" selector=" return window.getComputedStyle(document.querySelector('._required[data-index=sku]&gt;.admin__field-label span'), ':after').getPropertyValue('content');"/>
        <element name="productSku" type="input" selector="input[name='product[sku]']"/>
        <element name="enableProductAttributeLabel" type="text" selector="//span[text()='Enable Product']/parent::label"/>
        <element name="enableProductAttributeLabelWrapper" type="text" selector="//span[text()='Enable Product']/parent::label/parent::div"/>
        <element name="productStatus" type="checkbox" selector="input[name='product[status]']"/>
       ...
    </section>
    <section name="ProductInWebsitesSection">
        <element name="sectionHeader" type="button" selector="div[data-index='websites']" timeout="30"/>
        <element name="website" type="checkbox" selector="//label[contains(text(), '{{var1}}')]/parent::div//input[@type='checkbox']" parameterized="true"/>
    </section>
```

## Data entities

The hardcoded values of these form elements are abstracted to a "data entity" XML file.
We replace the hardcoded values with variables and the MFTF will do the variable substitution.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->

<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="CreateSimpleProductTest">
        <annotations>
            <features value="Catalog"/>
            <stories value="Create Product"/>
            <title value="Admin should be able to create simple product."/>
            <description value="Admin should be able to create simple product."/>
            <severity value="MAJOR"/>
            <group value="Catalog"/>
            <group value="alex" />
        </annotations>
        <before>
            <!-- Login to Admin panel -->
            <amOnPage url="admin" stepKey="openAdminPanelPage" />
            <fillField selector="#username" userInput="admin" stepKey="fillLoginField" />
            <fillField selector="#login" userInput="123123q" stepKey="fillPasswordField" />
            <click selector="#login-form .action-login" stepKey="clickLoginButton" />
        </before>
        <after>
            <!-- Logout from Admin panel -->
        </after>

        <!-- Navigate to Catalog -> Products page (or just open by link) -->
        <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="openProductGridPage" />

        <!-- Click "Add Product" button -->
        <click selector="{{AdminProductGridActionSection.addProductBtn}}" stepKey="clickAddProductButton" />
        <waitForPageLoad stepKey="waitForNewProductPageOpened" />

        <!-- Fill field "Name" with "Simple Product %unique_value%" -->
  ----><fillField selector="{{AdminProductFormSection.productName}}" userInput="{{_defaultProduct.name}}" stepKey="fillNameField" />

        <!-- Fill field "SKU" with "simple_product_%unique_value%" -->
        <fillField selector="{{AdminProductFormSection.productSku}}" userInput="{{_defaultProduct.sku}}" stepKey="fillSKUField" />

        <!-- Fill field "Price" with "500.00" -->
        <fillField selector="{{AdminProductFormSection.productPrice}}" userInput="{{_defaultProduct.price}}" stepKey="fillPriceField" />

        <!-- Fill field "Quantity" with "100" -->
        <fillField selector="{{AdminProductFormSection.productQuantity}}" userInput="{{_defaultProduct.quantity}}" stepKey="fillQtyField" />

        <!-- Fill field "Weight" with "100" -->
        <fillField selector="{{AdminProductFormSection.productWeight}}" userInput="{{_defaultProduct.weight}}" stepKey="fillWeightField" />

       ...
    </test>
</tests>
```

One of the reasons that we abstract things is so that they are more flexible and reusable. In this case, we can leverage this flexibility and use an existing data file. For this scenario, we are using [this file](https://raw.githubusercontent.com/magento-pangolin/magento2/MageTestFest/app/code/Magento/Catalog/Test/Mftf/Data/ProductData.xml).

Data entities are important because this is where a `suffix` or `prefix` can be defined. This ensures that data values can be unique for every test run.

Notice that the `<entity>` name is `_defaultProduct` as referenced above. Within this entity is the `name` value.

```xml
<entities xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd">
    <entity name="_defaultProduct" type="product">
        <data key="sku" unique="suffix">testSku</data>
        <data key="type_id">simple</data>
        <data key="attribute_set_id">4</data>
        <data key="visibility">4</data>
   ---> <data key="name" unique="suffix">testProductName</data>
        <data key="price">123.00</data>
        <data key="urlKey" unique="suffix">testurlkey</data>
        <data key="status">1</data>
        <data key="quantity">100</data>
        <data key="weight">1</data>
        <requiredEntity type="product_extension_attribute">EavStockItem</requiredEntity>
        <requiredEntity type="custom_attribute_array">CustomAttributeCategoryIds</requiredEntity>
    </entity>
```

The `unique="suffix"` attribute appends a random numeric string to the end of the actual data string. This ensures that unique values are used for each test run.
See  [Input testing data][] for more information.

## Convert actions to action groups

Action groups are sets of steps that are run together. Action groups are designed to break up multiple individual steps into logical groups. For example: logging into the admin panel requires ensuring the login form exists, filling in two form fields and clicking the **Submit** button. These can be bundled into a single, reusable "LoginAsAdmin" action group that can be applied to any other test. This leverages existing code and prevents duplication of effort. We recommend that all steps in a test be within an action group.

Using action groups can be very useful when testing extensions.
Extending the example above, assume the first extension adds a new field to the admin log in, a Captcha for example.
The second extension we are testing needs to log in AND get past the Captcha.

1. The admin login is encapsulated in an action group.
2. The Captcha extension properly extends the `LoginAsAdmin` capture group using the `merge` functionality.
3. Now the second extension can call the `LoginAsAdmin` action group and because of the `merge`, it will automatically include the Captcha field.

In this case, the action group is both reusable and extensible!

We further abstract the test by putting these action groups in their own file: ['app/code/Magento/Catalog/Test/Mftf/ActionGroup/AdminProductActionGroup.xml'](https://raw.githubusercontent.com/magento-pangolin/magento2/e5671d84aa63cad772fbba757005b3d89ddb79d9/app/code/Magento/Catalog/Test/Mftf/ActionGroup/AdminProductActionGroup.xml)

To create an action group, take the steps and put them within an `<actionGroup>` element. Note that the `<argument>` node defines the `_defaultProduct` data entity that is required for the action group.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->
<actionGroups xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/actionGroupSchema.xsd">
<!--Fill main fields in create product form-->
    <actionGroup name="fillMainProductForm">
        <arguments>
            <argument name="product" defaultValue="_defaultProduct"/>
        </arguments>
  -----><fillField selector="{{AdminProductFormSection.productName}}" userInput="{{product.name}}" stepKey="fillProductName"/>
        <fillField selector="{{AdminProductFormSection.productSku}}" userInput="{{product.sku}}" stepKey="fillProductSku"/>
        <fillField selector="{{AdminProductFormSection.productPrice}}" userInput="{{product.price}}" stepKey="fillProductPrice"/>
        <fillField selector="{{AdminProductFormSection.productQuantity}}" userInput="{{product.quantity}}" stepKey="fillProductQty"/>
        <selectOption selector="{{AdminProductFormSection.productStockStatus}}" userInput="{{product.status}}" stepKey="selectStockStatus"/>
        <selectOption selector="{{AdminProductFormSection.productWeightSelect}}" userInput="This item has weight" stepKey="selectWeight"/>
        <fillField selector="{{AdminProductFormSection.productWeight}}" userInput="{{product.weight}}" stepKey="fillProductWeight"/>
        <click selector="{{AdminProductSEOSection.sectionHeader}}" stepKey="openSeoSection"/>
        <fillField userInput="{{product.urlKey}}" selector="{{AdminProductSEOSection.urlKeyInput}}" stepKey="fillUrlKey"/>
    </actionGroup>
```

Note how the `<argument>` node takes in the `_defaultProduct` data entity and renames it to `product`, which is then used for the `userInput` values.

Now we can reference this action group within our test (and any other test).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */
-->

<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="CreateSimpleProductTest">
        <annotations>
            <features value="Catalog"/>
            <stories value="Create Product"/>
            <title value="Admin should be able to create simple product."/>
            <description value="Admin should be able to create simple product."/>
            <severity value="MAJOR"/>
            <group value="Catalog"/>
            <group value="alex" />
        </annotations>
        <before>
            <!-- Login to Admin panel -->
            <actionGroup ref="LoginAsAdmin" stepKey="loginToAdminPanel" />
        </before>
        <after>
            <!-- Logout from Admin panel -->
        </after>

        <!-- Navigate to Catalog -> Products page (or just open by link) -->
        <amOnPage url="{{AdminProductIndexPage.url}}" stepKey="openProductGridPage" />
        <waitForPageLoad stepKey="waitForProductGridPageLoaded" />

        <!-- Click "Add Product" button -->
        <actionGroup ref="goToCreateProductPage" stepKey="goToCreateProductPage" />
  -----><fillField selector="{{AdminProductFormSection.productName}}" userInput="{{product.name}}" stepKey="fillProductName"/>
        <actionGroup ref="fillMainProductForm" stepKey="fillProductForm">
            <argument name="product" value="_defaultProduct" />
        </actionGroup>

        <!-- See success save message "You saved the product." -->
        <actionGroup ref="saveProductForm" stepKey="clickSaveOnProductForm" />

        <actionGroup ref="AssertProductInGridActionGroup" stepKey="assertProductInGrid" />

        <!-- Open Storefront Product Page and verify "Name", "SKU", "Price" -->
        <actionGroup ref="AssertProductInStorefrontProductPage" stepKey="assertProductInStorefrontProductPage">
            <argument name="product" value="_defaultProduct" />
        </actionGroup>
    </test>
</tests>
```

A well written test will end up being a set of action groups.
The finished test is fully abstracted in such a way that it is short and readable and importantly, the abstracted data and action groups can be used again.

<!-- Link Definitions -->
[actions]: https://devdocs.magento.com/mftf/docs/test/actions.html
[action groups]: https://devdocs.magento.com/mftf/docs/test/action-groups.html
[Data entities]: https://devdocs.magento.com/mftf/docs/data.html
[Input testing data]: https://devdocs.magento.com/mftf/docs/data.html
[parameterized selectors]: https://devdocs.magento.com/mftf/docs/section/parameterized-selectors.html
