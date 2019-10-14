# Test Modularity

One of MFTF's most distinguishing functionalities is the framework's modularity.

## What is test modularity

Within MFTF, test modularity can refer to two different concepts:

### Test material merging

Test material merging is covered extensively in the [merging] topic, so it will not be our focus in this guide.

### Modular test materials

This refers to test materials being correctly owned by the right Magento module, and for tests to have references to only what their parent Magento module has a dependency on.
 
Since MFTF queries the Magento instance for enabled modules, MFTF test materials are included or excluded from the merging process dynamically, making proper ownership and dependencies a must.

Consider the following scenario:

* TestA in ModuleA is using materials form ModuleB
* In Magento, I now disable ModuleB
* TestA will try to use ModuleB materials, which are no longer being read by MFTF since the Magento instance has it disable

Since TestA's dependencies are out of sync with ModuleA, the tests are no longer properly modular.

## Why is test modularity important?

This concept is important simply because without proper modularity, tests or test materials may be incorrectly merged in (or left out), leading to the the test itself being out of sync with the Magento instance.

For example, in a situation where an extension drastically alters the login process (for instance: two factor authentication), the only way the tests will be able to pass is if the test materials are correctly nested in the extension.

## How can I achieve test modularity?

Test modularity can be challenging, depending on the breadth of the changes being introduced in a module.

### Determine test material ownership

This is should be the first step when creating new test materials. We will use the `New Product` page as an example.

#### Intuitive reasoning

The easiest way to do this has limited application, but some times it is fairly obvious where test material comes from due to nomenclature or functionality.

The following `<select>` for `Tax Class` clearly belongs to the `Tax` module:

```xml
<select class="admin__control-select" name="product[tax_class_id]"/>
```

This approach will work on getting the quickest ownership, but it is fairly obvious that it may be necessary to double check.

#### Deduction 

This is the next step up in difficulty from the above method, as it involves searching through the Magento codebase.

Take the `Add Attribute` button for example. The button has an `id="addAttribute"` and since we know Magento uses XML to declare much of its layout/CSS properties we can start by searching only `*.xml` files.

Searching through the codebase for `"addAttribute"` in `xml` files leads to four different files:

```terminal
app/code/Magento/Customer/Test/Mftf/Section/AdminCustomerActivitiesConfigureSection.xml
app/code/Magento/GiftRegistry/Test/Mftf/Section/AdminGiftRegistrySection.xml
app/code/Magento/Catalog/Test/Mftf/ActionGroup/AdminProductAttributeActionGroup.xml
app/code/Magento/Catalog/view/adminhtml/ui_component/product_form.xml
```

The first three are clearly MFTF test materials, which leaves us with the final file, and the line below

```xml
<button name="addAttribute" class="Magento\Catalog\Block\Adminhtml\Product\Edit\Button\AddAttribute"/>
```

This means we can safely assume `Add Attribute` button belongs to `Catalog` based on the above class namespace and filepath.

This kind of deduction is more involved, but it much more likely to give you the true source of the element.

### Use bin/mftf static-checks

For tests to be fully modular, an MFTF test must have the same dependencies as its parent module. This is quite difficult to do by hand, and requires checking of every `{{test.material}}` call and any other references to MFTF test materials in a test.

The `static-checks` command includes a test material ownership check that should help suss out these kind of dependency issues.

See [mftf commands] for more information.

<!-- Link definitions -->
[merging]: ../merging.md
[mftf commands]: ../commands/mftf.md
