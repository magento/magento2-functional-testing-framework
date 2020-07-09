# Test

Test cases in the Magento Functional Testing Framework (MFTF) are defined in XML as [`<tests>`].
`<tests>` is a [Codeception test container][Codeception] that contains individual test [`<test>`] with its metadata ([`<annotations>`]), before ([`<before>`]) and after ([`<after>`]) section.

MFTF `<test>` is considered a sequence of actions with associated parameters.
Any failed [assertion] within a test constitutes a failed test.

<div class="bs-callout bs-callout-info" markdown="1">
 `<before>` and `<after>` hooks are not global within `<tests>`.
They only apply to the `<test>` in which they are declared.
The steps in `<after>` are run in both successful **and** failed test runs.
</div>

The following diagram shows the structure of an MFTF test case:

![Structure of MFTF test case](img/test-dia.svg)

## Format

The format of a test XML file is:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<tests xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:noNamespaceSchemaLocation="urn:magento:mftf:Test/etc/testSchema.xsd">
    <test name="" insertBefore="" insertAfter="">
        <annotations>
            <!-- TEST ANNOTATIONS -->
        </annotations>
        <before>
            <!-- ACTIONS AND ACTION GROUPS PERFORMED BEFORE THE TEST -->
        </before>
        <after>
            <!-- ACTIONS AND ACTION GROUPS PERFORMED AFTER THE TEST -->
        </after>
        <!-- TEST ACTIONS, ACTION GROUPS, AND ASSERTIONS-->
    </test>
</tests>
```

## Principles

The following conventions apply to MFTF tests:

*  One `<test>` tag is allowed per test XML file.
*  All names within the framework are in the **PascalCase** format and must be alphanumeric.
*  Each action and action group call should have its own identifier `<stepKey>`.
*  A test may have any number of [assertions][assertion] at any point within the `<test>`.
*  If `<test>` is included in [`<suite>`][suites], it **cannot be generated in isolation** from `<before>` and `<after>` section of the suite (see [suites] for details).

## Elements reference

There are several XML elements that are used within `<test>` in the MFTF.

### tests {#tests-tag}

`<tests>` is a container for test and must contain exactly one [`<test>`].

### test {#test-tag}

`<test>` is a set of steps, including [actions], [assertions][assertion] and Action Group calls. It is a sequence of test steps that define test flow within a test method.

Attribute|Type|Use|Description
---|---|---|---
`name`|string|optional|The test identifier.
`remove`|boolean|optional|Set `true` to remove the test when merging.
`insertBefore`|string|optional| This option is used for [merging]. It enables you to add all test actions contained in the original test into a test with the same name BEFORE the test step with `stepKey` that you assigned in `insertBefore`.
`insertAfter`|string|optional| Set `stepKey` of the test step after which you want to insert the test when [merging].
`deprecated`|string|optional|Used to warn about the future deprecation of the test. String will appear in Allure reports and console output at runtime.
`extends`|string|optional|A name of the parent test to [extend].

`<test>` may also contain [`<annotations>`], [`<before>`], [`<after>`], any [action][actions], or [`<actionGroup>`].

### annotations {#annotations-tag}

[Annotations] are supported by both [Codeception] and [Allure].

Codeception annotations typically provide metadata and are able to influence test selection.
Allure annotations provide metadata for reporting.

### before {#before-tag}

`<before>` wraps the steps that are preconditions for the [`<test>`]. For example: Change configuration, create Customer Account, Create Category and Product.

`<before>` may contain these child elements:

*  Any [Action][actions]
*  [`<actionGroup>`]s

### after {#after-tag}

`<after>` wraps the steps to perform after the [`<test>`]. The steps are run in both successful **and** failed test runs. The goal of this section is to perform cleanup (revert the environment to the pre-test state).

`<after>` may contain:

*  Any [Action][actions]
*  [`<actionGroup>`]

### actionGroup {#actiongroup-tag}

`<actionGroup>` calls a corresponding [action group].

Attribute|Type|Use|Description
---|---|---|---
`ref`|string|required|References the required action group by its `name`.
`stepKey`|string|required| Identifies the element within `<test>`.
`before`|string|optional| `<stepKey>` of an action or action group that must be executed next while merging.
`after`|string|optional| `<stepKey>` of an action or action group that must be executed one step before the current one while merging.

`<actionGroup>` may contain [`<argument>`].

### argument {#argument-tag}

`<argument>` sets an argument that is used in the parent [`<actionGroup>`].

Attribute|Type|Use
---|---|---
`name`|string|optional| Name of the argument.
`value`|string|optional| Value of the argument.

See [Action groups][action group] for more information.

<!-- Link definitions -->

[`<actionGroup>`]: #actiongroup-tag
[`<after>`]: #after-tag
[`<annotations>`]: #annotations-tag
[`<argument>`]: #argument-tag
[`<before>`]: #before-tag
[`<test>`]: #test-tag
[`<tests>`]: #tests-tag
[action group]: ./test/action-groups.md
[actions]: ./test/actions.md
[Allure]: https://github.com/allure-framework/
[Annotations]: ./test/annotations.md
[assertion]: ./test/assertions.md
[Codeception]: https://codeception.com/docs/07-AdvancedUsage
[extend]: extending.md
[merging]: ./merging.md#insert-after
[suites]: ./suite.md
