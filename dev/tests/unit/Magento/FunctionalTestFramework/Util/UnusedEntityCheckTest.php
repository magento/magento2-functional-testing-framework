<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use Exception;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;
use Magento\FunctionalTestingFramework\StaticCheck\UnusedEntityCheck;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;

class UnusedEntityCheckTest extends MagentoTestCase
{
   
    public function testUnusedEntityFilesCheck()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $result =  $unusedEntityCheck->unusedEntities();
        $this->assertIsArray($result);
    }

    public function testUnusedActiongroupFiles()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $actionGroupFiles = ['DeprecationCheckActionGroup' =>
            '/verification/DeprecationCheckModule/ActionGroup/DeprecationCheckActionGroup.xml',
            'ActionGroupWithMultiplePausesActionGroup'=>
            '/verification/PauseCheckModule/ActionGroup/ActionGroupWithMultiplePausesActionGroup.xml',
            'ActionGroupWithNoPauseActionGroup'=>
            '/verification/PauseCheckModule/ActionGroup/ActionGroupWithNoPauseActionGroup.xml'];
        $result =  $unusedEntityCheck->unusedActionEntity($domDocument, [], [], $actionGroupFiles, []);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testUnusedActiongroupFilesReturnedWhenActionXmlFilesAreNotEmpty()
    {
        $scriptUtil = new ScriptUtil();
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $actionGroupXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "ActionGroup");
        $actionGroupFiles = ['DeprecationCheckActionGroup' =>
            '/verification/DeprecationCheckModule/ActionGroup/DeprecationCheckActionGroup.xml',
            'ActionGroupWithMultiplePausesActionGroup'=>
            '/verification/PauseCheckModule/ActionGroup/ActionGroupWithMultiplePausesActionGroup.xml',
            'ActionGroupWithNoPauseActionGroup' =>
            '/verification/PauseCheckModule/ActionGroup/ActionGroupWithNoPauseActionGroup.xml'];
        $result =  $unusedEntityCheck->unusedActionEntity(
            $domDocument,
            $actionGroupXmlFiles,
            [],
            $actionGroupFiles,
            []
        );
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testUnusedSectionFiles()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $section = [
            'DeprecationCheckSection' => '/verification/DeprecationCheckModule/Section/DeprecationCheckSection.xml',
            'DeprecatedSection' => '/verification/TestModule/Section/DeprecatedSection.xml',
            'LocatorFunctionSection' => '/verification/TestModule/Section/LocatorFunctionSection.xml',
            'SampleSection' => '/verification/TestModuleMerged/Section/MergeSection.xml'
        ];
        $result=$unusedEntityCheck->unusedSectionEntity($domDocument, [], [], [], $section, []);
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }

    public function testUnusedSectionFilesReturnedWhenSectionXmlFilesAreNotEmpty()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $scriptUtil = new ScriptUtil();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $sectionXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "Section");

        $domDocument = new \DOMDocument();
        $section = [
            'DeprecationCheckSection' => '/verification/DeprecationCheckModule/Section/DeprecationCheckSection.xml',
            'DeprecatedSection' => '/verification/TestModule/Section/DeprecatedSection.xml',
            'LocatorFunctionSection' => '/verification/TestModule/Section/LocatorFunctionSection.xml',
            'SampleSection' => '/verification/TestModuleMerged/Section/MergeSection.xml'
        ];
        $result =  $unusedEntityCheck->unusedSectionEntity($domDocument, $sectionXmlFiles, [], [], $section, []);
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
    }

    public function testUnusedPageFiles()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $page = [
            'DeprecationCheckPage' => '/verification/DeprecationCheckModule/Page/DeprecationCheckPage.xml',
            'DeprecatedPage' => '/verification/TestModule/Page/DeprecatedPage.xml',
            'AdminOneParamPage' => '/verification/TestModule/Page/SamplePage/AdminOneParamPage.xml',
            'AdminPage' => '/verification/TestModule/Page/SamplePage/AdminPage.xml',
            'ExternalPage' => '/verification/TestModule/Page/SamplePage/ExternalPage.xml',
            'NoParamPage' => '/verification/TestModule/Page/SamplePage/NoParamPage.xml'
        ];
        $result =  $unusedEntityCheck->unusedPageEntity($domDocument, [], [], $page, []);
        $this->assertIsArray($result);
        $this->assertCount(6, $result);
    }

    public function testUnusedPageFilesReturnedWhenPageXmlFilesPassedAreNotEmpty()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $scriptUtil = new ScriptUtil();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $pageXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "Page");
        $page = [
            'DeprecationCheckPage' => '/verification/DeprecationCheckModule/Page/DeprecationCheckPage.xml',
            'DeprecatedPage' => '/verification/TestModule/Page/DeprecatedPage.xml',
            'AdminOneParamPage' => '/verification/TestModule/Page/SamplePage/AdminOneParamPage.xml',
            'AdminPage' => '/verification/TestModule/Page/SamplePage/AdminPage.xml',
            'ExternalPage' => '/verification/TestModule/Page/SamplePage/ExternalPage.xml',
            'NoParamPage' => '/verification/TestModule/Page/SamplePage/NoParamPage.xml'
        ];
        $result =  $unusedEntityCheck->unusedPageEntity($domDocument, $pageXmlFiles, [], $page, []);
        $this->assertIsArray($result);
        $this->assertCount(6, $result);
    }

    public function testUnusedData()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $data = [
            "simpleData" =>
            [
                "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
            ],
    
            "uniqueData" => [
                
                    "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
            ],
    
            "offset"=>
                [
                    "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
                ]
        ];
        $result=$unusedEntityCheck->unusedPageEntity($domDocument, [], [], $data, []);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testUnusedDataReturnedWhenCreateDataEntityAreNotEmpty()
    {
        $unusedEntityCheck = new UnusedEntityCheck();
        $domDocument = new \DOMDocument();
        $scriptUtil = new ScriptUtil();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $dataXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "Data");
        $data = [
            "simpleData" =>
            [
                "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
            ],
    
            "uniqueData" => [
                
                    "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
            ],
    
            "offset" =>
                [
                    "dataFilePath" => "/verification/TestModule/Data/ReplacementData.xml"
                ]
        ];
        $result = $unusedEntityCheck->unusedPageEntity($domDocument, $dataXmlFiles, [], $data, []);
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }
}
