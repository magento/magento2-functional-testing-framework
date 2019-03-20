# Extend data entities

Extending an action group doesn't affect the existing action group.

In this example we add a `<click>` command to check the checkbox that our extension added with a new action group for the simple product creation form.

## Starting entity

```xml
<entity name="SimpleProduct" type="product">
    <data key="sku" unique="suffix">SimpleProduct</data>
    <data key="type_id">simple</data>
    <data key="attribute_set_id">4</data>
    <data key="name" unique="suffix">SimpleProduct</data>
    <data key="price">123.00</data>
    <data key="visibility">4</data>
    <data key="status">1</data>
    <data key="quantity">1000</data>
    <data key="urlKey" unique="suffix">simpleproduct</data>
    <data key="weight">1</data>
    <requiredEntity type="product_extension_attribute">EavStockItem</requiredEntity>
    <requiredEntity type="custom_attribute_array">CustomAttributeCategoryIds</requiredEntity>
</entity>
```

## File to merge

```xml
<entity name="ExtensionProduct" type="product" extends="SimpleProduct">
    <!-- myExtensionData will simply be added to the product and quantity will be changed to 1001. -->
    <data key="quantity">1001</data>
    <data key="myExtensionData">dataHere</data>
</entity>
```

## Resultant entity

Note that there are now two data entities below.

```xml
<entity name="SimpleProduct" type="product">
    <data key="sku" unique="suffix">SimpleProduct</data>
    <data key="type_id">simple</data>
    <data key="attribute_set_id">4</data>
    <data key="name" unique="suffix">SimpleProduct</data>
    <data key="price">123.00</data>
    <data key="visibility">4</data>
    <data key="status">1</data>
    <data key="quantity">1000</data>
    <data key="urlKey" unique="suffix">simpleproduct</data>
    <data key="weight">1</data>
    <requiredEntity type="product_extension_attribute">EavStockItem</requiredEntity>
    <requiredEntity type="custom_attribute_array">CustomAttributeCategoryIds</requiredEntity>
</entity>
<entity name="ExtensionProduct" type="product">
    <data key="sku" unique="suffix">SimpleProduct</data>
    <data key="type_id">simple</data>
    <data key="attribute_set_id">4</data>
    <data key="name" unique="suffix">SimpleProduct</data>
    <data key="price">123.00</data>
    <data key="visibility">4</data>
    <data key="status">1</data>
    <data key="quantity">1001</data>
    <data key="urlKey" unique="suffix">simpleproduct</data>
    <data key="weight">1</data>
    <requiredEntity type="product_extension_attribute">EavStockItem</requiredEntity>
    <requiredEntity type="custom_attribute_array">CustomAttributeCategoryIds</requiredEntity>
    <data key="myExtensionData">dataHere</data>
</entity>
```