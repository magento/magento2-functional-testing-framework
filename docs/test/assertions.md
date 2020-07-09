# Assertions

Assertions serve to pass or fail the [test](../test.md#test-tag) if a condition is not met. These assertions will look familiar to you if you've used any other testing framework, like PHPUnit.

All assertions contain the same [common actions attributes](./actions.md#common-attributes): `stepKey`, `before`, and `after`.

Most assertions contain a `message` attribute that specifies the text of an informational message to help you identify the cause of the failure.

## Principles

The [principles for actions](../test.md#principles) are also applicable to assertions.

Assertion actions have nested self-descriptive elements, `<expectedResult>` and `<actualResult>`. These elements contain a result type and a value:

* `type`
  * `const` (default)
  * `int`
  * `float`
  * `bool`
  * `string`
  * `variable`
  * `array`
* `value`

If `variable` is used, the test transforms the corresponding value to `$variable`. Use the `stepKey` of a test, that returns the value you want to use, in assertions:

`actual="stepKeyOfGrab" actualType="variable"`

To use variables embedded in a string in `expected` and `actual` of your assertion, use the `{$stepKey}` format:

`actual="A long assert string {$stepKeyOfGrab} with an embedded variable reference." actualType="variable"`

In case of `assertContains` actions, `<expectedResult>` is the needle and `<actualResult>` is the haystack.

## Example

The following example shows a common test that gets text from a page and asserts that it matches what we expect to see. If it does not, the test will fail at the assert step.

```xml
<!-- Grab a value from the page using any grab action -->
<grabTextFrom selector="#elementId" stepKey="stepKeyOfGrab"/>

<!-- Ensure that the value we grabbed matches our expectation -->
<assertEquals message="This is an optional human readable hint that will be shown in the logs if this assert fails." stepKey="assertEquals1">
   <expectedResult type="string">Some String</expectedResult>
   <actualResult type="string">A long assert string {$stepKeyOfGrab} with an embedded variable reference.</actualResult>
</assertEquals>
```

## Elements reference

### assertElementContainsAttribute

Example:

```xml
<assertElementContainsAttribute stepKey="assertElementContainsAttribute">
    <expectedResult selector=".admin__menu-overlay" attribute="style" type="string">color: #333;</expectedResult>
</assertElementContainsAttribute>
```

Attribute|Type|Use|Description
---|---|---|---
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertArrayIsSorted

The `<assertArrayIsSorted>` asserts that the array is sorted according to a specified sort order, ascending or descending.

Example:

```xml
<assertArrayIsSorted sortOrder="asc" stepKey="assertSorted">
    <array>[1,2,3,4,5,6,7]</array>
</assertArrayIsSorted>
```

Attribute|Type|Use|Description
---|---|---|---
`sortOrder`|Possible values: `asc`, `desc`|required| A sort order to assert on array values.
`stepKey`|string|required| A unique identifier of the test step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

It contains an `<array>` child element that specifies an array to be asserted for proper sorting.
It must be in typical array format like `[1,2,3,4,5]` or `[alpha, brontosaurus, zebra]`.

### assertArrayHasKey

See [assertArrayHasKey docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertArrayHasKey)

Attribute|Type|Use|Description
---|---|---|---`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertArrayNotHasKey

See [assertArrayNotHasKey docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertArrayNotHasKey).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertContains

See [assertContains docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertContains).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsString

See [assertStringContainsString docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringContainsString).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text describing the cause of the failure.
`stepKey`|string|required| Unique identifier of the text step.
`before`|string|optional| `stepKey` of the action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsStringIgnoringCase

See [assertStringContainsStringIgnoringCase docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringContainsStringIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Message describing the cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of the action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertCount

See [assertCount docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertCount).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEmpty

See [assertEmpty docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertEmpty).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEquals

See [assertEquals docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertEquals).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsWithDelta

See [assertEqualsWithDelta docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertEqualsWithDelta).

Attribute|Type|Use|Description
---|---|---|---
`delta`|string|optional|
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsCanonicalizing

See [assertEqualsCanonicalizing docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertEqualsCanonicalizing).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsIgnoringCase

See [assertEqualsIgnoringCase docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertEqualsIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFalse

See [assertFalse docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertFalse).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFileExists

See [assertFileExists docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertFileExists).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFileNotExists

See [assertFileNotExists docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertFileNotExists).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterOrEquals

See [assertGreaterOrEquals docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertGreaterOrEquals).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterThan

See [assertGreaterThan docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertGreaterThan).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterThanOrEqual

See [assertGreaterThanOrEqual docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertGreaterThanOrEqual).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertInstanceOf

See [assertInstanceOf docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertInstanceOf).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertIsEmpty

See [assertIsEmpty docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertIsEmpty).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessOrEquals

See [assertLessOrEquals docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertLessOrEquals).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessThan

See [assertLessThan docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertLessThan).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessThanOrEqual

See [assertLessThanOrEqual docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertLessThanOrEqual).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotContains

See [assertNotContains docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotContains).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringNotContainsString

See [assertStringNotContainsString docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringNotContainsString).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsStringIgnoringCase

See [assertStringNotContainsStringIgnoringCase docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringNotContainsStringIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEmpty

See [assertNotEmpty docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotEmpty).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEquals

See [assertNotEquals docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotEquals).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsWithDelta

See [assertNotEqualsWithDelta docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotEqualsWithDelta).

Attribute|Type|Use|Description
---|---|---|---
`delta`|string|optional|
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsCanonicalizing

See [assertNotEqualsCanonicalizing docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotEqualsCanonicalizing).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsIgnoringCase

See [assertNotEqualsIgnoringCase docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotEqualsIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotInstanceOf

See [assertNotInstanceOf docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotInstanceOf).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotNull

See [assertNotNull docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotNull).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotRegExp

See [assertNotRegExp docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotRegExp).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotSame

See [assertNotSame docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNotSame).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNull

See [assertNull docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertNull).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertRegExp

See [assertRegExp docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertRegExp).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertSame

See [assertSame docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertSame).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringStartsNotWith

See [assertStringStartsNotWith docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringStartsNotWith).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringStartsWith

See [assertStringStartsWith docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertStringStartsWith).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertTrue

See [assertTrue docs on codeception.com](http://codeception.com/docs/modules/Asserts#assertTrue).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### expectException

See [expectException docs on codeception.com](http://codeception.com/docs/modules/WebDriver#expectException).

Attribute|Type|Use|Description
---|---|---|---
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### fail

See [fail docs on codeception.com](http://codeception.com/docs/modules/WebDriver#fail).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|required|
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.
