# Extend data entities

Extending a data entity does not affect the existing data entity.

In this example we update the quantity to 1001 and add a new piece of data relevant to our extension. Unlike merging, this will _not_ affect any other tests that use this data entity.

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
