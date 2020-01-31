# How To write good selectors

Selectors are the atomic unit of test writing. They fit into the hierarchy like this: MFTF tests make use of action groups > which are made up of actions > which interact with page objects > which contain elements > which are specified by selectors. Because they are fundamental building blocks, we must take care when writing them.

## What is a selector?

A "selector" works like an address to an element in the Document Object Model (DOM). It specifies page elements and allows MFTF to interact with them.
By 'element' we mean things such as input fields, buttons, tables, divs, etc.
By 'interact' we mean actions such as click, fill field, etc.

Selectors live inside of MFTF page objects and are meant to be highly re-usable amongst all tests. They can be written in either CSS or XPath.

## Why are good selectors important?

Good selectors are important because they are the most re-used component of functional testing. They are the lowest building blocks of tests; the foundation. If they are unstable then everything else built on top of them will inherit that instability.

## How do I write good selectors?

We could cover this subject with an infinite amount of documentation and some lessons only come from experience. This guide explains some DOs and DONTs to help you along the way towards selector mastery.

### Inspecting the DOM

To write a selector you need to be able to see the DOM and find the element within it. Fortunately you do not have to look at the entire DOM every time. Nor do you have to read it from top to bottom. Instead you can make use of your browsers built-in developer tools or go a step further and try out some popular browser extensions.

See these links for more information about built-in browser developer tools:

*  [Chrome Developer Tools](https://developers.google.com/web/tools/chrome-devtools/)
*  [Firefox Developer Tools](https://developer.mozilla.org/en-US/docs/Tools)

See these links for common browser addons that may offer advantages over browser developer tools:

*  [Live editor for CSS, Less & Sass - Magic CSS](https://chrome.google.com/webstore/detail/live-editor-for-css-less/ifhikkcafabcgolfjegfcgloomalapol?hl=en)
*  [XPath Helper](https://chrome.google.com/webstore/detail/xpath-helper/hgimnogjllphhhkhlmebbmlgjoejdpjl?hl=en)

### CSS vs XPath

There are similarities and differences between CSS and XPath. Both are powerful and complex in ways that are outside of the scope of this document.
In general:

*  CSS is more stable, easier to read, and easier to maintain (typically).
*  XPath provides several powerful tools and it has been around the longest so it is well documented.
*  XPath can be less stable and potentially unsupported by certain actions in Selenium.

### Priority

The best and most simple selector will always be to use an element ID: `#some-id-here`. If only we were so lucky to have this every time.

When writing selectors, you should prioritize finding in this order:

1. ID, name, class, or anything else that is unique to the element
2. Complex CSS selectors
3. XPath selectors
4. If none of the above work for you, then the last resort is to ask a developer to add a unique ID or class to the element you are trying to select.

We suggest the use of CSS selectors above XPath selectors when possible.

### Writing proper selectors

There are correct ways of writing selectors and incorrect ways. These suggestions will help you write better selectors.

#### Incorrect - copy selector/xpath

DO NOT right click on an element in your browser developer tools and select "Copy selector" or "Copy XPath" and simply use that as your selector. These auto-generated selectors are prime examples of what not to do.

These are bad:

```css
#html-body > section > div > div > div > div
```

```xpath
//*[@id='html-body']/section/div/div/div/div
```

Both include unnecessary hierarchical details. As written, we are looking for a `div` inside of a `div` inside of a `div` inside of... you get the picture. If an application developer adds another `div` parent tomorrow, for whatever reason, this selector will break. Furthermore, when reading it, it is not clear what the intended target is. It may also grab other elements that were not intended.

#### Do not be too general

DO NOT make your selectors too generic. If a selector is too generic, there is a high probability that it will match multiple elements on the page. Maybe not today, but perhaps tomorrow when the application being tested changes.

These are bad:

```html
input[name*='firstname']
```

The `*=` means `contains`. The selector is saying 'find an input whose name contains the string "firstname"'. But if a future change adds a new element to the page whose name also contains "firstname", then this selector will match two elements and that is bad.

```css
.add
```

Similarly here, this will match all elements which contains the class `.add`. This is brittle and susceptible to breaking when new elements/styles are added to the page.

#### Avoid being too specific

DO NOT make your selectors too specific either. If a selector is too specific, there is a high probability that it will break due to even minor changes to the application being tested.

These are bad:

```css
#container .dashboard-advanced-reports .dashboard-advanced-reports-description .dashboard-advanced-reports-title
```

This selector is too brittle. It would break very easily if an application developer does something as simple as adding a parent container for style reasons.

```xpath
//*[@id='container']/*[@class='dashboard-advanced-reports']/*[@class='dashboard-advanced-reports-description']/*[@class='dashboard-advanced-reports-title']
```

This is the same selector as above, but represented in XPath instead of CSS. It is brittle for the same reasons.

#### XPath selectors should not use @attribute="foo"

This XPath is fragile. It would fail if the attribute was `attribute="foo bar"`. Instead you should use `contains(@attribute, "foo")` where @attribute is any valid attribute such as @text or @class.

#### CSS and XPath selectors should avoid making use of hardcoded indices

Hardcoded values are by definition not flexible. A hardcoded index may change if new code is introduced. Instead, parameterize the selector.

GOOD: .foo:nth-of-type({{index}})

BAD: .foo:nth-of-type(1)

GOOD: button[contains(@id, "foo")][{{index}}]

BAD: button[contains(@id, "foo")][1]

GOOD: #actions__{{index}}__aggregator

BAD: #actions__1__aggregator

#### CSS and XPath selectors MUST NOT reference the @data-bind attribute

The @data-bind attribute is used by KnockoutJS, a framework Magento uses to create dynamic Javascript pages. Since this @data-bind attribute is tied to a specific framework, it should not be used for selectors. If Magento decides to use a different framework then these @data-bind selectors would break.

#### Use isolation

You should think in terms of "isolation" when writing new selectors.

For example, say you have a login form that contains a username field, a password field, and a 'Sign In' button. First isolate the parent element. Perhaps it's `#login-form`. Then target the child element under that parent element: `.sign-in-button` The result is `#login-form .sign-in-button`.

Using isolation techniques reduces the amount of DOM that needs to be processed. This makes the selector both accurate and efficient.

#### Use advanced notation

If you need to interact with the parent element but it is too generic, and the internal contents are unique then you need to:

1. Target the unique internal contents first.
1. Then jump to the parent element using `::parent`.

Imagine you want to find a table row that contains the string "Jerry Seinfeld". You can use the following XPath selector:

```xpath
//div[contains(text(), 'Jerry Seinfeld')]/parent::td/parent::tr
```

Note in this instance that CSS does not have an equivalent to `::parent`, so XPath is a better choice.

### CSS Examples

Examples of common HTML elements and the corresponding selector to find that element in the DOM:

Type|HTML|Selector
---|---|---
IDs|`<div id="idname"/>`|`#idname`
Classes|`<div class="classname"/>`|`.classname`
HTML Tags|`<div/>`|`div`
HTML Tag & ID|`<div id="idname"/>`|`div#idname`
HTML Tag & Class|`<div class="classname"/>`|`div.classname`
ID & Class|`<div id="idname" class="classname"/>`|`#idname.classname`
HTML Tag & ID & Class|`<div id="idname" class="classname"/>`|`div#idname.classname`

Examples of common CSS selector operators and their purpose:

Symbol|Name|Purpose|Selector
---|---|---|---
`*`|Universal Selector|Allows you to select ALL ELEMENTS on the Page. Wild Card.|`*`
Whitespace|Descendant Combinator|Allows you to combine 2 or more selectors.|`#idname .classname`
`>`|Child Combinator|Allows you to select the top-level elements THAT FOLLOWS another specified element.|`#idname > .classname`
`+`|Adjacent Sibling Combinator|Allows you to select an element THAT FOLLOWS DIRECTLY AFTER another specified element.|`#idname + .classname`
`~`|General Sibling Combinator|Allows you to select an element THAT FOLLOWS (directly or indirectly) another specified element.|`#idname ~ .classname`

Examples of CSS attribute operators and their purpose:

Symbol|Purpose|Example
---|---|---
`=`|Returns all elements that CONTAIN the EXACT string in the value.|`[attribute='value']`
`*=`|Returns all elements that CONTAINS the substring in the value.|`[attribute*='value']`
`~=`|Returns all elements that CONTAINS the given words delimited by spaces in the value.|`[attribute~='value']`
`$=`|Returns all elements that ENDS WITH the substring in the value.|`[attribute$='value']`
`^=`|Returns all elements that BEGIN EXACTLY WITH the substring in the value.|`[attribute^='value']`
`!=`|Returns all elements that either DOES NOT HAVE the given attribute or the value of the attribute is NOT EQUAL to the value.|`[attribute!='value']`

### XPath Examples

#### `/` vs `//`

The absolute XPath selector is a single forward slash `/`. It is used to provide a direct path to the element from the root element.

WARNING: The `/` selector is brittle and should be used sparingly.

Here is an example of what NOT to do, but this demonstrates how the selector works:

```xpath
/html/body/div[2]/div/div[2]/div[1]/div[2]/form/div/input
```

In the BAD example above, we are specifying a very precise path to an input element in the DOM, starting from the very top of the document.

Similarly, the relative XPath selector is a double forward slash `//`. It is used to start searching for an element anywhere in the DOM starting from the specified element. If no element is defined, the entire DOM is searched.

Example:

```xpath
//div[@class=’form-group’]//input[@id='user-message']
```

In the `GOOD` example above, all `<div class='form-group'/>` elements in the DOM are matched first. Then all `<input id='user-message'/>` with `<div class='form-group'/>` as one of its parents are matched. The parent does not have to immediately precede it since it uses another double forward slash `//`.

#### Parent Selectors

The parent selector (`..`) allows you to jump to the parent element.

Example #1:

Given this HTML:

```html
<tr>
    <td>
        <div>Unique Value</div>
    </td>
</tr>
```

We can locate the `<tr>` element with this selector:

```xpath
//*[text()='Unique Value']/../..
```

Example #2:

Given this HTML:

```html
<tr>
    <td>
        <a href=“#”>Edit</a>
    </td>
    <td>
        <div>Unique Value</div>
    </td>
</tr>
```

We can locate the `<a>` element with this selector:

```xpath
//div[text()='Unique Value']/../..//a
```

#### Attribute Selectors

Attribute selectors allow you to select elements that match a specific attribute value.

Examples:

Attribute|HTML|Selector
---|---|---
id|`<div id='idname'/>`|`//*[@id='idname']`
class|`<div class='classname'/>`|`//*[@class='classname']`
type|`<button type='submit'/>`|`//*[@type='submit']`
value|`<input value='value'/>`|`//*[@value='value']`
href|`<a href='https://google.com'/>`|`//*[@href='https://google.com']`
src|`<img src='/img.png'/>`|`//*[@src='/img.png']`

#### `contains()` Selector

The `contains()` selector allows you to select an element that contains an attribute value string.

Examples:

Attribute|HTML|Selector
---|---|---
`text()`|`<p>Hello World!</p>`|`[contains(text(), 'Hello')]`
`@id`|`<div id='idname1234abcd'/>`|`[contains(@id, 'idname')]`
`@class`|`<div class='classname1 classname2'/>`|`[contains(@class, 'classname1')]`
`@name`|`<input name='inputname'/>`|`[contains(@name, 'name')]`
`@value`|`<input value='value'/>`|`[contains(@value, 'value')]`
`@href`|`<a href='https://google.com'/>`|`[contains(@href, 'google.com')]`

#### `text()` Selector

The `text()` selector allows you to select an element that contains a specific string.

Examples:

Type|HTML|Selector
---|---|---
Exact Match|`<p>Hello World!!</p>`|`//p[text()='Hello World!!']`
Substring Match|`<p>Hello World!!</p>`|`//p[contains(text(), 'Hello')]`

#### `starts-with()` Selector

The `starts-with()` selector allows you to select an element whose attribute or text starts with a search string.

Examples:

Attribute|HTML|Selector
---|---|---
`@id`|`<div id='unique_id_abcd1234'/>`|`//*[starts-with(@id, 'unique_id')]`
`@class`|`<div class='unique_class_abcd1234'/>`|`//*[starts-with(@class, 'unique_class')]`
`@href`|`<a href='https://www.google.com/'/>`|`//a[starts-with(@href, 'https://')]`
`text()`|`<p>Hello World!</p>`|`//p[starts-with(text(), 'Hello ')]`

#### `ends-with()` Selector

The `ends-with()` selector allows you to select an element whose attribute or text ends with a search string.

Examples:

Attribute|HTML|Selector
---|---|---
`@id`|`<div id='abcd1234_unique_id'/>`|`//*[ends-with(@id, 'unique_id')]`
`@class`|`<div class='abcd1234_unique_class'/>`|`//*[ends-with(@class, 'unique_class')]`
`@href`|`<a href='https://www.google.com'/>`|`//a[ends-with(@href, 'google.com')]`
`text()`|`<p>Hello World!</p>`|`//p[ends-with(text(), 'World!')]`

### Translating Between CSS and XPath

Most of the time it is possible to translate from CSS to XPath and vice versa. Here are some examples:

Type|CSS|XPath
---|---|---
IDs|`#idname`|`//*[@id='idname']`
Classes|`.classname`|`//*[@class='classname']`
HTML Tags|`div`|`//div`
HTML Tag & ID|`div#idname`|`//div[@id='idname']`
HTML Tag & Class|`div.classname`|`//div[@class='classname']`
Universal|`*`|`//*`
Descendant|`#idname .classname`|`//*[@id='idname']//*[@class='classname']`
Child|`#idname > .classname`|`//*[@id='idname']/*[@class='classname']`
Adjacent Sibling|`#idname + .classname`|`//*[@id='idname']/following-sibling::*[@class='classname'][1]`
General Sibling|`#idname ~ .classname`|`//*[@id='idname']/following-sibling::*[@class='classname']`
