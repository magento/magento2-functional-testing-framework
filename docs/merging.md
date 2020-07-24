# Merging

MFTF allows you to merge test components defined in XML files, such as:

-  [`<tests>`][]
-  [`<pages>`][]
-  [`<sections>`][]
-  [`<data>`][]
-  [`<action groups>`][]

You can create, delete, or update the component.
It is useful for supporting rapid test creation for extensions and customizations.

You can specify needed changes to an existing file and merge them to produce a modification of the original that incorporates the specified changes (the "delta").

Merging operates at the XML tag level, triggered by our parser when there are two (or more) entities with the same name.
Your update (XML node with changes) must have the same attribute `name` as its base node (the target object to be changed).

For example:

-  All tests with `<test name="SampleTest>` will be merged into one.
-  All pages with `<page name="SamplePage>` will be merged into one.
-  All sections with `<section name="SampleSection">` will be merged into one.
-  All data entities with `<entity name="sampleData" type="sample">` will be merged into one.
-  All action groups with `<actionGroup name="SelectNotLoggedInCustomerGroupActionGroup">` will be merged into one.

Although a file name does not influence merging, we recommend using the same file names in merging updates.
This makes it easier to search later on.

## Merging precedence

**Magento Functional Testing Framework** uses Module's `<sequence>` to merge all XML configurations into Codeception instructions. If there's no Sequence specified, MFTF would use:

1. Vendor modules (Magento & Vendors) located in `vendor/`
1. Tests located in `app/code/*/*/Test/Mftf`

![Usual precedence for merging MFTF Tests and resources][mftfExtendingPrecedence image]

## Add a test

You cannot add another [`<test>`][tests] using merging functionality.
To add a `<test>`, create a new `*Test.xml` file.

## Remove a test

Tests cannot be removed while merging.
If a [`<test>`][tests] must be skipped due to a module completely invalidating a function, you can use `skip` annotation to skip the test.

Learn more about running tests with different options using [`mftf`] or [`codecept`] commands.

### Example

Skip the `AdminLoginSuccessfulTest` test in the `.../Backend/Test/AdminLoginSuccessfulTest.xml` file while merging with the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

<!-- {% raw %} -->

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <annotations>
            <features value="Backend"/>
            <stories value="Login on the Admin Login page"/>
            <title value="Admin should be able to log into the Magento Admin backend successfully"/>
            <description value="Admin should be able to log into the Magento Admin backend successfully"/>
            <severity value="CRITICAL"/>
            <testCaseId value="MAGETWO-71572"/>
            <group value="example"/>
            <group value="login"/>
        </annotations>
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

Create the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
       <annotations>
          <skip>
             <issueId value="Issue#"/>
          </skip>
       </annotations>
    </test>
</tests>
```

The `AdminLoginSuccessfulTest` result corresponds to:

```xml
<test name="AdminLoginSuccessfulTest">
    <annotations>
        <features value="Backend"/>
        <stories value="Login on the Admin Login page"/>
        <title value="Admin should be able to log into the Magento Admin backend successfully"/>
        <description value="Admin should be able to log into the Magento Admin backend successfully"/>
        <severity value="CRITICAL"/>
        <testCaseId value="MAGETWO-71572"/>
        <group value="example"/>
        <group value="login"/>
        <skip>
            <issueId value="Issue#"/>
        </skip>
    </annotations>
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
    <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
    <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
</test>
```

## Update a test

Any change must include information about where it should be inserted relative to other test steps in the scope of the test.

This is done using the `before` or `after` element.
See the previous examples.

### Add a test step

**Use case**: Add `AdminCheckOptionSalesActionGroup` before `AssertAdminSuccessLoginActionGroup` (`stepKey="assertLoggedIn"`) and add `AdminAssertSalesOnDashboardActionGroup` after the `AssertAdminSuccessLoginActionGroup` in the `AdminLoginSuccessfulTest` test (in the `.../Backend/Test/AdminLoginSuccessfulTest.xml` file) while merging with the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

Create the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales" before="assertLoggedIn"/>
        <actionGroup ref="AdminAssertSalesOnDashboardActionGroup" stepKey="assertSalesOnDashboard" after="assertLoggedIn"/>
    </test>
</tests>
```

The `AdminLoginSuccessfulTest` result corresponds to:

```xml
<test name="AdminLoginSuccessfulTest">
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
    <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales" before="assertLoggedIn"/>
    <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
    <actionGroup ref="AdminAssertSalesOnDashboardActionGroup" stepKey="assertSalesOnDashboard" after="assertLoggedIn"/>
    <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
</test>
```

### Remove a test step

**Use case**: Remove `AdminCheckOptionSalesActionGroup` (`stepKey="checkOptionSales"`) from the `LogInAsAdminTest` test (in the `.../Backend/Test/AdminLoginSuccessfulTest.xml` file) while merging with the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

Create the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <remove keyForRemoval="checkOptionSales"/>
    </test>
</tests>
```

The `AdminLoginSuccessfulTest` result corresponds to:

```xml
<test name="AdminLoginSuccessfulTest">
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
    <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
    <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
</test>
```

### Update a test step

**Use case**: Change argument in `AdminCheckOptionSalesActionGroup` (`stepKey="checkOptionSales"`) of the `AdminLoginSuccessfulTest` test (in the `.../Backend/Test/AdminLoginSuccessfulTest.xml` file) while merging with the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales">
            <argument name="salesOption" value="{{AdminSalesData.lifeTimeSales}}"/>
        </actionGroup>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

Create the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<test name="AdminLoginSuccessfulTest">
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
    <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales">
        <argument name="salesOption" value="{{AdminSalesData.annualSales}}"/>
    </actionGroup>
    <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
    <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
</test>
```

The `AdminLoginSuccessfulTest` result corresponds to:

```xml
<test name="AdminLoginSuccessfulTest">
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
    <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales">
        <argument name="salesOption" value="{{AdminSalesData.annualSales}}"/>
    </actionGroup>
    <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
    <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
</test>
```

### Add several test steps {#insert-after}

**Use case**: Add several action groups after the `AdminLoginActionGroup` (`stepKey="loginAsAdmin"`) in the `AdminLoginSuccessfulTest` test (in the `.../Backend/Test/AdminLoginSuccessfulTest.xml` file) while merging with the `.../Foo/Test/LogInAsAdminTest.xml` file:

```xml
<test name="AdminLoginSuccessfulTest">
    <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
</test>
```

Create the `.../Foo/Test/AdminLoginSuccessfulTest.xml` file:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest" insertAfter="loginAsAdmin">
        <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

The `AdminLoginSuccessfulTest` result corresponds to:

```xml
<tests ...>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AdminCheckOptionSalesActionGroup" stepKey="checkOptionSales"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

## Merge action groups

Merging action groups allows you to extend existing tests by reusing existing action groups, while customizing them for your specific needs.

### Use case

Here is an action group for selecting `customerGroup` in the `Cart Price Rules` section.
The controls change drastically in the B2B version, so it was abstracted to an action group so that it may be easily changed if B2B is enabled.

> Action group for selecting `customerGroup` in the `Cart Price Rules` section:

```xml
<actionGroup name="SelectNotLoggedInCustomerGroupActionGroup">
    <selectOption selector="{{AdminCartPriceRulesFormSection.customerGroups}}" userInput="NOT LOGGED IN" stepKey="selectCustomerGroup"/>
</actionGroup>
```

> B2B Merge file

```xml
<!-- name matches -->
<actionGroup name="SelectNotLoggedInCustomerGroupActionGroup">
    <!-- removes the original action -->
    <remove keyForRemoval="selectCustomerGroup"/>
    <!-- adds in sequence of actions to be performed instead-->
    <click selector="{{AdminCartPriceRulesFormSection.customerGroups}}" stepKey="expandCustomerGroups"/>
    <fillField selector="{{AdminCartPriceRulesFormSection.customerGroupsInput}}" userInput="NOT LOGGED IN" stepKey="fillCustomerGroups"/>
    <click selector="{{AdminCartPriceRulesFormSection.customerGroupsFirstResult}}" stepKey="selectNotLoggedInGroup"/>
    <click selector="{{AdminCartPriceRulesFormSection.customerGroupsDoneBtn}}" stepKey="closeMultiSelect"/>
</actionGroup>
```

## Merge pages

Use [page] merging to add or remove [sections] in your module.

To merge [pages][page], the `page name` must be the same as in the base module.
`page name` is set in the `module` attribute.

### Add a section

**Use case**: The `FooBackend` module extends the `Backend` module and requires use of two new sections that must be covered with tests.
Add `AdminBaseBackendSection` and `AdminAnotherBackendSection` to the `AdminBaseBackendPage` (`.../Backend/Page/AdminBaseBackendPage.xml` file):

```xml
<pages ...>
    <page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">
        <section name="AdminBaseBackendSection"/>
        <section name="AdminAnotherBackendSection"/>
    </page>
</pages>
```

Create the `.../FooBackend/Page/AdminBaseBackendPage.xml` file:

```xml
<pages ...>
   <page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">
      <section name="AdminNewExtensionSection"/>
   </page>
</pages>
```

The `AdminBaseBackendPage` result corresponds to:

```xml
<page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">

    <section name="AdminBaseBackendSection"/>
    <section name="AdminAnotherBackendSection"/>
    <section name="AdminNewExtensionSection"/>
</page>
```

### Remove a section

**Use case**: The `FooBackend` module extends the `Backend` module and requires deletion of the `AdminAnotherBackendSection` section (the `.../Backend/Page/AdminBaseBackendPage.xml` file):

```xml
<page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">
    <section name="AdminBaseBackendSection"/>
    <section name="AdminAnotherBackendSection"/>
</page>
```

Create the `.../FooBackend/Page/AdminBaseBackendPage.xml` file:

```xml
<page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">
   <section name="AdminAnotherBackendSection" remove="true"/>
</page>
```

The `AdminBaseBackendPage` result corresponds to:

```xml
<page name="AdminBaseBackendPage" url="admin" area="admin" module="Magento_Backend">
   <section name="AdminBaseBackendSection"/>
</page>
```

## Merge sections

Use merging to add, remove, or update [elements] in sections.

All sections with the same _file name_, _section name_, and _element name_ are merged during test generation.

### Add an element

**Use case**: The `FooBackend` module extends the `Backend` module and requires a new `mergeElement` element in the `AdminLoginFormSection`.
Add `mergeElement` to the `AdminLoginFormSection`:

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="username" type="input" selector="#username"/>
      <element name="password" type="input" selector="#login"/>
      <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
   </section>
</sections>
```

Create the `.../FooBackend/Section/AdminLoginFormSection.xml` file:

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="mergeElement" type="input" selector="#selector"/>
   </section>
</sections>
```

The `AdminLoginFormSection` result corresponds to:

```xml
<section name="AdminLoginFormSection">
   <element name="username" type="input" selector="#username"/>
   <element name="password" type="input" selector="#login"/>
   <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
   <element name="mergeElement" type="input" selector="#selector"/>
</section>
```

### Remove an element

**Use case**: The `FooBackend` module extends the `Backend` module and requires deletion of `username` in the `AdminLoginFormSection`.
Remove `username` from the `AdminLoginFormSection`:

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="username" type="input" selector="#username"/>
      <element name="password" type="input" selector="#login"/>
      <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
   </section>
</sections>
```

Create the `.../FooBackend/Section/AdminLoginFormSection.xml` file:

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="username" type="input" remove="true"/>
   </section>
</sections>
```

The `AdminLoginFormSection` result corresponds to:

```xml
<section name="AdminLoginFormSection">
   <element name="password" type="input" selector="#login"/>
   <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
</section>
```

### Update an element

**Use case**: The `FooBackend` module extends the `Backend` module and requires change of a selector in `username` in the `AdminLoginFormSection`.
Update `username` in the `AdminLoginFormSection` (the `.../Backend/Section/AdminLoginFormSection.xml` file):

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="username" type="input" selector="#username"/>
      <element name="password" type="input" selector="#login"/>
      <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
   </section>
</sections>
```

Create the `.../FooBackend/Section/AdminLoginFormSection.xml` file:

```xml
<sections ...>
   <section name="AdminLoginFormSection">
      <element name="username" type="input" selector="#newSelector"/>
   </section>
</sections>
```

The `AdminLoginFormSection` result corresponds to:

```xml
<section name="AdminLoginFormSection">
   <element name="username" type="input" selector="#newSelector"/>
   <element name="password" type="input" selector="#login"/>
   <element name="signIn" type="button" selector=".actions .action-primary" timeout="30"/>
</section>
```

## Merge data

You can add or update `<data>` elements within an `<entity>`.
Removal of individual `<data>` tags is not supported.

Entities and data with the same _file name_, _entity name and type_, and _data key_ are merged during test generation.

### Add data

**Use case**: The `FooSample` module extends the `Sample` module and requires a new data item `thirdField` in the `_defaultSample` entity.
Add `<data key="thirdField">field3</data>` to the `_defaultSample` (the `.../Sample/Data/SampleData.xml` file):

```xml
<entities ...>
   <entity name="_defaultSample" type="testData">
      <data key="firstField">field1</data>
      <data key="secondField">field2</data>
   </entity>
</entities>
```

Create the `.../FooSample/Data/SampleData.xml` file:

```xml
<entities ...>
   <entity name="sampleData" type="testData">
      <data key="thirdField">field3</data>
   </entity>
</entities>
```

The `_defaultSample` result corresponds to:

```xml
<entity name="_defaultSample" type="testData">
   <data key="firstField">field1</data>
   <data key="secondField">field2</data>
   <data key="thirdField">field3</data>
</entity>
```

### Update data

**Use case**: The `FooSample` module extends the `Sample` module and requires changing the `firstField` data item in the `_defaultSample` entity.
Change `firstField` to `<data key="firstField">overrideField</data>` in the `_defaultSample` (the `.../Sample/Data/SampleData.xml` file):

```xml
<entities ...>
   <entity name="_defaultSample" type="testData">
      <data key="firstField">field1</data>
      <data key="secondField">field2</data>
   </entity>
</entity>
```

Create the `.../FooSample/Data/SampleData.xml` file:

```xml
<entities ...>
   <entity name="_defaultSample" type="testData">
      <data key="firstField">overrideField</data>
   </entity>
</entity>
```

The `_defaultSample` results corresponds to:

```xml
<entity name="_defaultSample" type="testData">
   <data key="firstField">overrideField</data>
   <data key="secondField">field2</data>
</entity>
```

<!-- {% endraw %} -->

<!-- Link definitions -->

[`codecept`]: ./commands/codeception.md
[`mftf`]: ./commands/mftf.md
[`<data>`]: ./data.md
[elements]: ./section.md#element-tag
[`<pages>`]: ./page.md
[`<sections>`]: ./section.md
[`<tests>`]: ./test.md
[`<action groups>`]: ./test/action-groups.md
[mftfExtendingPrecedence image]: img/mftf-extending-precedence.png
