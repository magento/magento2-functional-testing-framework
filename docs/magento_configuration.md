# Magento 2 Configuration for MFTF Testing

## Install Magento {#install-magento}

Follow Magento Installation Guide to install Magento either by [Git][magento_install_git] clone or by [Composer][magento_install_composer].

## Prepare Magento {#prepare-magento}

Configure the following settings in Magento as described below.

### WYSIWYG settings {#wysiwyg-settings}

A Selenium web driver cannot enter data to fields with WYSIWYG.

To disable the WYSIWYG and enable the web driver to process these fields as simple text areas:

1. Log in to the Magento Admin as an administrator.
2. Navigate to **Stores** > Settings > **Configuration** > **General** > **Content Management**.
3. In the WYSIWYG Options section set the **Enable WYSIWYG Editor** option to **Disabled Completely**.
4. Click **Save Config**.

<div class="bs-callout bs-callout-tip">
When you want to test the WYSIWYG functionality, re-enable WYSIWYG in your test suite.
</div>

### Security settings {#security-settings}

To enable the **Admin Account Sharing** setting, to avoid unpredictable logout during a testing session, and disable the **Add Secret Key in URLs** setting, to open pages using direct URLs:

1. Navigate to **Stores** > Settings > **Configuration** > **Advanced** > **Admin** > **Security**.
2. Set **Admin Account Sharing** to **Yes**.
3. Set **Add Secret Key to URLs** to **No**.
4. Click **Save Config**.

### Nginx settings {#nginx-settings}

If Nginx Web server is used on your development environment then **Use Web Server Rewrites** setting in **Stores** > Settings > **Configuration** > **Web** > **Search Engine Optimization** must be set to **Yes**.

To be able to run Magento command line commands in tests add the following location block to Nginx configuration file:

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

In the `dev/tests/acceptance` directory, run the following command to enable the MFTF to send Magento CLI commands to your Magento instance.

 ```bash
cp dev/tests/acceptance/.htaccess.sample dev/tests/acceptance/.htaccess
```

#### Optionally copy command.php if run MFTF in standalone mode

Copy the `etc/config/command.php` file from MFTF into your Magento installation at `<magento root directory>/dev/tests/acceptance/utils/`.
Create the `utils/` directory, if you didn't find it.

<!-- Link definitions -->

[magento_install_composer]: https://devdocs.magento.com/guides/v2.3/install-gde/composer.html
[magento_install_git]: https://devdocs.magento.com/guides/v2.3/install-gde/prereq/dev_install.html
