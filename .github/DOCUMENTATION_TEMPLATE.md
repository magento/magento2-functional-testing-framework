# How to conttribute to MFTF docs

MFTF documentation is kept within the /docs/ folder in this repository.
We welcome contributions to the documentation.
This page describes the process for submitting documentation and serves as a template for a properly written content.

The contribution workflow for the Magento functional testing framework (MFTF) documentation is the same as submitting code.

1. Make a branch from the `develop` branch in the [MFTF repo][].
1. Make edits/additions/deletions as needed.
1. Submit your PR to the `develop` branch.

Once submitted, a member of the documentation team will review and merge it.
If it needs any work, we will inform you.

Any changes to the Table of Contents will need to be made in the regular [devdocs repo][].

## H2 Heading - blank line before and after, capitalize first word only

1. Number all ordered list items as `1.`
1. The build process will number them correctly.
1. Single spafce after the number.
1. Blank line before and after list.

## Unordered lists

- Use dashes in unordered lists.
- Add one space after the dash.
- Add a blank line before and after list.

## Code samples

For formatting and code coloring, wrap code samples in the following format:
Replace the `xml` with the corresponding language (tupe) of the code sample. Use `bash` for shell commands and `terminal` for terminal output.

```xml
<xmlSample>
   ...
   ...
</xmlSample>
```

## Markdown tables

| Header      | Header |
| ----------- | ----------- |
| Colume 1 text | Column 2 text|
| Column 1 text | Column 2 text|

Markdown formatting works for simple tables. It does not support multiline content within a cell, or split/merged cells within a row or column.

## Other tips

- Use spaces instead of tabs.
- Do not duplicate blank lines.
- Read more about how to [Contribute to Magento Devdocs][]

<!-- For readability, we abstract the link URLS to the bottom of the page. The extra set of square brackets denotes it is a link, rather than plain brackets. >

<!-- Link Definitions -->
[devdocs repo]: https://github.com/magento/devdocs
[MFTF repo]: https://github.com/magento/magento2-functional-testing-framework
[Contribute to Magento Devdocs]: https://github.com/magento/devdocs/blob/master/.github/CONTRIBUTING.md
