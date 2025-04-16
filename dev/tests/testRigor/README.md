# Test Automation with testRigor for Magento

This document provides step-by-step instructions for setting up test automation for Magento using testRigor. It covers creating an account, setting up a test suite, and running tests using the testRigor CLI.

## Table of Contents

- [Creating an Account on testRigor](#creating-an-account-on-testrigor)
- [Running Tests with the CLI](#running-tests-with-the-cli)
- [Additional Resources](#additional-resources)

## Creating an Account on testRigor

1. **Visit the testRigor website:**

   - Go to [testRigor](https://www.testrigor.com/).

2. **Sign up for a new account:**

   - Click on the "Sign Up" button on the top right corner.
   - Select the "Public Open Source" version.
   - Fill in the required details and follow the instructions to complete the registration.

3. **Verify your email and log in:**

   - Check your email inbox for a verification email from testRigor.
   - Click on the verification link to activate your account.
   - Once your account is activated, log in.

4. **Create a test suite:**
   - After logging into your account, create a test suite.

## Running Tests with the CLI

1. **Prerequisites:**

   - **None!** MFTF will automatically download and install all required dependencies (Node.js and TestRigor CLI) on first run.
   - Everything is installed locally within the project, requiring zero manual setup.
   - The framework is fully self-contained and ready for CI/CD environments.

2. **Obtain Required Parameters:**

   - **Test Suite ID:** You can obtain the Test Suite ID in the URL of your test suite. If the URL is `https://app.testrigor.com/test-suites/12345`, then `12345` is your Test Suite ID.
   - **Auth Token:** You can obtain your token from the "CI/CD integration" section on testRigor. Look for "auth-token" and copy the value next to it, which will be in the format `########-####-####-####-############`.

3. **Set Parameters in `.env` file:**

   - Before running the tests, create a .env file on the testRigor directory and set the following variables to the parameters you obtained:
     - `MAGENTO_TEST_SUITE_ID`: Set this variable to your Test Suite ID.
     - `MAGENTO_AUTH_TOKEN`: Set this variable to your auth token.
     - `MAGENTO_BASE_URL`: Set this variable to the URL where Magento is running locally.

   Example `.env` file:
   ```
   MAGENTO_TEST_SUITE_ID=12345
   MAGENTO_AUTH_TOKEN=########-####-####-####-############
   MAGENTO_BASE_URL=http://localhost:8080
   ```

4. **Run Tests:**

   ```bash
   bin/mftf run:testrigor
   ```

   The command will:
   - Automatically check if Node.js and TestRigor CLI are installed
   - Download and install them locally if not found (no manual installation needed!)
   - Load environment variables from the `.env` file in the project root
   - Execute your TestRigor test suite

5. **View Test Results:**
   - You can view the results on testRigor by opening the link shown in the terminal.

## Troubleshooting

### Automatic Installation Issues

MFTF handles all installations automatically. If you encounter issues:

1. **Check for curl:**
   ```bash
   curl --version
   ```
   The framework uses `curl` to download Node.js. Most systems have it pre-installed.

2. **Verify disk space:**
   Ensure you have at least 100MB of free disk space for Node.js and dependencies.

3. **Check file permissions:**
   The framework creates a `.mftf-tools` directory in the project root. Ensure the directory is writable.

4. **Manual cleanup:**
   If installation fails, try removing cached files:
   ```bash
   rm -rf .mftf-tools node_modules
   ```
   Then run the command again.

5. **Using system Node.js:**
   If you already have Node.js 18+ installed system-wide, MFTF will detect and use it automatically, skipping the download.

## Additional Resources

- [testRigor Documentation](https://docs.testrigor.com/)
- [testRigor Command Line Documentation](https://testrigor.com/command-line/)
