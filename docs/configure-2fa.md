# MFTF Configuration for Magento with Two-Factor Authentication (2FA)

## Configure Magento {#config-magento-2fa}

To prepare Magento for MFTF testing when 2FA is enabled, set the following configurations through Magento CLI

### Select `Google Authenticator` as Magento 2FA provider

```bash
bin/magento config:set twofactorauth/general/force_providers google
```

### Set OTP window to `60` seconds

```bash
bin/magento config:set twofactorauth/google/otp_window 60
```

### Set a base32 encoded `secret` for `Google Authenticator` to generate OTP for the default admin user that you set for `MAGENTO_ADMIN_USERNAME` in .env.

```bash
bin/magento security:tfa:google:set-secret <MAGENTO_ADMIN_USERNAME> <OTP_SHARED_SECRET>  
```

## Configure MFTF {#config-mftf-2fa}

Save the same base32 encoded `secret` in MFTF Credential Storages, e.g. `.credentials` file, `HashiCorp Vault` or `AWS Secrets Manager`. 
More details [here](../credentials.md).

The path of the `secret` should be:

```conf
magento/tfa/OTP_SHARED_SECRET
```

## GetOTP {#getOTP}

One-time password (OTP) is required when an admin user logs in to Magento Admin page. 
Use action `getOTP` [Reference](../test/actions.md#getotp) to generate the code and use it for the `Authenticator code` text field in 2FA - Google Auth page.

Note:
You will need to set the `secret` for any non default admin users first before using `getOTP`. For example

```xml
<magentoCLI command="security:tfa:google:set-secret admin2 {{_CREDS.magento/tfa/OTP_SHARED_SECRET}}" stepKey="setSecret"/>
```
