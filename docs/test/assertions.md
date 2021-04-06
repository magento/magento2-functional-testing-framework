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

The `<assertElementContainsAttribute>` asserts that the selected html element contains and matches the expected value for the given attribute.

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

See [assertArrayHasKey docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertArrayHasKey)

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertArrayNotHasKey

See [assertArrayNotHasKey docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertArrayNotHasKey).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertContains

See [assertContains docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertContains).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsString

See [assertStringContainsString docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringContainsString).

Example:

```xml
<assertStringContainsString stepKey="assertDropDownTierPriceTextProduct1">
    <expectedResult type="string">Buy 5 for $5.00 each and save 50%</expectedResult>
    <actualResult type="variable">DropDownTierPriceTextProduct1</actualResult>
</assertStringContainsString>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text describing the cause of the failure.
`stepKey`|string|required| Unique identifier of the text step.
`before`|string|optional| `stepKey` of the action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsStringIgnoringCase

See [assertStringContainsStringIgnoringCase docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringContainsStringIgnoringCase).

Example:

```xml
<assertStringContainsStringIgnoringCase stepKey="verifyContentType">
    <actualResult type="variable">grabContentType</actualResult>
    <expectedResult type="string">{{image.extension}}</expectedResult>
</assertStringContainsStringIgnoringCase>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Message describing the cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of the action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertCount

See [assertCount docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertCount).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEmpty

See [assertEmpty docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertEmpty).

Example:

```xml
<assertEmpty stepKey="assertSearchButtonEnabled">
    <actualResult type="string">$grabSearchButtonAttribute</actualResult>
</assertEmpty>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEquals

See [assertEquals docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertEquals).

Example:

```xml
<assertEquals message="ExpectedPrice" stepKey="assertBundleProductPrice">
    <actualResult type="variable">grabProductPrice</actualResult>
    <expectedResult type="string">$75.00</expectedResult>
</assertEquals>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsWithDelta

See [assertEqualsWithDelta docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertEqualsWithDelta).

Attribute|Type|Use|Description
---|---|---|---
`delta`|string|optional|
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsCanonicalizing

See [assertEqualsCanonicalizing docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertEqualsCanonicalizing).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertEqualsIgnoringCase

See [assertEqualsIgnoringCase docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertEqualsIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFalse

See [assertFalse docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertFalse).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFileExists

See [assertFileExists docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertFileExists).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertFileNotExists

See [assertFileNotExists docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertFileNotExists).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterOrEquals

See [assertGreaterOrEquals docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertGreaterOrEquals).

Example:

```xml
<assertGreaterOrEquals stepKey="checkStatusSortOrderAsc" after="getOrderStatusSecondRow">
	<actualResult type="const">$getOrderStatusSecondRow</actualResult>
	<expectedResult type="const">$getOrderStatusFirstRow</expectedResult>
</assertGreaterOrEquals>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterThan

See [assertGreaterThan docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertGreaterThan).

Example:

```xml
<assertGreaterThan stepKey="checkQuantityWasChanged">
	<actualResult type="const">$grabEndQuantity</actualResult>
	<expectedResult type="const">$grabStartQuantity</expectedResult>
</assertGreaterThan>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertGreaterThanOrEqual

See [assertGreaterThanOrEqual docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertGreaterThanOrEqual).

Example:

```xml
<assertGreaterThanOrEqual stepKey="checkStatusSortOrderAsc" after="getOrderStatusSecondRow">
	<actualResult type="const">$getOrderStatusSecondRow</actualResult>
	<expectedResult type="const">$getOrderStatusFirstRow</expectedResult>
</assertGreaterThanOrEqual>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertInstanceOf

See [assertInstanceOf docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertInstanceOf).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertIsEmpty

See [assertIsEmpty docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertIsEmpty).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessOrEquals

See [assertLessOrEquals docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertLessOrEquals).

Example:

```xml
<assertLessOrEquals stepKey="checkHeightIsCorrect">
    <actualResult type="variable">getImageHeight</actualResult>
    <expectedResult type="variable">getSectionHeight</expectedResult>
</assertLessOrEquals>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessThan

See [assertLessThan docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertLessThan).

Example:

```xml
<assertLessThan stepKey="assertLessImages">
    <expectedResult type="variable">initialImages</expectedResult>
    <actualResult type="variable">newImages</actualResult>
</assertLessThan>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertLessThanOrEqual

See [assertLessThanOrEqual docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertLessThanOrEqual).

Example:

```xml
<assertLessThanOrEqual stepKey="checkHeightIsCorrect">
    <actualResult type="variable">getImageHeight</actualResult>
    <expectedResult type="variable">getSectionHeight</expectedResult>
</assertLessThanOrEqual>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotContains

See [assertNotContains docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotContains).

Example:

```xml
<assertNotContains stepKey="assertCustomerGroupNotInOptions">
    <actualResult type="variable">customerGroups</actualResult>
    <expectedResult type="string">{{customerGroup.code}}</expectedResult>
</assertNotContains>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringNotContainsString

See [assertStringNotContainsString docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringNotContainsString).

Example:

```xml
<assertStringNotContainsString stepKey="checkoutAsGuest">
    <expectedResult type="string">{{CaptchaData.checkoutAsGuest}}</expectedResult>
    <actualResult type="variable">$formItems</actualResult>
</assertStringNotContainsString>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringContainsStringIgnoringCase

See [assertStringNotContainsStringIgnoringCase docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringNotContainsStringIgnoringCase).

Example:

```xml
<assertStringContainsStringIgnoringCase stepKey="verifyContentType">
    <actualResult type="variable">grabContentType</actualResult>
    <expectedResult type="string">{{image.extension}}</expectedResult>
</assertStringContainsStringIgnoringCase>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEmpty

See [assertNotEmpty docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotEmpty).

Example:

```xml
<assertNotEmpty stepKey="checkSwatchFieldForAdmin">
	<actualResult type="const">$grabSwatchForAdmin</actualResult>
</assertNotEmpty>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEquals

See [assertNotEquals docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotEquals).

Example:

```xml
<assertNotEquals stepKey="assertNotEquals">
	<actualResult type="string">{$grabTotalAfter}</actualResult>
	<expectedResult type="string">{$grabTotalBefore}</expectedResult>
</assertNotEquals>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsWithDelta

See [assertNotEqualsWithDelta docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotEqualsWithDelta).

Attribute|Type|Use|Description
---|---|---|---
`delta`|string|optional|
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsCanonicalizing

See [assertNotEqualsCanonicalizing docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotEqualsCanonicalizing).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotEqualsIgnoringCase

See [assertNotEqualsIgnoringCase docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotEqualsIgnoringCase).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotInstanceOf

See [assertNotInstanceOf docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotInstanceOf).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotNull

See [assertNotNull docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotNull).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotRegExp

See [assertNotRegExp docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotRegExp).

Example:

```xml
<assertNotRegExp stepKey="simpleThumbnailIsNotDefault">
	<actualResult type="const">$getSimpleProductThumbnail</actualResult>
	<expectedResult type="const">'/placeholder\/thumbnail\.jpg/'</expectedResult>
</assertNotRegExp>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNotSame

See [assertNotSame docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNotSame).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertNull

See [assertNull docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertNull).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertRegExp

See [assertRegExp docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertRegExp).

Example:

```xml
<assertRegExp message="adminAnalyticsMetadata object is invalid" stepKey="validateadminAnalyticsMetadata">
    <expectedResult type="string">#var\s+adminAnalyticsMetadata\s+=\s+{\s+("[\w_]+":\s+"[^"]*?",\s+)*?("[\w_]+":\s+"[^"]*?"\s+)};#s</expectedResult>
    <actualResult type="variable">$pageSource</actualResult>
</assertRegExp>
```

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertSame

See [assertSame docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertSame).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringStartsNotWith

See [assertStringStartsNotWith docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringStartsNotWith).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertStringStartsWith

See [assertStringStartsWith docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertStringStartsWith).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### assertTrue

See [assertTrue docs on codeception.com](https://codeception.com/docs/modules/Asserts#assertTrue).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|optional|Text of informational message about a cause of failure.
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### expectException

See [expectException docs on codeception.com](https://codeception.com/docs/modules/Asserts#expectException).

Attribute|Type|Use|Description
---|---|---|---
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.

### fail

See [fail docs on codeception.com](https://codeception.com/docs/modules/Asserts#fail).

Attribute|Type|Use|Description
---|---|---|---
`message`|string|required|
`stepKey`|string|required| A unique identifier of the text step.
`before`|string|optional| `stepKey` of action that must be executed next.
`after`|string|optional| `stepKey` of the preceding action.
