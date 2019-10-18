#Git vs Composer Installation of Magento with MFTF


###GitHub Installation

If you are planning on contributing a PR to the Magento 2 codebase, you can download Magento 2 from GitHub. Contribution to the codebase is done using the 'fork and pull' model where contributors maintain their own fork of the repo. This repo is then used to submit a pull request to the base repo.

Install guide: [GitHub Installation](https://devdocs.magento.com/mftf/docs/getting-started.html)

###Composer based Installation

Composer install downloads released packages of Magento 2 from the composer repo [https://repo.magento.com](https://repo.magento.com).

All Magento modules and their MFTF tests are put under `<vendor>` directory for convenience of 3rd party developers. With this setup, you can keep your custom modules separate from the core. You can also develop modules in a separate VCS repository and add them to your `composer.json` which will allow them to be installed into the `vendor` directory.

Install guide: [Composer based Installation](https://devdocs.magento.com/guides/v2.3/install-gde/composer.html)


###Managing modules - Composer vs GitHub

####Via GitHub:

Cloning Magento 2 git repository is a way of installing when you don't have to worry frequently about matching the codebase with production. Your version control system generally holds and manages your `app/code` folder and you can do manual ad-hoc development here.

####Via composer:

Magento 2 advocates the use of composer for managing modules. When you install a module through composer, it is added to `vendor/<vendor-name>/<module>`

If you are developing your own module or adding MFTF tests to the module, you should not edit `vendor` because a composer update could clobber your changes. Instead, you can override a module under `vendor`, by adding files or cloning your module specific git repo to `app/code/<vendor-name>/<module>`.

If you want to distribute the module and its tests, you can initialize a git repo and create a [composer package](https://devdocs.magento.com/guides/v2.3/extension-dev-guide/package/package_module.html). In this way others will be able to download and install your module and access your tests as a composer package, in their `<vendor>` folder.


###MFTF test artifacts location

- For GitHub installation, MFTF test artifacts are located at `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/`. This is the directory to create new tests or maintain existing ones.

- For Composer based installation, MFTF test artifacts are located at `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/`. This is the directory to run tests fetched by Composer.

The file structure under both paths is the same as below:

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

###How ModuleResolver reads modules

In either of the installations, all tests and test data are read and merged by MFTF's ModuleResolver in the order indicated below:

1. `<magento_root>/app/code/<vendor_name>/<module_name>/Test/Mftf/`
2. `<magento_root>/vendor/<vendor_name>/<module_name>/Test/Mftf/`

###Conclusion

There are no differences from MFTF's perspective between having the test artifacts in `app/code` or in `/vendor` as it reads artifacts from both paths. It works the same. Composer based install may benefit teams when there's a need to match file systems in dev and production.

If you are a contributing developer with an understanding of Git and Composer commands, you can choose the GitHub installation method instead.



