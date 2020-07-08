# MFTF versioning schema

This document describes the versioning policy for the Magento Functional Testing Framework (MFTF), including the version numbering schema.

## Backward compatibility

In this context, backward compatibility means that when changes are made to the MFTF, all existing tests still run normally.
If a modification to MFTF forces tests to be changed, this is a backward incompatible change.

## Find your MFTF version number

To find the version of MFTF that you are using, run the Magento CLI command:

```bash
vendor/bin/mftf --version
```

## Versioning Policy

MFTF versioning policy follows [Semantic Versioning](https://semver.org/) guidelines.

### 3-component version numbers

Version numbering schemes help users to understand the scope of the changes made a new release.

```tree
X.Y.Z
| | |
| | +-- Backward Compatible changes (Patch release - bug fixes, small additions)
| +---- Backward Compatible changes (Minor release - small new features, bug fixes)
+------ Backward Incompatible changes (Major release - new features and/or major changes)
```

For example:

-  Magento 2 ships with MFTF version 2.3.9
-  A patch is added to fix a bug: 2.3.10 (Increment Z = backward compatible change)
-  New action command added: 2.4.0 (Increment Y, set Z to 0 = backward compatible change)
-  New action added: 2.4.1 (Increment Z = backward compatible change)
-  Major new features added to MFTF to support changes in Magento codebase: 3.0.0. (Increment X, reset Y and Z to 0 = backward incompatible change)

### Z release - patch

Patch version **Z** MUST be incremented for a release that introduces only backward compatible changes.
  
### Y release - minor

Minor version **Y** MUST be incremented for a release that introduces new, backward compatible features.
It MUST be incremented if any test or test entity is marked as deprecated.
It MAY include patch level changes. Patch version MUST be reset to 0 when minor version is incremented.

### X release - major

Major version **X** MUST be incremented for a release that introduces backward incompatible changes.
A major release can also include minor and patch level changes.
You must reset the patch and minor version to 0 when you change the major version.

## Magento 2  compatibility

This table lists the version of the MFTF that was released with a particular version of Magento.

|Magento version| MFTF  version|
|---    |---     |
| 2.3.4 | 2.5.3  |
| 2.3.3 | 2.4.5  |
| 2.3.2 | 2.3.14 |
| 2.3.1 | 2.3.13 |
| 2.3.0 | 2.3.9  |
| 2.2.8 | 2.3.13 |
