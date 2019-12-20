# Git vs Composer installation of Magento with MFTF

Depending on how you plan to use Magnto code, there are different options for installing Magento.

## GitHub Installation

If you are contributing a pull request to the Magento 2 codebase, download Magento 2 from our GitHub repository. Contribution to the codebase is done using the 'fork and pull' model where contributors maintain their own fork of the repo. This repo is then used to submit a pull request to the base repo.

Install guide: [GitHub Installation][]

## Composer based Installation

A Composer install downloads released packages of Magento 2 from the composer repo [https://repo.magento.com](https://repo.magento.com).

All Magento modules and their MFTF tests are put under `<vendor>` directory, for convenience of 3rd party developers. With this setup, you can keep your custom modules separate from core modules. You can also develop modules in a separate VCS repository and add them to your `composer.json` which installs them into the `vendor` directory.

Install guide: [Composer based Installation][]

## MFTF Installation

After installing your Magento project in either of the above ways, the composer dependency `magento/magento2-functional-testing-framework` downloads and installs MFTF. MFTF is embedded in your Magento 2 installation and will cover your project with functional tests.

If you want to contribute a pull request into MFTF codebase, you will need to install MFTF in the [Standalone][] mode.

## Managing modules - Composer vs GitHub

### Via GitHub

Cloning the Magento 2 git repository is a way of installing where you do not have to worry about matching your codebase with production. Your version control system generally holds and manages your `app/code` folder and you can do manual, ad-hoc development here.

### Via Composer

Magento advocates the use of composer for managing modules. When you install a module through composer, it is added to `vendor/<vendor-name>/<module>`.

When developing your own module or adding MFTF tests to a module, you should not edit in `vendor` because a composer update could overwrite your changes. Instead, overwrite a module under `vendor` by adding files or cloning your module-specific Git repo to `app/code/<vendor-name>/<module>`.

To distribute the module and its tests, you can initialize a git repo and create a [composer package][]. In this way others will be able to download and install your module and access your tests as a composer package, in their `<vendor>` folder.

## MFTF test materials location

-  For GitHub installations, MFTF test materials are located in `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/`. This is the directory for new tests or to maintain existing ones.
-  For Composer-based installations, MFTF test materials are located at `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/`. This is the directory to run tests fetched by Composer.

The file structure under both paths is the same:

```tree
<Path>
├── ActionGroup
│   └── ...
├── Data
│   └── ...
├── Metadata
│   └── ...
├── Page
│   └── ...
├── Section
│   └── ...
└── Test
    └── ...
```

## How ModuleResolver reads modules

With either type of installation, all tests and test data are read and merged by MFTF's ModuleResolver in this order:

1. `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/`
1. `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/`

## Conclusion

There is no difference between having the test materials in `app/code` or in `/vendor`: it works the same. Composer-based installs may benefit teams when there is a need to match file systems in `development` and `production`.

If you are a contributing developer with an understanding of Git and Composer commands, you can choose the GitHub installation method instead.

<!-- Link definitions -->

[Composer based Installation]: https://devdocs.magento.com/guides/v2.3/install-gde/composer.html
[GitHub Installation]: https://devdocs.magento.com/guides/v2.3/install-gde/prereq/dev_install.html
[Standalone]: ../getting-started.html#set-up-a-standalone-mftf
[composer package]: https://devdocs.magento.com/guides/v2.3/extension-dev-guide/package/package_module.html
