# Magento 2 configuration for MFTF Testing

## Prepare Magento {#prepare-magento}

Configure the settings in Magento as described below.

### WYSIWYG settings {#wysiwyg-settings}

By default, the Selenium web driver cannot enter data to text areas with WYSIWYG editors.

To disable the WYSIWYG and enable the web driver to process these fields as simple text areas:

1. Log in to the Magento Admin as an administrator.
1. Navigate to **Stores** > Settings > **Configuration** > **General** > **Content Management**.
1. In the WYSIWYG Options section set the **Enable WYSIWYG Editor** option to **Disabled Completely**.
1. Click **Save Config**.

<div class="bs-callout bs-callout-tip">
When you want to test the WYSIWYG functionality, re-enable WYSIWYG in your test suite.
</div>

### Security settings {#security-settings}

Ao avoid unpredictable logout during a testing session, enable the **Admin Account Sharing** setting, and to open pages using direct URLs, disable the **Add Secret Key in URLs** setting:

1. Navigate to **Stores** > Settings > **Configuration** > **Advanced** > **Admin** > **Security**.
1. Set **Admin Account Sharing** to **Yes**.
1. Set **Add Secret Key to URLs** to **No**.
1. Click **Save Config**.

### Nginx settings {#nginx-settings}

If the nginx web server is used on your development environment then **Use Web Server Rewrites** setting in **Stores** > Settings > **Configuration** > **Web** > **Search Engine Optimization** must be set to **Yes**.

To run Magento command line commands in tests, add the following location block to the nginx configuration file:

```conf
location ~* ^/dev/tests/acceptance/utils($|/) {
  root $MAGE_ROOT;
  location ~ ^/dev/tests/acceptance/utils/command.php {
      fastcgi_pass   fastcgi_backend;
      fastcgi_index  index.php;
      fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
      include        fastcgi_params;
  }
}
```

### Enable the Magento CLI commands {#add-cli-commands}

#### Set web server access permission

From the `dev/tests/acceptance` directory, run the following command to enable the MFTF to send Magento CLI commands to your Magento instance.

 ```bash
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess
```

#### Optionally copy command.php if run MFTF in standalone mode

Copy the `etc/config/command.php` file from MFTF into your Magento installation at `<magento root directory>/dev/tests/acceptance/utils/`.
Create the `utils/` directory, if you do not find it.

<!-- Link definitions -->

[magento_install_composer]: https://devdocs.magento.com/guides/v2.3/install-gde/composer.html
[magento_install_git]: https://devdocs.magento.com/guides/v2.3/install-gde/prereq/dev_install.html
