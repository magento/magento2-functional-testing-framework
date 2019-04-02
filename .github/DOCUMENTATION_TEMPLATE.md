# How to conttribute to MFTF docs

MFTF documentation is kept within the /docs/ folder in this repository.
We welcome contributions to the documentation.
This page describes the process for submitting docs and serves as a template so that docs are written properly.

The contribution workflow for docs is the same as submitting code.
To contribute to MFTF docs:

1. Make a branch from the [MFTF repo][].
1. Make edits/additions/deletions as needed.
1. Submit your PR to the `develop` branch.

Once submitted, it will be reviewed by a member of the documentation team.
If approved it will be tested and merged.
If it needs any work, we will inform you.

Any changes to the Table of Contents will need to be made in the regular [devdocs repo][].

## H2 heading - blank line before and after, capitalize first word only

1. Ordered lists all all numbered 1.
1. The build process will number them correctly.
1. Single spafce after the number. Blank line before and after list.

### H3

- Unordered lists use dashes.
- One space after the dash.
- Blank line before and after list.

## Code Samples

For formatting and code coloring, wrap code samples in the following format.
Replace the `xml` with the file extension of the code sample type. Use `bash` for command-line text.

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

Markdown tables work for simple tables. If you need lists or other complex features within a cell, you may have to use a HTML table.

## Other tips

- Use spaces instead of tabs.
- One empty line between content. No duplicate empty lines.
- Read more about how to [Contribute to Magento Devdocs][]

<!-- For readability, we abstract the link URLS to the bottom of the page. The extra set of square brackets denotes it is a link, rather than plain brackets. >

<!-- Link Definitions -->
[devdocs repo]: https://github.com/magento/devdocs
[MFTF repo]: https://github.com/magento/magento2-functional-testing-framework
[Contribute to Magento Devdocs]: https://github.com/magento/devdocs/blob/master/.github/CONTRIBUTING.md
