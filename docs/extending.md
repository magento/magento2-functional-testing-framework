# Extending

There are cases when you need to create many tests that are very similar to each other.
For example, only one or two parameters (for example, URL) might vary between tests.
To avoid copy-pasting and to save some time the Magento Functional Testing Framework (MFTF) enables you to extend test components such as [test], [data], and [action group].
You can create or update any component of the parent body in your new test/action group/entity.

*  A test starting with `<test name="SampleTest" extends="ParentTest">` creates a test `SampleTest` that takes body of existing test `ParentTest` and adds to it the body of `SampleTest`.
*  An action group starting with `<actionGroup name="SampleActionGroup" extends="ParentActionGroup">` creates an action group based on the `ParentActionGroup`, but with the changes specified in `SampleActionGroup`.
*  An entity starting with `<entity name="SampleEntity" extends="ParentEntity">` creates an entity `SampleEntity` that is equivalent to merging the `SampleEntity` with the `ParentEntity`.

Specify needed variations for a parent object and produce a copy of the original that incorporates the specified changes (the "delta").

<div class="bs-callout bs-callout-info">
Unlike merging, the parent test (or action group) will still exist after the test generation.
</div>

## Extending tests

### Update a test step

<!-- {% raw %} -->

__Use case__: Create two similar tests with a different action group reference by overwriting a `stepKey`.

> Test with "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginAsOtherUserSuccessfulTest" extends="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginAsOtherUserActionGroup" stepKey="loginAsAdmin"/>
    </test>
</tests>
```

> Test without "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginAsOtherUserSuccessfulTest">
        <actionGroup ref="AdminLoginAsOtherUserActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

### Add a test step

__Use case__: Create two similar tests where the second test contains two additional steps specified to occur `before` or `after` other `stepKeys`.

> Tests with "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginCheckRememberMeSuccessfulTest" extends="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminCheckRememberMeActionGroup" stepKey="checkRememberMe" after="loginAsAdmin"/>
        <actionGroup ref="AssertAdminRememberMeActionGroup" stepKey="assertRememberMe" before="logoutFromAdmin"/>
    </test>
</tests>
```

> Tests without "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginCheckRememberMeSuccessfulTest">
        <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        <actionGroup ref="AdminCheckRememberMeActionGroup" stepKey="checkRememberMe"/>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AssertAdminRememberMeActionGroup" stepKey="assertRememberMe"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

### Update a test before hook

__Use case__: Create two similar tests where the second test contains an additional action in the `before` hook.

> Tests with "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <before>
            <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        </before>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginCheckRememberMeSuccessfulTest" extends="AdminLoginSuccessfulTest">
        <before>
            <actionGroup ref="AdminCheckRememberMeActionGroup" stepKey="checkRememberMe" after="loginAsAdmin"/>
        </before>
    </test>
</tests>
```

> Tests without "extends":

```xml
<tests>
    <test name="AdminLoginSuccessfulTest">
        <before>
            <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
        </before>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
    <test name="AdminLoginCheckRememberMeSuccessfulTest">
        <before>
            <actionGroup ref="AdminLoginActionGroup" stepKey="loginAsAdmin"/>
            <actionGroup ref="AdminCheckRememberMeActionGroup" stepKey="checkRememberMe"/>
        </before>
        <actionGroup ref="AssertAdminSuccessLoginActionGroup" stepKey="assertLoggedIn"/>
        <actionGroup ref="AdminLogoutActionGroup" stepKey="logoutFromAdmin"/>
    </test>
</tests>
```

## Extending action groups

Extend an [action group] to add or update [actions] in your module.

### Update an action

__Use case__: The `AssertAdminCountProductActionGroup` action group counts the particular product.
Modify the action group to use another product.

> Action groups with "extends":

```xml
<actionGroups>
    <actionGroup name="AssertAdminCountProductActionGroup">
        <arguments>
            <argument name="count" type="string"/>
        </arguments>
        <grabMultiple selector="selectorForProductA" stepKey="grabProducts"/>
        <assertCount stepKey="assertCount">
            <expectedResult type="int">{{count}}</expectedResult>
            <actualResult type="variable">grabProducts</actualResult>
        </assertCount>
    </actionGroup>

    <actionGroup name="AssertAdminOtherCountProductActionGroup" extends="AssertAdminCountProductActionGroup">
        <grabMultiple selector="selectorForProductB" stepKey="grabProducts"/>
    </actionGroup>
</actionGroups>
```

> Action groups without "extends":

```xml
<actionGroups>
    <actionGroup name="AssertAdminCountProductActionGroup">
        <arguments>
            <argument name="count" type="string"/>
        </arguments>
        <grabMultiple selector="selectorForProductA" stepKey="grabProducts"/>
        <assertCount stepKey="assertCount">
            <expectedResult type="int">{{count}}</expectedResult>
            <actualResult type="variable">grabProducts</actualResult>
        </assertCount>
    </actionGroup>

    <actionGroup name="AssertAdminOtherCountProductActionGroup">
        <arguments>
            <argument name="count" type="string"/>
        </arguments>
        <grabMultiple selector="selectorForProductB" stepKey="grabProducts"/>
        <assertCount stepKey="assertCount">
            <expectedResult type="int">{{count}}</expectedResult>
            <actualResult type="variable">grabProducts</actualResult>
        </assertCount>
    </actionGroup>
</actionGroups>
```

### Add an action

__Use case__: The `AdminGetProductCountActionGroup` action group returns the count of products.
Add a new test `AssertAdminVerifyProductCountActionGroup` that asserts the count of products:

> Action groups with "extends":

```xml
<actionGroups>
    <actionGroup name="AdminGetProductCountActionGroup">
        <arguments>
            <argument name="productSelector" type="string"/>
        </arguments>
        <grabMultiple selector="{{productSelector}}" stepKey="grabProducts"/>
    </actionGroup>

    <actionGroup name="AssertAdminVerifyProductCountActionGroup" extends="AdminGetProductCountActionGroup">
        <arguments>
            <argument name="count" type="string"/>
        </arguments>
        <assertCount stepKey="assertCount" after="grabProducts">
            <expectedResult type="int">{{count}}</expectedResult>
            <actualResult type="variable">grabProducts</actualResult>
        </assertCount>
    </actionGroup>
</actionGroups>
```

> Action groups without "extends":

```xml
<actionGroups>
    <actionGroup name="AdminGetProductCountActionGroup">
        <arguments>
            <argument name="productSelector" type="string"/>
        </arguments>
        <grabMultiple selector="{{productSelector}}" stepKey="grabProducts"/>
    </actionGroup>

    <actionGroup name="AssertAdminVerifyProductCountActionGroup">
        <arguments>
            <argument name="count" type="string"/>
            <argument name="productSelector" type="string"/>
        </arguments>
        <grabMultiple selector="{{productSelector}}" stepKey="grabProducts"/>
        <assertCount stepKey="assertCount">
            <expectedResult type="int">{{count}}</expectedResult>
            <actualResult type="variable">grabProducts</actualResult>
        </assertCount>
    </actionGroup>
</actionGroups>
```

<!-- {% endraw %} -->

## Extending data

Extend data to reuse entities in your module.

### Update a data entry

__Use case__: Create an entity named `DivPanelGreen`, which is similar to the `DivPanel` entity, except that it is green.

> Entities with "extends":

```xml
<entities>
    <entity name="DivPanel">
        <data key="divColor">Red</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
    </entity>
    <entity name="DivPanelGreen" extends="DivPanel">
        <data key="divColor">Green</data>
    </entity>
</entities>
```

> Entities without "extends":

```xml
<entities>
    <entity name="DivPanel">
        <data key="divColor">Red</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
    </entity>
    <entity name="DivPanelGreen" extends="DivPanel">
        <data key="divColor">Green</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
    </entity>
</entities>
```

### Add a data entry

__Use case__: Create an entity named `DivPanelGreen`, which is similar to the `DivPanel` entity, except that it has a specific panel color.

> Entities with "extends":

```xml
<entities>
    <entity name="DivPanel">
        <data key="divColor">Red</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
    </entity>
    <entity name="DivPanelGreen" extends="DivPanel">
        <data key="divColor">#000000</data>
        <data key="AttributeHidden">True</data>
    </entity>
</entities>
```

> Entities without "extends":

```xml
<entities>
    <entity name="DivPanel">
        <data key="divColor">Red</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
    </entity>
    <entity name="DivPanelGreen" extends="DivPanel">
        <data key="divColor">#000000</data>
        <data key="divSize">80px</data>
        <data key="divWidth">100%</data>
        <data key="AttributeHidden">True</data>
    </entity>
</entities>
```

<!-- Link definitions -->
[test]: ./test.md
[data]: ./data.md
[action group]: ./test/action-groups.md
[actions]: ./test/actions.md
