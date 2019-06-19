<?php
namespace Magento\AcceptanceTest\_default\Backend;

use Magento\FunctionalTestingFramework\AcceptanceTester;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use \Codeception\Util\Locator;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Model\SeverityLevel;
use Yandex\Allure\Adapter\Annotation\TestCaseId;

/**
 */
class SecretCredentialDataTestCest
{
    /**
     * @Features({"AdminNotification"})
     * @Parameter(name = "AcceptanceTester", value="$I")
     * @param AcceptanceTester $I
     * @return void
     * @throws \Exception
     */
    public function SecretCredentialDataTest(AcceptanceTester $I)
    {
        $createProductWithFieldOverridesUsingHardcodedData1Fields['qty'] = "123";
        $createProductWithFieldOverridesUsingHardcodedData1Fields['price'] = "12.34";
        $I->amGoingTo("create entity that has the stepKey: createProductWithFieldOverridesUsingHardcodedData1");
        PersistedObjectHandler::getInstance()->createEntity(
            "createProductWithFieldOverridesUsingHardcodedData1",
            "test",
            "_defaultProduct",
            [],
            $createProductWithFieldOverridesUsingHardcodedData1Fields
        );
        $createProductWithFieldOverridesUsingSecretCredData1Fields['qty'] = CredentialStore::getInstance()->getSecret("payment_authorizenet_trans_key");
        $createProductWithFieldOverridesUsingSecretCredData1Fields['price'] = CredentialStore::getInstance()->getSecret("carriers_dhl_account_eu");
        $I->amGoingTo("create entity that has the stepKey: createProductWithFieldOverridesUsingSecretCredData1");
        PersistedObjectHandler::getInstance()->createEntity(
            "createProductWithFieldOverridesUsingSecretCredData1",
            "test",
            "_defaultProduct",
            [],
            $createProductWithFieldOverridesUsingSecretCredData1Fields
        );
        $I->fillField("#username", "Hardcoded");
        $I->fillSecretField("#username", CredentialStore::getInstance()->getSecret("carriers_dhl_id_eu"));
        $magentoCliUsingHardcodedData1 = $I->magentoCLI("config:set cms/wysiwyg/enabled 0");
        $I->comment($magentoCliUsingHardcodedData1);
        $magentoCliUsingSecretCredData1 = $I->magentoCLI("config:set cms/wysiwyg/enabled " . CredentialStore::getInstance()->getSecret("payment_authorizenet_login"));
        $I->comment($magentoCliUsingSecretCredData1);
    }
}
