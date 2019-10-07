# Test Isolation

Because MFTF is a framework for testing a highly customizable and ever changing application, MFTF tests need to be properly isolated.

## What is test isolation?

Test isolation refers to a test that does not leave behind any data or configuration changes in the Magento instance.

An MFTF test is considered fully isolated if:

1. It does not leave data behind.
1. It does not leave Magento configured in a different state than when the test started.
1. It does not affect a following test's outcome.
1. It does not rely on an irregular configuration to start its preconditions.

### Deleting versus restoring

In the above list, points 1 and 2 refer to leaving things behind during test execution. This means you are either deleting or restoring entities in Magento after your test's execution.

Some examples of entities to be deleted include:

1. Products
2. Categories
3. Rules (Price, Related Products)

The list of entities to restore is much simpler:

1. Application Configuration

The distinction above is because MFTF tests expect the environment to be in a completely clean state, outside of a test or suite's preconditions. Data must be cleaned up and any application configuration must go back to the default.

## Why is isolation important?

As mentioned above, isolation is important because poor isolation can lead to other test failures. For a test to be useful, you must have high confidence in the test's outcome, and by introducing test isolation issues it can invalidate a test's result.

## How can I achieve test isolation?

This is difficult to do given how large the Magento application is, but a systematic approach can ensure a high level of confidence in you test's isolation.

### Cleaning up data

If your test creates any data via `<createData>` then a subsequent `<deleteData>` action *must* exist in the test's `<after>` block.

This includes both `<createData>` actions in the test's `<before>` as well as in the test body.

```xml
<test name="SampleTest">
    <before>
        <createData entity="SimpleSubCategory" stepKey="category"/>
    </before>
    <after>
        <deleteData createDataKey="category" stepKey="deleteCategory"/>
        <deleteData createDataKey="entityCreatedDuringWorkflow" stepKey="deleteCategory"/>
    </after>
    ...
    <createData entity="SimpleSubCategory" stepKey="entityCreatedDuringWorkflow"/>
    ...
</test>
```

Other test data can be more difficult to detect, and requires an understanding of what the test does in its workflow.

```xml
<test name="AdminAddImageForCategoryTest">
    <before>
        <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin"/>
    </before>
    <after>
        <actionGroup ref="DeleteCategory" stepKey="DeleteCategory">
            <argument name="categoryEntity" value="SimpleSubCategory"/>
        </actionGroup>
        <actionGroup ref="logout" stepKey="logout"/>
    </after>
    <!-- Go to create a new category with image -->
    <actionGroup ref="goToCreateCategoryPage" stepKey="goToCreateCategoryPage"/>
    ...
</test>
```

Note that the test contains a context setting comment describing the workflow; this is very helpful in determining that a new category will be created, which will need to be cleaned up in the test `<after>` block.

### Cleaning up configuration

Similarly, configuration changes can be easily identified by `<magentoCLI>` actions.

```xml
<test name="AddOutOfStockProductToCompareListTest">
    <before>
        <magentoCLI command="config:set cataloginventory/options/show_out_of_stock 0" stepKey="displayOutOfStockNo"/>
        ...
    </before>
    <after>
        <magentoCLI command="config:set cataloginventory/options/show_out_of_stock 1" stepKey="displayOutOfStockNo"/>
        ...
    </after>
    ...
</test>
```

Configuration changes can also be done via `<createData>` actions, but that is not recommended as it is much easier to identify `<magentoCLI>` commands.

A test's workflow can also alter the application's configuration, and much like data cleanup, this can only be identified by understanding a test's workflow:

```xml
<test name="AdminMoveProductBetweenCategoriesTest">
    ...
    <!-- Enable `Use Categories Path for Product URLs` on Stores -> Configuration -> Catalog -> Catalog -> Search Engine Optimization -->
    <amOnPage url="{{AdminCatalogSearchConfigurationPage.url}}" stepKey="onConfigPage"/>
    <waitForPageLoad stepKey="waitForLoading"/>
    <conditionalClick selector="{{AdminCatalogSearchEngineConfigurationSection.searchEngineOptimization}}" dependentSelector="{{AdminCatalogSearchEngineConfigurationSection.openedEngineOptimization}}" visible="false" stepKey="clickEngineOptimization"/>
    <uncheckOption selector="{{AdminCatalogSearchEngineConfigurationSection.systemValueUseCategoriesPath}}" stepKey="uncheckDefault"/>
    <selectOption userInput="Yes" selector="{{AdminCatalogSearchEngineConfigurationSection.selectUseCategoriesPatForProductUrls}}" stepKey="selectYes"/>
    <click selector="{{AdminConfigSection.saveButton}}" stepKey="saveConfig"/>
    <waitForPageLoad stepKey="waitForSaving"/>
    ...
</test>
```

One thing to note, unless a test is specifically testing the configuration page's frontend capabilities, configuring the application should always be done with a `<magentoCLI>` action.
