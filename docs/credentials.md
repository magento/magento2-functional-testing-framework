# Credentials

When you test functionality that involves external services such as UPS, FedEx, PayPal, or SignifyD,
use the MFTF credentials feature to hide sensitive [data][] like integration tokens and API keys.

Currently the MFTF supports three types of credential storage:

-  **.credentials file**
-  **HashiCorp Vault**
-  **AWS Secrets Manager**

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

## Configure AWS Secrets Manager

AWS Secrets Manager offers secret management that supports:
- Secret rotation with built-in integration for Amazon RDS, Amazon Redshift, and Amazon DocumentDB
- Fine-grained policies and permissions
- Audit secret rotation centrally for resources in the AWS Cloud, third-party services, and on-premises

### Prerequisites

#### Use AWS Secrets Manager from your own AWS account

- An AWS account with Secrets Manager service
- An IAM user with AWS Secrets Manager access permission

#### Use AWS Secrets Manager in CI/CD

- AWS account ID where the AWS Secrets Manager service is hosted
- Authorized CI/CD EC2 instances with AWS Secrets Manager service access IAM role attached

### Store secrets in AWS Secrets Manager

#### Secrets format

`Secret Name` and `Secret Value` are two key pieces of information for creating a secret. 

`Secret Value` can be either plaintext or key/value pairs in JSON format.  

`Secret Name` must use the following format:

```conf
mftf/<VENDOR>/<YOUR/SECRET/KEY>
```

`Secret Value` can be stored in two different formats: plaintext or key/value pairs.

For plaintext format, `Secret Value` can be any string you want to secure.

For key/value pairs format, `Secret Value` is a key/value pair with `key` the same as `Secret Name` without `mftf/<VENDOR>/` prefix,  which is `<YOUR/SECRET/KEY>`, and value can be any string you want to secure.

##### Create Secrets using AWS CLI

```bash
aws secretsmanager create-secret --name "mftf/magento/shipping/carriers_usps_userid" --description "Carriers USPS user id" --secret-string "1234567"
```

##### Create Secrets using AWS Console

- Sign in to the AWS Secrets Manager console
- Choose Store a new secret
- In the Select secret type section, specify "Other type of secret"
- For `Secret Name`, `Secret Key` and `Secret Value` field, for example, to save the same secret in key/value JSON format, you should use
 
```conf
# Secret Name
mftf/magento/shipping/carriers_usps_userid

# Secret Key
shipping/carriers_usps_userid

# Secret Value
1234567
```

### Setup MFTF to use AWS Secrets Manager

To use AWS Secrets Manager, the AWS region to connect to is required. You can set it through environment variable [`CREDENTIAL_AWS_SECRETS_MANAGER_REGION`][] in `.env`.

MFTF uses the recommended [Default Credential Provider Chain][credential chain] to establish connection to AWS Secrets Manager service. 
You can setup credentials according to [Default Credential Provider Chain][credential chain] and there is no MFTF specific setup required. 
Optionally, however, you can explicitly set AWS profile through environment variable [`CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE`][] in `.env`.

```conf
# Sample AWS Secrets Manager configuration
CREDENTIAL_AWS_SECRETS_MANAGER_REGION=us-east-1
CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE=default
```

### Optionally set CREDENTIAL_AWS_ACCOUNT_ID environment variable
 
In case AWS credentials cannot resolve to a valid AWS account, full AWS KMS ([Key Management Service][]) key ARN ([Amazon Resource Name][]) is required.
You will also need to set `CREDENTIAL_AWS_ACCOUNT_ID` environment variable so that MFTF can construct the full ARN. This is mostly used for CI/CD.

```bash
export CREDENTIAL_AWS_ACCOUNT_ID=<Your_12_Digits_AWS_Account_ID>
```

## Configure multiple credential storage

It is possible and sometimes useful to setup and use multiple credential storage at the same time.
In this case, the MFTF tests are able to read secret data at runtime from all storage options, in this case MFTF use the following precedence:

```
.credentials File > HashiCorp Vault > AWS Secrets Manager
```
<!-- {% raw %} -->

## Use credentials in a test

Credentials can be used in actions: [`fillField`][], [`magentoCLI`][], and [`createData`][].

Define the value as a reference to the corresponding key in the credentials file or vault such as `{{_CREDS.my_data_key}}`:

-  `_CREDS` is an environment constant pointing to the `.credentials` file
-  `my_data_key` is a key in the the `.credentials` file or vault that contains the value to be used in a test step
   - for File Storage, ensure your key contains the vendor prefix, which is `vendor/my_data_key`

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
[credential chain]: https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
[`CREDENTIAL_AWS_SECRETS_MANAGER_PROFILE`]: configuration.md#credential_aws_secrets_manager_profile
[`CREDENTIAL_AWS_SECRETS_MANAGER_REGION`]: configuration.md#credential_aws_secrets_manager_region
[Key Management Service]: https://aws.amazon.com/kms/
[Amazon Resource Name]: https://docs.aws.amazon.com/general/latest/gr/aws-arns-and-namespaces.html