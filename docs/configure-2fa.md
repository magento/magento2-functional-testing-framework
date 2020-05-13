# Configuring MFTF for Two-Factor Authentication (2FA)

Using two-factor authentication (2FA) with MFTF is possible with some configurations settings in Magento.
In this document, we will use Google as the authentication provider.

## Configure Magento {#config-magento-2fa}

To prepare Magento for MFTF testing when 2FA is enabled, set the following configurations through the Magento CLI.

First, select `Google Authenticator` as Magento's 2FA provider:

```bash
bin/magento config:set twofactorauth/general/force_providers google
```

Now set the OTP window to `60` seconds:

```bash
bin/magento config:set twofactorauth/google/otp_window 60
```

Set a base32-encoded `secret` for `Google Authenticator` to generate a OTP for the default admin user that you set for `MAGENTO_ADMIN_USERNAME` in `.env`:

```bash
bin/magento security:tfa:google:set-secret <MAGENTO_ADMIN_USERNAME> <OTP_SHARED_SECRET>  
```

## Configure the MFTF {#config-mftf-2fa}

Save the same base32-encoded `secret` in a MFTF credential storage, e.g. `.credentials` file, `HashiCorp Vault` or `AWS Secrets Manager`. 
More details are [here](./credentials.md).

The path of the `secret` should be:

```conf
magento/tfa/OTP_SHARED_SECRET
```

## GetOTP {#getOTP}

A one-time password (OTP) is required when an admin user logs into the Magento admin. 
Use the action `getOTP` [Reference](./test/actions.md#getotp) to generate the code and use it for the `Authenticator code` text field in 2FA - Google Auth page.

Note:
You will need to set the `secret` for any non-default admin users first, before using `getOTP`. For example:

```xml
<magentoCLI command="security:tfa:google:set-secret admin2 {{_CREDS.magento/tfa/OTP_SHARED_SECRET}}" stepKey="setSecret"/>
```
