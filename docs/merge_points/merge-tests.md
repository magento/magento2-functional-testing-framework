# Merge tests

Tests can be merged to create a new test that covers new extension capabilities.

In this example we add an action group that modifies the original test to interact with our extension sending in data we created.

## Starting test

```xml
<test name="AdminCreateSimpleProductTest">
    <annotations>
        <features value="Catalog"/>
        <stories value="Create a Simple Product via Admin"/>
        <title value="Admin should be able to create a Simple Product"/>
        <description value="Admin should be able to create a Simple Product"/>
        <severity value="CRITICAL"/>
        <testCaseId value="MAGETWO-23414"/>
        <group value="product"/>
    </annotations>
    <before>
        <createData entity="_defaultCategory" stepKey="createPreReqCategory"/>
    </before>
    <after>
        <amOnPage url="admin/admin/auth/logout/" stepKey="amOnLogoutPage"/>
        <deleteData createDataKey="createPreReqCategory" stepKey="deletePreReqCategory"/>
    </after>

    <actionGroup ref="AdminLoginActionGroup" stepKey="adminLoginActionGroup1"/>
    <actionGroup ref="AdminFillSimpleProductFormActionGroup" stepKey="fillProductFieldsInAdmin">
        <argument name="category" value="$$createPreReqCategory$$"/>
        <argument name="simpleProduct" value="_defaultProduct"/>
    </actionGroup>
    <actionGroup ref="AssertProductInStorefrontCategoryPage" stepKey="assertProductInStorefront1">
        <argument name="category" value="$$createPreReqCategory$$"/>
        <argument name="product" value="_defaultProduct"/>
    </actionGroup>
    <actionGroup ref="AssertProductInStorefrontProductPage" stepKey="assertProductInStorefront2">
        <argument name="product" value="_defaultProduct"/>
    </actionGroup>
</test>
```

## File to merge

```xml
<test name="AdminCreateSimpleProductTest">
    <!-- This will be added after the step "fillProductFieldsInAdmin" in the above test. -->
    <actionGroup ref="AddMyExtensionData" stepKey="extensionField" after="fillProductFieldsInAdmin">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>

    <!-- This will be added after the step "assertProductInStorefront2" in the above test. -->
    <actionGroup ref="AssertMyExtensionDataExists" stepKey="assertExtensionInformation" after="assertProductInStorefront2">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>
</test>
```

## Resultant test

```xml
<test name="AdminCreateSimpleProductTest">
    <annotations>
        <features value="Catalog"/>
        <stories value="Create a Simple Product via Admin"/>
        <title value="Admin should be able to create a Simple Product"/>
        <description value="Admin should be able to create a Simple Product"/>
        <severity value="CRITICAL"/>
        <testCaseId value="MAGETWO-23414"/>
        <group value="product"/>
    </annotations>
    <before>
        <createData entity="_defaultCategory" stepKey="createPreReqCategory"/>
    </before>
    <after>
        <amOnPage url="admin/admin/auth/logout/" stepKey="amOnLogoutPage"/>
        <deleteData createDataKey="createPreReqCategory" stepKey="deletePreReqCategory"/>
    </after>

    <actionGroup ref="AdminLoginActionGroup" stepKey="AdminLoginActionGroup1"/>
    <actionGroup ref="AdminFillSimpleProductFormActionGroup" stepKey="fillProductFieldsInAdmin">
        <argument name="category" value="$$createPreReqCategory$$"/>
        <argument name="simpleProduct" value="_defaultProduct"/>
    </actionGroup>
    <!-- First merged action group -->
    <actionGroup ref="AddMyExtensionData" stepKey="extensionField">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>

    <actionGroup ref="AssertProductInStorefrontCategoryPage" stepKey="assertProductInStorefront1">
        <argument name="category" value="$$createPreReqCategory$$"/>
        <argument name="product" value="_defaultProduct"/>
    </actionGroup>
    <actionGroup ref="AssertProductInStorefrontProductPage" stepKey="assertProductInStorefront2">
        <argument name="product" value="_defaultProduct"/>
    </actionGroup>
    <!-- Second merged action group -->
    <actionGroup ref="AssertMyExtensionDataExists" stepKey="assertExtensionInformation">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>
</test>
```