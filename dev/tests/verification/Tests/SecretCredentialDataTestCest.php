<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Tests\Verification;

use Magento\FunctionalTestingFramework\AcceptanceTester;

/**
 */
class SecretCredentialDataTestCest
{
    /**
     * @Features({"AdminNotification"})
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function secretCredentialDataTest(AcceptanceTester $I)
    {
        $createProductWithFieldOverridesUsingHardcodedData1Fields['qty'] = "123";

        $createProductWithFieldOverridesUsingHardcodedData1Fields['price'] = "12.34";

        $I->comment("[createProductWithFieldOverridesUsingHardcodedData1] create '_defaultProduct' entity");
        $I->createEntity(
            "createProductWithFieldOverridesUsingHardcodedData1",
            "test",
            "_defaultProduct",
            [],
            $createProductWithFieldOverridesUsingHardcodedData1Fields
        );

        $createProductWithFieldOverridesUsingSecretCredData1Fields['qty'] =
            $I->getSecret("payment_authorizenet_trans_key");

        $createProductWithFieldOverridesUsingSecretCredData1Fields['price'] =
            $I->getSecret("carriers_dhl_account_eu");

        $I->comment("[createProductWithFieldOverridesUsingSecretCredData1] create '_defaultProduct' entity");
        $I->createEntity(
            "createProductWithFieldOverridesUsingSecretCredData1",
            "test",
            "_defaultProduct",
            [],
            $createProductWithFieldOverridesUsingSecretCredData1Fields
        );

        $I->fillField("#username", "Hardcoded"); // stepKey: fillFieldUsingHardCodedData1
        $I->fillSecretField("#username", $I->getSecret("carriers_dhl_id_eu"));
        // stepKey: fillFieldUsingSecretCredData1
        $magentoCliUsingHardcodedData1 = $I->magentoCLI("config:set cms/wysiwyg/enabled 0");
        // stepKey: magentoCliUsingHardcodedData1
        $I->comment($magentoCliUsingHardcodedData1);

        $magentoCliUsingSecretCredData1 = $I->magentoCLI("config:set cms/wysiwyg/enabled " .
            $I->getSecret("payment_authorizenet_login"));
        // stepKey: magentoCliUsingSecretCredData1
        $I->comment($magentoCliUsingSecretCredData1);
    }
}
