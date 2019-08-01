## Selectors

These guidelines should help you to write high quality selectors.

### Selectors SHOULD be written in CSS instead of XPath whenever possible

CSS is generally easier to read than XPath. For example, `//*[@id="foo"]` in XPath can be expressed as simply as `#foo` in CSS.
See this [XPath Cheatsheet](https://devhints.io/xpath) for more examples.

### XPath selectors SHOULD NOT use `@attribute="foo"`. 

This would fail if the attribute was `attribute="foo bar"`.
Instead you SHOULD use `contains(@attribute, "foo")` where `@attribute` is any valid attribute such as `@text` or `@class`.

### CSS and XPath selectors SHOULD be implemented in their most simple form

* <span class="color:green">GOOD:</span> `#foo`
* <span class="color:red">BAD:</span> `button[contains(@id, "foo")]`

### CSS and XPath selectors SHOULD avoid making use of hardcoded indices

Instead you SHOULD parameterize the selector.

* <span class="color:green">GOOD:</span> `.foo:nth-of-type({{index}})`
* <span class="color:red">BAD:</span> `.foo:nth-of-type(1)`

* <span class="color:green">GOOD:</span> `button[contains(@id, "foo")][{{index}}]`
* <span class="color:red">BAD:</span> `button[contains(@id, "foo")][1]`

* <span class="color:green">GOOD:</span> `#actions__{{index}}__aggregator`
* <span class="color:red">BAD:</span> `#actions__1__aggregator`

### CSS and XPath selectors MUST NOT reference the `@data-bind` attribute

The `@data-bind` attribute is used by KnockoutJS, a framework Magento uses to create dynamic Javascript pages. Since this `@data-bind` attribute is tied to a specific framework, it should not be used for selectors. If Magento decides to use a different framework then these `@data-bind` selectors would break.
