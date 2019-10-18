# Extend tests

Tests can be extended to cover the needs of your extension.

In this example, we add an action group to a new copy of the original test for our extension.

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

    <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin1"/>
    <actionGroup ref="FillAdminSimpleProductForm" stepKey="fillProductFieldsInAdmin">
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
<test name="AdminCreateSimpleProductExtensionTest" extends="AdminCreateSimpleProductTest">
    <!-- Since this is its own test you need the annotations block -->
    <annotations>
        <features value="Catalog"/>
        <stories value="Create a Simple Product via Admin"/> <!-- you should leave this the same since it is part of the same group -->
        <title value="Admin should be able to create a Simple Product with my extension"/>
        <description value="Admin should be able to create a Simple Product with my extension via the product grid"/>
        <severity value="CRITICAL"/>
        <testCaseId value="Extension/Github Issue Number"/>
        <group value="product"/>
    </annotations>
    <!-- This will be added after the step "fillProductFieldsInAdmin" on line 20 in the above test. -->
    <actionGroup ref="AddMyExtensionData" stepKey="extensionField" after="fillProductFieldsInAdmin">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>

    <!-- This will be added after the step "assertProductInStorefront2" on line 28 in the above test. -->
    <actionGroup ref="AssertMyExtensionDataExists" stepKey="assertExtensionInformation" after="assertProductInStorefront2">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>
</test>
```

## Resultant test

Note that there are now two tests below.

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

    <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin1"/>
    <actionGroup ref="FillAdminSimpleProductForm" stepKey="fillProductFieldsInAdmin">
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
<test name="AdminCreateSimpleProductExtensionTest">
    <annotations>
        <features value="Catalog"/>
        <stories value="Create a Simple Product via Admin"/>
        <title value="Admin should be able to create a Simple Product with my extension"/>
        <description value="Admin should be able to create a Simple Product with my extension via the product grid"/>
        <severity value="CRITICAL"/>
        <testCaseId value="Extension/Github Issue Number"/>
        <group value="product"/>
    </annotations>
    <before>
        <createData entity="_defaultCategory" stepKey="createPreReqCategory"/>
    </before>
    <after>
        <amOnPage url="admin/admin/auth/logout/" stepKey="amOnLogoutPage"/>
        <deleteData createDataKey="createPreReqCategory" stepKey="deletePreReqCategory"/>
    </after>

    <actionGroup ref="LoginAsAdmin" stepKey="loginAsAdmin1"/>
    <actionGroup ref="FillAdminSimpleProductForm" stepKey="fillProductFieldsInAdmin">
        <argument name="category" value="$$createPreReqCategory$$"/>
        <argument name="simpleProduct" value="_defaultProduct"/>
    </actionGroup>

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

    <actionGroup ref="AssertMyExtensionDataExists" stepKey="assertExtensionInformation">
        <argument name="extensionData" value="_myData"/>
    </actionGroup>
</test>
```
