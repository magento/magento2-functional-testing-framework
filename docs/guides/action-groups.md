# Action Group Best Practices

We strive to write tests using only action groups. Fortunately, we have built up a large set of action groups to get started. We can make use of them and extend them for our own specific needs. In some cases, we may never even need to write action groups of our own. We may be able to simply chain together calls to existing action groups to implement our new test case.

## Why use Action Groups?

Action groups simplify maintainability by reducing duplication. Because they are re-usable building blocks, odds are that they are already made use of by existing tests in the Magento codebase. This proves their stability through real-world use. Take for example, the action group named `LoginAsAdmin`:

```xml
<actionGroup name="LoginAsAdmin">
    <annotations>
        <description>Login to Backend Admin using provided User Data. PLEASE NOTE: This Action Group does NOT validate that you are Logged In.</description>
    </annotations>
    <arguments>
        <argument name="adminUser" type="entity" defaultValue="DefaultAdminUser"/>
    </arguments>

    <amOnPage url="{{AdminLoginPage.url}}" stepKey="navigateToAdmin"/>
    <fillField selector="{{AdminLoginFormSection.username}}" userInput="{{adminUser.username}}" stepKey="fillUsername"/>
    <fillField selector="{{AdminLoginFormSection.password}}" userInput="{{adminUser.password}}" stepKey="fillPassword"/>
    <click selector="{{AdminLoginFormSection.signIn}}" stepKey="clickLogin"/>
    <closeAdminNotification stepKey="closeAdminNotification"/>
</actionGroup>
``` 

Logging in to the admin panel is one of the most used action groups. It is used around 1,500 times at the time of this writing.

Imagine if this was not an action group and instead we were to copy and paste these 5 actions every time. In that scenario, if a small change was needed, it would require a lot of work. But with the action group, we can make the change in one place.

## How to extend action groups

Again using `LoginAsAdmin` as our example, we trim away metadata to clearly reveal that this action group performs 5 actions:

```xml
<actionGroup name="LoginAsAdmin">
    ...
    <amOnPage url="{{AdminLoginPage.url}}" .../>
    <fillField selector="{{AdminLoginFormSection.username}}" .../>
    <fillField selector="{{AdminLoginFormSection.password}}" .../>
    <click selector="{{AdminLoginFormSection.signIn}}" .../>
    <closeAdminNotification .../>
</actionGroup>
```

This works against the standard Magento admin panel login page. Bu imagine we are working on a Magento extension that adds a CAPTCHA field to the login page. If we create and activate this extension and then run all existing tests, we can expect almost everything to fail because the CAPTCHA field is left unfilled.

We can overcome this by making use of MFTF's extensibility. All we need to do is to provide a "merge" that modifies the existing `LoginAsAdmin` action group. Our merge file will look like:

```xml
<actionGroup name="LoginAsAdmin">
    <fillField selector="{{CaptchaSection.captchaInput}}" before="signIn" .../>
</actionGroup>
```

Because the name of this merge is also `LoginAsAdmin`, the two get merged together and an additional step happens everytime this action group is used.

To continue this example, imagine someone else is working on a 'Two-Factor Authentication' extension and they also provide a merge for the `LoginAsAdmin` action group. Their merge looks similar to what we have already seen. The only difference is that this time we fill a different field:

```xml
<actionGroup name="LoginAsAdmin">
    <fillField selector="{{TwoFactorSection.twoFactorInput}}" before="signIn" .../>
</actionGroup>
```

Bringing it all together, our resulting `LoginAsAdmin` action group becomes this:

```xml
<actionGroup name="LoginAsAdmin">
    ...
    <amOnPage url="{{AdminLoginPage.url}}" .../>
    <fillField selector="{{AdminLoginFormSection.username}}" .../>
    <fillField selector="{{AdminLoginFormSection.password}}" .../>
    <fillField selector="{{CaptchaSection.captchaInput}}" .../>
    <fillField selector="{{TwoFactorSection.twoFactorInput}}" .../>
    <click selector="{{AdminLoginFormSection.signIn}}" .../>
    <closeAdminNotification .../>
</actionGroup>
```

No one file contains this exact content as above, but instead all three files come together to form this action group.

This extensibility can be applied in many ways. We can use it to affect existing Magento entities such as tests, action groups, and data. Not so obvious is that this tehcnique can be used within your own entities to make them more maintainable as well.
