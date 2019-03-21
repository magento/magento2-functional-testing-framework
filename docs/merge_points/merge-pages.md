# Merge pages

Sections can be merged into pages to cover your extension.

In this example we add a section that may be relevant to our extension to the list of sections underneath one page.

## Starting page

```xml
<page name="AdminCategoryPage" url="catalog/category/" area="admin" module="Magento_Catalog">
    <section name="AdminCategorySidebarActionSection"/>
    <section name="AdminCategoryMainActionsSection"/>
    <section name="AdminCategorySidebarTreeSection"/>
    <section name="AdminCategoryBasicFieldSection"/>
    <section name="AdminCategorySEOSection"/>
    <section name="AdminCategoryProductsSection"/>
    <section name="AdminCategoryProductsGridSection"/>
    <section name="AdminCategoryModalSection"/>
    <section name="AdminCategoryMessagesSection"/>
    <section name="AdminCategoryContentSection"/>
</page>
```

## File to merge

```xml
<page name="AdminCategoryPage" url="catalog/category/" area="admin" module="Magento_Catalog">
    <!-- myExtensionSection will simply be added to the page -->
    <section name="MyExtensionSection"/>
</page>
```

## Resultant page

```xml
<page name="AdminCategoryPage">
    <section name="AdminCategorySidebarActionSection"/>
    <section name="AdminCategoryMainActionsSection"/>
    <section name="AdminCategorySidebarTreeSection"/>
    <section name="AdminCategoryBasicFieldSection"/>
    <section name="AdminCategorySEOSection"/>
    <section name="AdminCategoryProductsSection"/>
    <section name="AdminCategoryProductsGridSection"/>
    <section name="AdminCategoryModalSection"/>
    <section name="AdminCategoryMessagesSection"/>
    <section name="AdminCategoryContentSection"/>
    <!-- New section merged -->
    <section name="MyExtensionSection"/>
</page>
```