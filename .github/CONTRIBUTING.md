# Contribution Guidelines for the Magento Functional Testing Framework

- [Contribution requirements](#contribution-requirements)
- [Fork a repository](#fork-a-repository)
  - [Update the fork with the latest changes](#update-the-fork-with-the-latest-changes)
- [Create a pull request](#create-a-pull-request)
- [Report an issue](#report-an-issue)
- [Read labels](#read-labels)
  - [Pull request status](#pull-request-status)
  - [Issue resolution status](#issue-resolution-status)
  - [Domains impacted](#domains-impacted)
  - [Type](#type)

Use the [fork] & [pull] model to contribute to the Magento Functional Testing Framework (MFTF) code base.
This contribution model has contributors maintaining their own copy of the forked code base (which can be easily synced with the main copy).
The forked repository is then used to submit a request to the base repository to pull a set of changes (pull request).

Contributions can take the form of new components or features, changes to existing features, tests, documentation (such as developer guides, user guides, examples, or specifications), bug fixes, optimizations, or just good suggestions.

The MFTF development team reviews all issues and contributions submitted by the community of developers in a "first-in, first-out" basis.
During the review we might require clarifications from the contributor.
If there is no response from the contributor for two weeks, the issue is closed.

Often when the MFTF team works on reviewing the suggested changes, we will add a label to the issue to indicate to our internal team certain information, like status or who is working the issue.
If you’re ever curious what the different labels mean, see the [table][labels] below for an explanation of each one.

## Code Of Conduct

This project adheres to the Adobe [Code Of Conduct](../CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. 
Please report unacceptable behavior to [Grp-opensourceoffice@adobe.com](mailto:Grp-opensourceoffice@adobe.com).

## Contributor License Agreement

All third-party contributions to this project must be accompanied by a signed Contributor License Agreement (CLA).
This gives Adobe permission to redistribute your contributions as part of the project.
[Sign our CLA](https://opensource.adobe.com/cla.html). You only need to sign it once.

## Contribution requirements

1. Contributions must adhere to [Magento coding standards].
2. Refer to the Magento development team’s [Definition of Done].
   We use these guidelines internally to ensure that we deliver well-tested, well-documented, solid code, and we encourage you to as well!
3. Pull requests (PRs) must be accompanied by a meaningful description of their purpose.
   Comprehensive descriptions increase the chances that a pull request is merged quickly and without additional clarification requests.
4. Commits must be accompanied by meaningful commit messages.
5. PRs that include bug fixing must be accompanied by a step-by-step description of how to reproduce the bug.
6. PRs that include new logic or new features must be submitted along with:

   - Unit/integration test coverage
   - Proposed documentation update.
     For the documentation contribution guidelines, see [DOCUMENTATION_TEMPLATE][].
7. For large features or changes, [open an issue][issue] to discuss first.
   This may prevent duplicate or unnecessary effort, and it may gain you some additional contributors.
8. To report a bug, [open an issue][issue], and follow [guidelines about bugfix issues][issue reporting].
9. All automated tests must pass successfully (all builds on [Travis CI] must be green).

## Fork a repository

To fork a repository on Github, do the following:

1. Navigate to the [MFTF repository].
2. Click **Fork** at the top right.
3. Clone the repo into your development environment and start playing.

Learn more in the [Fork a repo][github fork] GitHub article.

### Update the fork with the latest changes

As community and Magento writers’ changes are merged to the repository, your fork becomes outdated and pull requests might result in conflicts.
To see if your fork is outdated, open the fork page in GitHub and if at the top displays the following message:

__This branch is NUMBER commits behind magento:develop.__

It means your fork must be updated.

There are two ways to update your fork.
The typical way is discussed in the [Syncing a fork][github sync fork] GitHub article.
Make sure to update from the correct branch!

The other way is to create a reverse pull request from the original repository.
Though this method has the downside of inserting unnecessary information into fork commit history.

1. In your fork, click **New pull request**.
2. Click the "switching the base" link and then click **Create pull request**.
3. Provide a descriptive name for your pull request in the provided field.
4. Scroll to the bottom of the page and click **Merge pull request**, then click **Confirm Merge**.

## Create a pull request

First, check the [existing PRs] and make sure you are not duplicating others’ work!

To create a pull request do the following:

1. Create a feature branch for your changes and push those changes to the copy of your repository on GitHub.
 This is the best way to organize and even update your PR.
2. In your repository, click **Pull requests**, and then click **New pull request**.
3. Ensure that you are creating a PR to the **magento/magento2-functional-testing-framework: develop** branch.
 We accept PRs to this branch only.
4. Review the changes, then click **Create pull request**.
 Fill out the form, and click **Create pull request** again to submit the PR—that’s it!

Learn more in the [Creating a pull request][create pr] GitHub article.

After submitting your PR, you can head over to the repository’s [Pull Requests panel][existing PRs] to see your PR along with the others.
Your PR undergoes automated testing, and if it passes, the core team considers it for inclusion in the Magento Functional Testing Framework codebase.
If some tests fail, make the corresponding corrections in your code.

## Report an issue

If you find a bug in Magento Functional Testing Framework code, you can report it by creating an issue in the Magento Functional Testing Framework repository.

Before creating an issue, do the following:

1. Read the [issue reporting guidelines][issue reporting] to learn how to create an issue that can be processed in a timely manner.
2. Check the documentation to make sure the behavior you are reporting is really a bug, not a feature.
3. Check the [existing issues] to make sure you are not duplicating somebody’s work.

To add an issue:

1. [Open a new issue][open new issue]
2. Fill in the **Title** and issue description
3. Click **Submit new issue**

Learn more in the [Creating an issue][create issue] GitHub article.

## Read labels

Refer to the tables with descriptions of each label below.
The labels reflect the status, impact, or which team is working on it.

### Pull request status

Label| Description
---|---
**accept**| The pull request has been accepted to be merged into mainline code.
**reject**| The pull request has been rejected. The most common cases are when the issue has already been fixed in another code contribution, or there is an issue with the code contribution.
**needsUpdate**| We need more information from the PR author to properly prioritize and process the pull request.

### Issue resolution status

Label| Description
---|---
**acknowledged**| We validated the issue and created an internal ticket.
**needsUpdate**| We need more information from the PR author to properly prioritize and process the issue.
**cannot reproduce**| We do not have enough details from the issue description to reproduce the issue.
**non-issue**| We don't think that this is an issue according to the provided information.

### Domains impacted

Label| Description
---|---
**PROD**| Affects the Product team (mostly feature requests or business logic change).
**DOC**| Affects Documentation domain.
**TECH**| Affects Architect Group (mostly to make decisions around technology changes).

### Type

Label| Description
---|---
**bugfix**| The issue or pull request is about fixing a bug.
**enhancement**| The issue or pull request that makes the MFTF even more awesome (for example new features, optimization, refactoring, etc).

[fork]: #fork-a-repository
[issue]: #report-an-issue
[labels]: #read-labels
[pull]: #create-a-pull-request

[create issue]: https://help.github.com/articles/creating-an-issue/
[create pr]: https://help.github.com/articles/creating-a-pull-request/
[Definition of Done]: https://devdocs.magento.com/guides/v2.2/contributor-guide/contributing_dod.html
[DOCUMENTATION_TEMPLATE]: https://github.com/magento/devdocs/blob/master/.github/DOCUMENTATION_TEMPLATE.md
[existing issues]: https://github.com/magento/magento2-functional-testing-framework/issues?q=is%3Aopen+is%3Aissue
[existing PRs]: https://github.com/magento/magento2-functional-testing-framework/pulls?q=is%3Aopen+is%3Apr
[GitHub documentation]: https://help.github.com/articles/syncing-a-fork
[github fork]: https://help.github.com/articles/fork-a-repo/
[github sync fork]: https://help.github.com/articles/syncing-a-fork/
[issue reporting]: https://github.com/magento/magento2-functional-testing-framework/wiki/Issue-reporting-guidelines
[Magento coding standards]: https://devdocs.magento.com/guides/v2.2/coding-standards/bk-coding-standards.html
[Magento Contributor Agreement]: http://www.magento.com/legaldocuments/mca
[MFTF repository]: https://github.com/magento/magento2-functional-testing-framework
[open new issue]: https://github.com/magento/magento2-functional-testing-framework/issues/new
[Travis CI]: https://travis-ci.com/magento/magento2-functional-testing-framework/pull_requests
