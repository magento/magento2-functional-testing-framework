<style>
.mftf-dl {
  margin-bottom: 2.5em;
}
dl dt{
  font-weight:400;
}
</style>

# MFTF functional test modules and packaging

## MFTF predefined test module paths
The Magento Functional Testing Framework can run tests from predefined paths and custom paths. The predefined paths are:
```
app/code/<Vendor>/<Module>/Test/Mftf
dev/tests/acceptance/tests/functional/<Vendor>/<TestModule>
vendor/<Vendor>/<Module>/Test/Mftf
vendor/<Vendor>/<TestModule>
```

To support future service isolation, Test module in `dev/tests/acceptance/tests/functional/<Vendor>/<TestModule>` and 
`vendor/<Vendor>/<TestModule>` must define the module type as `magento2-functional-test-module` in its `composer.json` file. 
No `composer.json` file is required for tests in `app/code/<Vendor>/<Module>/Test/Mftf` and `vendor/<Vendor>/<Module>/Test/Mftf` 
as they are part of the Magento modules.

Test module for a specific Magento module can only be in one of the paths.

## Test module composer.json format

Test module `composer.json` file should use type `magento2-functional-test-module`.

Test module `composer.json` file should define Magento module dependencies in suggests block. 
MFTF will recognize the dependency if the suggest message of a module specifies `type` using `magento2-module` and `name` 
using module name registered with Magento.

Here is an example `composer.json` file for the test module `dev/tests/acceptance/tests/functional/Magento/ConfigurableProductCatalogSearch`:

```json
{
    "name": "magento/module-configurable-product-catalog-search-functional-test",
    "description": "MFTF test module for Magento_ConfigurableProduct and Magento_CatalogSearch",
    "type": "magento2-functional-test-module",
    "config": {
        "sort-packages": true
    },
    "require": {
        "magento/magento2-functional-testing-framework": ">=2.5"
    },
    "suggest": {
        "magento/module-configurable-product": "type: magento2-module, name: Magento_ConfigurableProduct, version: *",
        "magento/module-catalog-search": "type: magento2-module, name: Magento_CatalogSearch, version: *"
    },
    "license": [
        "OSL-3.0",
        "AFL-3.0"
    ]
}
```