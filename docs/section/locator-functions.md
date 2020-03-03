# Locator functions

## Define Locator::functions in elements

 Codeception has a set of very useful [Locator functions][] that may be used by elements inside a [section][].

Declare an element with a `locatorFunction`:

```xml
<element name="simpleLocator" type="button" locatorFunction="Locator::contains('label', 'Name')"/>
```

When using the `locatorFunction`, omit `Locator::` for code simplicity:

```xml
<element name="simpleLocatorShorthand" type="button" locatorFunction="contains('label', 'Name')"/>
```

An element's `locatorFunction` can also be parameterized the same way as [parameterized selectors][]:

<!-- {% raw %} -->

```xml
<element name="simpleLocatorTwoParam" type="button" locatorFunction="contains({{arg1}}, {{arg2}})" parameterized="true"/>
```

An element cannot, however, have both a `selector` and a `locatorFunction`.

## Call Elements that use locatorFunction

Given the above element definitions, you call the elements in a test just like any other element. No special reference is required, as you are still just referring to an `element` inside a `section`:

```xml
<test name="LocatorFuctionTest">
   <click selector="{{LocatorFunctionSection.simpleLocator}}" stepKey="SimpleLocator"/>
   <click selector="{{LocatorFunctionSection.simpleLocatorTwoParam('string1', 'string2')}}" stepKey="TwoParamLiteral"/>
</test>
```

<!-- {% endraw %} -->

<!-- Link Definitions -->
[Locator functions]: http://codeception.com/docs/reference/Locator
[section]: ../section.md
[parameterized selectors]: ./parameterized-selectors.md