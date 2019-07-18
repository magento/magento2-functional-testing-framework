## Selectors

The guidelines below will help you to write high quality selectors.

### Selectors SHOULD be written in CSS instead of Xpath whenever possible

### Xpath selectors SHOULD NOT use `@attribute="foo"`. 

Instead you SHOULD use `contains(@attribute, "foo")` where `@attribute` is any valid attribute such as `@text` or `@class`.

### CSS and Xpath selectors SHOULD be implemented in their most simple form

* <span class="color:green">GOOD:</span> `#foo`
* <span class="color:red">BAD:</span> `button[contains(@id, "foo")]`

### CSS and Xpath selectors SHOULD avoid making use of hardcoded indices

Instead you SHOULD parameterize the selector.

* <span class="color:green">GOOD:</span> `.foo:nth-of-type({{index}})`
* <span class="color:red">BAD:</span> `.foo:nth-of-type(1)`

* <span class="color:green">GOOD:</span> `button[contains(@id, "foo")][{{index}}]`
* <span class="color:red">BAD:</span> `button[contains(@id, "foo")][1]`

* <span class="color:green">GOOD:</span> `#actions__{{index}}__aggregator`
* <span class="color:red">BAD:</span> `#actions__1__aggregator`

### CSS and XPath selectors MUST NOT reference the `@data-bind` attribute
