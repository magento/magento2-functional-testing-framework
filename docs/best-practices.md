# Best practices

Check out our best practices below to ensure you are getting the absolute most out of the Magento Functional Testing Framework.

## Focus on reusability

### Use existing Tests and resources

Magento offers more than **3000** acceptance tests, **2500** [Action group]s, **750** Page declarations with more than **1500** Section definitions.
It is very probable that behaviour you want to test already exists as a Test or Action Group.
Instead of writing everything by yourself - use `extends` attribute to refer to existing element and customize it.

**Reusable Resources**

{%raw%}

*  Tests (reusable with `<test extends="...">` argument)
*  Action Group (reusable with including `<actionGroup ref="...">`, or extending `<actionGroup extends="...">`)
*  Pages (reusable with reference `{{PageDefinition.url}}`)
*  Sections (reusable with reference `{{SectionDefinition.elementDefinition}}`)
*  Data Entities (reusable with reference `<createData entity="...">"` or extending `<entity extends="...">`)

{%endraw%}

<div class="bs-callout bs-callout-warning" markdown="1">

Avoid using resources that are marked as **Deprecated**. Usually there is a replacement provided for a deprecated resource.

</div>

### Extract repetitive Actions

Instead of writing a few of Tests that perform mostly the same actions, you should thing about [Action group] that is a container for repetitive Actions.
If each run needs different data, use `<arguments>` to inject necessary information.

We recommend to keep Action Groups having single responsibility, for example `AdminLoginActionGroup`, which expected outcome is being logged in as Administrator when [Action group] is executed.

## Contribute

Althought the Magento Core team and Contributors join forces to cover most of the features with tests, it is impossible to have this done quickly.
If you've covered Magento Core feature with Functional Tests - you are more than welcome to contribute.

You can also help with MFTF Test Migration to get the experience and valuable feedback from other community members and maintainers.

## Action group

1. [Action group] names should be sufficiently descriptive to inform a test writer of what the action group does and when it should be used. Add additional explanation in annotations if needed.
2. Provide default values for the arguments that apply to your most common case scenarios.
3. One `<actionGroup>` tag is allowed per action group XML file.

## `actionGroups` vs `extends`

Use an action group to wrap a set of actions to reuse them multiple times.

Use an [extension] when a test or action group needs to be repeated with the exception of a few steps.

### When to use `extends`

Use `extends` in your new test or action group when at least one of the following conditions is applicable to your case:

1. You want to keep the original test without any modifications.
2. You want to create a new test that follows the same path as the original test.
3. You want a new action group that behaves similarly to the existing action group, but you do not want to change the functionality of the original action group.

### When to avoid `extends`

Do not use `extends` in the following conditions:

1. You want to change the functionality of the test or action group and do not need to run the original version.
2. You plan to merge the base test or action group.

The following pattern is used when merging with `extends`:

1. The original test is merged.
2. The extended test is created from the merged original test.
3. The extended test is merged.

## Annotation

1. Use [annotations] in a test.
2. Update your annotations correspondingly when updating tests.

## Data entity

1. Keep your testing instance clean.
 Remove data after the test if the test required creating any data.
 Use a corresponding [`<deleteData>`] test step in your [`<after>`] block when using a [`<createData>`] action in a [`<before>`] block.
2. Make specific data entries under test to be unique.
 Enable data uniqueness where data values are required to be unique in a database by test design.
 Use `unique=”suffix”` or `unique=”prefix”` to append or prepend a unique value to the [entity] attribute.
 This ensures that tests using the entity can be repeated.
3. Do not modify existing data entity fields or merge additional data fields without complete understanding and verifying the usage of existing data in tests.
 Create a new data entity for your test if you are not sure.

## Naming conventions

### File names

Name files according to the following patterns to make searching in future more easy:

<!-- {% raw %} -->

#### Test file name

Format: {_Admin_ or _Storefront_}{Functionality}_Test.xml_, where Functionality briefly describes the testing functionality.

Example: _StorefrontCreateCustomerTest.xml_.

#### Action Group file name

Format: {_Admin_ or _Storefront_}{Action Group Summary}ActionGroup.xml`, where Action Group Summary describes with a few words what we can expect from it.

Example: _AdminCreateStoreActionGroup.xml_ 

#### Section file name

Format: {_Admin_ or _Storefront_}{UI Description}_Section.xml_, where UI Description briefly describes the testing UI.

Example: _AdminNavbarSection.xml_.

#### Data file name

Format: {Type}_Data.xml_, where Type represents the entity type.

<!-- {% endraw %} -->

Example: _ProductData.xml_.

### Object names

Use the _Foo.camelCase_ naming convention, which is similar to _Classes_ and _classProperties_ in PHP.

#### Upper case

Use an upper case first letter for:

*  File names. Example: _StorefrontCreateCustomerTest.xml_
*  Test name attributes. Example: `<test name="TestAllTheThingsTest">`
*  Data entity names. Example: `<entity name="OutOfStockProduct">`
*  Page name. Example: `<page name="AdminLoginPage">`
*  Section name. Example: `<section name="AdminCategorySidebarActionSection">`
*  Action group name. Example: `<actionGroup name="LoginToAdminActionGroup">`

#### Lower case

Use a lower case first letter for:

*  Data keys. Example: `<data key="firstName">`
*  Element names. Examples: `<element name="confirmDeleteButton"/>`
*  Step keys. For example: `<click selector="..." stepKey="clickLogin"/>`

## Page object

1. One `<page>` tag is allowed per page XML file.
2. Use [parameterized selectors] for constructing a selector when test-specific or runtime-generated information is needed.
Do not use them for static elements.

<span class="color:red">
BAD:
</span>

<!-- {% raw %} -->

``` xml
<element name="relatedProductSectionText" type="text" selector=".fieldset-wrapper.admin__fieldset-section[data-index='{{productType}}']" parameterized="true"/>
```

<!-- {% endraw %} -->

<span class="color:green">
GOOD:
</span>

Define these three elements and reference them by name in the tests.

``` xml
<element name="relatedProductSectionText" type="text" selector=".fieldset-wrapper.admin__fieldset-section[data-index='related']"/>
<element name="upSellProductSectionText" type="text" selector=".fieldset-wrapper.admin__fieldset-section[data-index='upsell']"/>
<element name="crossSellProductSectionText" type="text" selector=".fieldset-wrapper.admin__fieldset-section[data-index='crosssell']"/>
```

## Test

1. Use actions such as [`<waitForElementVisible>`], [`<waitForLoadingMaskToDisappear>`], and [`<waitForElement>`] to wait the exact time required for the test step.
 Try to avoid using the [`<wait>`] action, because it forces the test to wait for the time you specify. You may not need to wait so long to proceed.
1. Keep your tests short and granular for target testing, easier reviews, and easier merge conflict resolution.
 It also helps you to identify the cause of test failure.
1. Use comments to keep tests readable and maintainable:
   *  Keep the inline `<!-- XML comments -->` and [`<comment>`] tags up to date.
     It helps to inform the reader of what you are testing and to yield a more descriptive Allure report.
   *  Explain in comments unclear or tricky test steps.
1. Refer to [sections] instead of writing selectors.
1. One `<test>` tag is allowed per test XML file.

## Test step merging order

When setting a [merging] order for a test step, do not depend on steps from Magento modules that could be disabled by an application.

For example, when you write a test step to create a gift card product, set your test step **after** simple product creation and let the MFTF handle the merge order.
Since the configurable product module could be disabled, this approach is more reliable than setting the test step **before** creating a configurable product.

<!-- Link definitions -->

[`<after>`]: test/actions.html#before-and-after
[`<before>`]: test/actions.html#before-and-after
[`<comment>`]: test/actions.html#comment
[`<createData>`]: test/actions.html#createdata
[`<deleteData>`]: test/actions.html#deletedata
[`<wait>`]: test/actions.html#wait
[`<waitForElement>`]: test/actions.html#waitforelement
[`<waitForElementVisible>`]: test/actions.html#waitforelementvisible
[`<waitForLoadingMaskToDisappear>`]: test/actions.html#waitforloadingmasktodisappear
[Action group]: test/action-groups.html
[annotations]: test/annotations.html
[entity]: data.html
[extension]: extending.html
[merging]: merging.html
[parameterized selectors]: section/parameterized-selectors.html
[sections]: section.html
[MFTF Test Migration]: https://github.com/magento/magento-functional-tests-migration
