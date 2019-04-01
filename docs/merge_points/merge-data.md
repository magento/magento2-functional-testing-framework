# Merge data

Data objects can be merged to cover the needs of your extension.

In this example we update the `quantity` to `1001` and add a new piece of data relevant to our extension. This will affect all other tests that use this data.

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
<entity name="SimpleProduct" type="product">
    <!-- myExtensionData will simply be added to the product and quantity will be changed to 1001. -->
    <data key="quantity">1001</data>
    <data key="myExtensionData">dataHere</data>
</entity>
```

## Resultant entity

```xml
<entity name="SimpleProduct" type="product">
    <data key="sku" unique="suffix">SimpleProduct</data>
    <data key="type_id">simple</data>
    <data key="attribute_set_id">4</data>
    <data key="name" unique="suffix">SimpleProduct</data>
    <data key="price">123.00</data>
    <data key="visibility">4</data>
    <data key="status">1</data>
    <!-- Quantity updated -->
    <data key="quantity">1001</data>
    <data key="urlKey" unique="suffix">simpleproduct</data>
    <data key="weight">1</data>
    <requiredEntity type="product_extension_attribute">EavStockItem</requiredEntity>
    <requiredEntity type="custom_attribute_array">CustomAttributeCategoryIds</requiredEntity>
    <!-- Data key merged -->
    <data key="myExtensionData">dataHere</data>
</entity>
```