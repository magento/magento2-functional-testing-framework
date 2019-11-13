# Credentials

When you test functionality that involves external services such as UPS, FedEx, PayPal, or SignifyD,
use the MFTF credentials feature to hide sensitive [data][] like integration tokens and API keys.

Currently the MFTF supports two types of credential storage:

-  **.credentials file**
-  **HashiCorp vault**

## Configure File Storage

The MFTF creates a sample file for credentials during [initial setup][]: `magento2/dev/tests/acceptance/.credentials.example`.
The file contains an example list of keys for fields that can require credentials.

### Create `.credentials`

To make the MFTF process the file with credentials, in the command line, navigate to `magento2/dev/tests/acceptance/` and rename `.credentials.example` to `.credentials`.

```bash
cd dev/tests/acceptance/
```

```bash
cp .credentials.example .credentials
```

### Add `.credentials` to `.gitignore`

Verify that the file is excluded from tracking by `.gitignore` (unless you need this behavior):

```bash
git check-ignore .credentials
```

The command outputs the path if the file is excluded:

```terminal
.credentials
```

### Define sensitive data in the `.credentials` file

Open the `.credentials` file and, for Magento core credentials, uncomment the fields you want to use and add your values:

```conf
...
# Credentials for the USPS service
magento/carriers_usps_userid=usps_test_user
magento/carriers_usps_password=Lmgxvrq89uPwECeV

# Credentials for the DHL service
#magento/carriers_dhl_id_us=dhl_test_user
#magento/carriers_dhl_password_us=Mlgxv3dsagVeG
....
```

Or add new key/value pairs for your own credentials. The keys use the following format:

```conf
<vendor>/<key_name>=<key_value>
```

<div class="bs-callout bs-callout-info" markdown="1">
The `/` symbol is not supported in a `key_name` other than the one after your vendor or extension name.
</div>

Otherwise you are free to use any other `key_name` you like, as they are merely the keys to reference from your tests.

```conf
# Credentials for the MyAwesome service
vendor/my_awesome_service_token=rRVSVnh3cbDsVG39oTMz4A
```

## Configure Vault Storage

Hashicorp vault secures, stores, and tightly controls access to data in modern computing.
It provides advanced data protection for your testing credentials.

The MFTF works with both `vault enterprise` and `vault open source` that use `KV Version 2` secret engine.

### Install vault CLI

Download and install vault CLI tool if you want to run or develop MFTF tests locally. [Download Vault][Download Vault]

### Authenticate to vault via vault CLI

Authenticate to vault server via the vault CLI tool: [Login Vault][Login Vault].

```bash
vault login -method -path
```

**Do not** use `-no-store` command option, as the MFTF will rely on the persisted token in the token helper (usually the local filesystem) for future API requests.

### Store secrets in vault

The MFTF uses the `KV Version 2` secret engine for secret storage.
More information for working with `KV Version 2` can be found in [Vault KV2][Vault KV2].

#### Secrets path and key convention

The path and key for secret data must follow the format:

```conf
<SECRETS_BASE_PATH>/mftf/<VENDOR>/<SECRET_KEY>
```

```conf
# Secret path and key for carriers_usps_userid
secret/mftf/magento/carriers_usps_userid

# Secret path and key for carriers_usps_password
secret/mftf/magento/carriers_usps_password
```

#### Write secrets to vault

You can use vault CLI or API to write secret data (credentials, etc) to vault. Here is a CLI example:

```bash
vault kv put secret/mftf/magento/carriers_usps_userid carriers_usps_userid=usps_test_user
vault kv put secret/mftf/magento/carriers_usps_password carriers_usps_password=Lmgxvrq89uPwECeV
```

### Setup MFTF to use vault

Add vault configuration environment variables [`CREDENTIAL_VAULT_ADDRESS`][] and [`CREDENTIAL_VAULT_SECRET_BASE_PATH`][]
from `etc/config/.env.example` in `.env`.
Set values according to your vault server configuration.

```conf
# Default vault dev server
CREDENTIAL_VAULT_ADDRESS=http://127.0.0.1:8200
CREDENTIAL_VAULT_SECRET_BASE_PATH=secret
```

## Configure both File Storage and Vault Storage

It is possible and sometimes useful to setup and use both `.credentials` file and vault for secret storage at the same time.
In this case, the MFTF tests are able to read secret data at runtime from both storage options, but the local `.credentials` file will take precedence.

<!-- {% raw %} -->

## Use credentials in a test

Credentials can be used in actions: [`fillField`][], [`magentoCLI`][], and [`createData`][].

Define the value as a reference to the corresponding key in the credentials file or vault such as `{{_CREDS.my_data_key}}`:

-  `_CREDS` is an environment constant pointing to the `.credentials` file
-  `my_data_key` is a key in the the `.credentials` file or vault that contains the value to be used in a test step
   - for File Storage, ensure your key contains the vendor prefix, i.e. `vendor/my_data_key`

For example, to reference secret data in the [`fillField`][] action, use the `userInput` attribute using a typical File Storage:

```xml
<fillField stepKey="FillApiToken" selector=".api-token" userInput="{{_CREDS.vendor/my_data_key}}" />
```

<!-- {% endraw %} -->

## Implementation details

The generated tests do not contain credentials values.
The MFTF dynamically retrieves, encrypts, and decrypts the sensitive data during test execution.
Decrypted credentials do not appear in the console, error logs, or [test reports][].
The decrypted values are only available in the `.credentials` file or within vault.

<div class="bs-callout bs-callout-info">
The MFTF tests delivered with Magento application do not use credentials and do not cover external services, because of sensitivity of the data.
</div>

<!-- Link definitions -->
[`fillField`]: test/actions.md#fillfield
[`magentoCLI`]: test/actions.md#magentocli
[`createData`]: test/actions.md#createdata
[data]: data.md
[initial setup]: getting-started.md
[test reports]: reporting.md
[Download Vault]: https://www.hashicorp.com/products/vault/
[Login Vault]: https://www.vaultproject.io/docs/commands/login.html
[Vault KV2]: https://www.vaultproject.io/docs/secrets/kv/kv-v2.html
[`CREDENTIAL_VAULT_ADDRESS`]: configuration.md#credential_vault_address
[`CREDENTIAL_VAULT_SECRET_BASE_PATH`]: configuration.md#credential_vault_secret_base_path
