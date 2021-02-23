# MFTF 2.7.0 backward incompatible changes

This page highlights backward incompatible changes from previous 2.6.x releases that have major impacts and require special instructions to ensure third-party tests continue working with Magento core tests.

## Minimum supported PHP version changes

We changed the minimum PHP version requirement from 7.0 to 7.3. Because of the PHP version requirement change, this MFTF version only supports Magento 2.3.x, where x is 7 or above.

## XSD schema changes

- `arrayVariable` is added as an additional supported result type in assertions.
This is used for `assertContains` and `assertNotContains` actions when the result type is a variable that contains an array.
  
   ```xml
   <grabMultiple selector="{{AdminGridHeaders.columnsNames}}" stepKey="columns"/>
   <assertContains stepKey="assertContains">
      <expectedResult type="string">ID</expectedResult>
      <actualResult type="arrayVariable">columns</actualResult>
   </assertContains>
   ```

## MFTF actions

### `formatMoney` removed

**Action**: `formatMoney` has been removed in favor of `formatCurrency`.

**Reason**: PHP 7.4 has deprecated use of `formatMoney`.

**Details**: Format input to specified currency according to the locale specified.

Usage example:

```xml
<formatCurrency userInput="1234.56789000" locale="de_DE" currency="USD" stepKey="usdInDE"/>
```

### `assertArraySubset` removed

**Action**: Assert action `assertArraySubset` has been removed.

**Reason**: PHPUnit 9 has dropped support for this assertion.
