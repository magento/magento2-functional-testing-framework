<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;

class TestGenerator
{

    const REQUIRED_ENTITY_REFERENCE = 'createDataKey';
    const GENERATED_DIR = '_generated';

    /**
     * Path to the export dir.
     *
     * @var string
     */
    private $exportDirectory;

    /**
     * Export dir name.
     *
     * @var string
     */
    private $exportDirName;

    /**
     * Array of CestObjects to be generated
     *
     * @var array
     */
    private $cests;

    /**
     * TestGenerator constructor.
     *
     * @param string $exportDir
     * @param array $cests
     */
    private function __construct($exportDir, $cests)
    {
        // private constructor for factory
        $this->exportDirName = $exportDir ?? self::GENERATED_DIR;
        $this->exportDirectory = rtrim(
            TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . self::GENERATED_DIR . DIRECTORY_SEPARATOR . $exportDir,
            DIRECTORY_SEPARATOR
        );
        $this->cests = $cests;
    }

    /**
     * Singleton method to retrieve Test Generator
     *
     * @param string $dir
     * @param array $cests
     * @return TestGenerator
     */
    public static function getInstance($dir = null, $cests = null)
    {
        return new TestGenerator($dir, $cests);
    }

    /**
     * Returns the absolute path to the test export director for the generator instance.
     *
     * @return string
     */
    public function getExportDir()
    {
        return $this->exportDirectory;
    }

    /**
     * Load all Cest files as Objects using the Cest Array Processor.
     *
     * @return array
     */
    private function loadAllCestObjects()
    {
        if ($this->cests === null) {
            return CestObjectHandler::getInstance()->getAllObjects();
        }

        return $this->cests;
    }

    /**
     * Create a single PHP file containing the $cestPhp using the $filename.
     * If the _generated directory doesn't exist it will be created.
     *
     * @param string $cestPhp
     * @param string $filename
     * @return void
     * @throws \Exception
     */
    private function createCestFile($cestPhp, $filename)
    {
        $exportFilePath = $this->exportDirectory . DIRECTORY_SEPARATOR . $filename . ".php";
        $file = fopen($exportFilePath, 'w');

        if (!$file) {
            throw new \Exception("Could not open the file!");
        }

        fwrite($file, $cestPhp);
        fclose($file);
    }

    /**
     * Assemble ALL PHP strings using the assembleAllCestPhp function. Loop over and pass each array item
     * to the createCestFile function.
     *
     * @param string $runConfig
     * @return void
     */
    public function createAllCestFiles($runConfig = null)
    {
        DirSetupUtil::createGroupDir($this->exportDirectory);

        // create our manifest file here
        $testManifest = new TestManifest($this->exportDirectory, $runConfig);
        $cestPhpArray = $this->assembleAllCestPhp($testManifest);

        foreach ($cestPhpArray as $cestPhpFile) {
            $this->createCestFile($cestPhpFile[1], $cestPhpFile[0]);
        }

        if ($testManifest->getManifestConfig() === TestManifest::SINGLE_RUN_CONFIG) {
            $testManifest->recordPathToExportDir();
        }
    }

    /**
     * Assemble the entire PHP string for a single Test based on a Cest Object.
     * Create all of the PHP strings for a Test. Concatenate the strings together.
     *
     * @param \Magento\FunctionalTestingFramework\Test\Objects\CestObject $cestObject
     * @throws TestReferenceException
     * @return string
     */
    private function assembleCestPhp($cestObject)
    {
        $usePhp = $this->generateUseStatementsPhp();
        $classAnnotationsPhp = $this->generateClassAnnotationsPhp($cestObject->getAnnotations());
        $className = $cestObject->getName();
        $className = str_replace(' ', '', $className);
        try {
            $hookPhp = $this->generateHooksPhp($cestObject->getHooks());
            $testsPhp = $this->generateTestsPhp($cestObject->getTests());
        } catch (TestReferenceException $e) {
            throw new TestReferenceException($e->getMessage(). " in Cest \"" . $cestObject->getName() . "\"");
        }

        $cestPhp = "<?php\n";
        $cestPhp .= "namespace Magento\AcceptanceTest\\" .  $this->exportDirName ."\Backend;\n\n";
        $cestPhp .= $usePhp;
        $cestPhp .= $classAnnotationsPhp;
        $cestPhp .= sprintf("class %s\n", $className);
        $cestPhp .= "{\n";
        $cestPhp .= $hookPhp;
        $cestPhp .= $testsPhp;
        $cestPhp .= "}\n";

        return $cestPhp;
    }

    /**
     * Load ALL Cest objects. Loop over and pass each to the assembleCestPhp function.
     *
     * @param TestManifest $testManifest
     * @return array
     */
    private function assembleAllCestPhp($testManifest)
    {
        $cestObjects = $this->loadAllCestObjects();
        $cestPhpArray = [];

        foreach ($cestObjects as $cest) {
            $name = $cest->getName();
            $name = $string = str_replace(' ', '', $name);
            $php = $this->assembleCestPhp($cest);
            $cestPhpArray[] = [$name, $php];

            //write to manifest here if config is not single run
            if ($testManifest->getManifestConfig() != TestManifest::SINGLE_RUN_CONFIG) {
                $testManifest->recordCest($cest->getName(), $cest->getTests());
            }
        }

        return $cestPhpArray;
    }

    /**
     * Creates a PHP string for the necessary Allure and AcceptanceTester use statements.
     * Since we don't support other dependencies at this time, this function takes no parameter.
     *
     * @return string
     */
    private function generateUseStatementsPhp()
    {
        $useStatementsPhp = "use Magento\FunctionalTestingFramework\AcceptanceTester;\n";

        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;\n";
        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Persist\DataPersistenceHandler;\n";
        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;\n";

        $allureStatements = [
            "Yandex\Allure\Adapter\Annotation\Features;",
            "Yandex\Allure\Adapter\Annotation\Stories;",
            "Yandex\Allure\Adapter\Annotation\Title;",
            "Yandex\Allure\Adapter\Annotation\Description;",
            "Yandex\Allure\Adapter\Annotation\Parameter;",
            "Yandex\Allure\Adapter\Annotation\Severity;",
            "Yandex\Allure\Adapter\Model\SeverityLevel;",
            "Yandex\Allure\Adapter\Annotation\TestCaseId;\n"
        ];

        foreach ($allureStatements as $allureUseStatement) {
            $useStatementsPhp .= sprintf("use %s\n", $allureUseStatement);
        }

        return $useStatementsPhp;
    }

    /**
     * Creates a PHP string for the Class Annotations block if the Cest file contains an <annotations> block, outside
     * of the <test> blocks.
     *
     * @param array $classAnnotationsObject
     * @return string
     */
    private function generateClassAnnotationsPhp($classAnnotationsObject)
    {
        $classAnnotationsPhp = "/**\n";

        foreach ($classAnnotationsObject as $annotationType => $annotationName) {
            if ($annotationType == "features") {
                $features = "";

                foreach ($annotationName as $name) {
                    $features .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $features .= ", ";
                    }
                }

                $classAnnotationsPhp .= sprintf(" * @Features({%s})\n", $features);
            }

            if ($annotationType == "stories") {
                $stories = "";

                foreach ($annotationName as $name) {
                    $stories .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $stories .= ", ";
                    }
                }

                $classAnnotationsPhp .= sprintf(" * @Stories({%s})\n", $stories);
            }

            if ($annotationType == "title") {
                $classAnnotationsPhp .= sprintf(
                    " * @Title(\"%s\")\n",
                    ucwords($annotationType),
                    $annotationName[0]
                );
            }

            if ($annotationType == "description") {
                $classAnnotationsPhp .= sprintf(" * @Description(\"%s\")\n", $annotationName[0]);
            }

            if ($annotationType == "severity") {
                $classAnnotationsPhp .= sprintf(" * @Severity(level = SeverityLevel::%s)\n", $annotationName[0]);
            }

            if ($annotationType == "testCaseId") {
                $classAnnotationsPhp .= sprintf(" * TestCaseId(\"%s\")\n", $annotationName[0]);
            }

            if ($annotationType == "group") {
                foreach ($annotationName as $group) {
                    $classAnnotationsPhp .= sprintf(" * @group %s\n", $group);
                }
            }

            if ($annotationType == "env") {
                foreach ($annotationName as $env) {
                    $classAnnotationsPhp .= sprintf(" * @env %s\n", $env);
                }
            }
        }

        $classAnnotationsPhp .= " */\n";

        return $classAnnotationsPhp;
    }

    /**
     * Creates a PHP string for the actions contained withing a <test> block.
     * Since nearly half of all Codeception methods don't share the same signature I had to setup a massive Case
     * statement to handle each unique action. At the bottom of the case statement there is a generic function that can
     * construct the PHP string for nearly half of all Codeception actions.
     * @param array $stepsObject
     * @param array $stepsData
     * @param array|bool $hookObject
     * @return string
     */
    private function generateStepsPhp($stepsObject, $stepsData, $hookObject = false)
    {
        $testSteps = "";

        foreach ($stepsObject as $steps) {
            $actor = "I";
            $actionName = $steps->getType();
            $customActionAttributes = $steps->getCustomActionAttributes();
            $selector = null;
            $selector1 = null;
            $selector2 = null;
            $input = null;
            $parameterArray = null;
            $returnVariable = null;
            $x = null;
            $y = null;
            $html = null;
            $url = null;
            $function = null;
            $time = null;
            $locale = null;
            $username = null;
            $password = null;
            $width = null;
            $height = null;
            $requiredAction = null;
            $value = null;
            $button = null;
            $parameter = null;
            $dependentSelector = null;
            $visible = null;

            if (isset($customActionAttributes['returnVariable'])) {
                $returnVariable = $customActionAttributes['returnVariable'];
            }

            if (isset($customActionAttributes['variable'])) {
                $input = $this->addDollarSign($customActionAttributes['variable']);
            } elseif (isset($customActionAttributes['userInput']) && isset($customActionAttributes['url'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['userInput']);
                $url = $this->addUniquenessFunctionCall($customActionAttributes['url']);
            } elseif (isset($customActionAttributes['userInput'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['userInput']);
            } elseif (isset($customActionAttributes['url'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['url']);
            }

            if (isset($customActionAttributes['time'])) {
                $time = $customActionAttributes['time'];
            }

            if (isset($customActionAttributes['timeout'])) {
                $time = $customActionAttributes['timeout'];
            }

            if (isset($customActionAttributes['parameterArray'])) {
                $parameterArray =  $customActionAttributes['parameterArray'];
            }

            if (isset($customActionAttributes['requiredAction'])) {
                $requiredAction = $customActionAttributes['requiredAction'];
            }

            if (isset($customActionAttributes['selectorArray'])) {
                $selector = $customActionAttributes['selectorArray'];
            } elseif (isset($customActionAttributes['selector'])) {
                $selector = $this->addUniquenessFunctionCall($customActionAttributes['selector']);
            }

            if (isset($customActionAttributes['selector1'])) {
                $selector1 = $this->addUniquenessFunctionCall($customActionAttributes['selector1']);
            }

            if (isset($customActionAttributes['selector2'])) {
                $selector2 = $this->addUniquenessFunctionCall($customActionAttributes['selector2']);
            }

            if (isset($customActionAttributes['x'])) {
                $x = $customActionAttributes['x'];
            }

            if (isset($customActionAttributes['y'])) {
                $y = $customActionAttributes['y'];
            }

            if (isset($customActionAttributes['function'])) {
                $function = $customActionAttributes['function'];
            }

            if (isset($customActionAttributes['html'])) {
                $html = $customActionAttributes['html'];
            }

            if (isset($customActionAttributes['locale'])) {
                $locale = $this->wrapWithDoubleQuotes($customActionAttributes['locale']);
            }

            if (isset($customActionAttributes['username'])) {
                $username = $this->wrapWithDoubleQuotes($customActionAttributes['username']);
            }

            if (isset($customActionAttributes['password'])) {
                $password = $this->wrapWithDoubleQuotes($customActionAttributes['password']);
            }

            if (isset($customActionAttributes['width'])) {
                $width = $customActionAttributes['width'];
            }

            if (isset($customActionAttributes['height'])) {
                $height = $customActionAttributes['height'];
            }

            if (isset($customActionAttributes['value'])) {
                $value = $this->wrapWithDoubleQuotes($customActionAttributes['value']);
            }

            if (isset($customActionAttributes['button'])) {
                $button = $this->wrapWithDoubleQuotes($customActionAttributes['button']);
            }

            if (isset($customActionAttributes['parameter'])) {
                $parameter = $this->wrapWithDoubleQuotes($customActionAttributes['parameter']);
            }

            if (isset($customActionAttributes['dependentSelector'])) {
                $dependentSelector = $this->wrapWithDoubleQuotes($customActionAttributes['dependentSelector']);
            }

            if (isset($customActionAttributes['visible'])) {
                $visible = $customActionAttributes['visible'];
            }

            switch ($actionName) {
                case "createData":
                    $entity = $customActionAttributes['entity'];
                    $key = $steps->getMergeKey();
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"create entity that has the mergeKey: %s\");\n",
                        $actor,
                        $key
                    );
                    //Get Entity from Static data.
                    $testSteps .= sprintf(
                        "\t\t$%s = DataObjectHandler::getInstance()->getObject(\"%s\");\n",
                        $entity,
                        $entity
                    );

                    //HookObject End-Product needs to be created in the Class/Cest scope,
                    //otherwise create them in the Test scope.
                    //Determine if there are required-entities and create array of required-entities for merging.
                    $requiredEntities = [];
                    $requiredEntityObjects = [];
                    foreach ($customActionAttributes as $customAttribute) {
                        if (is_array($customAttribute) && $customAttribute['nodeName'] = 'required-entity') {
                            if ($hookObject) {
                                $requiredEntities [] = "\$this->" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getName() => " . "\$this->" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getType()";
                                $requiredEntityObjects [] = '$this->' . $customAttribute
                                    [self::REQUIRED_ENTITY_REFERENCE];
                            } else {
                                $requiredEntities [] = "\$" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE]
                                    . "->getName() => " . "\$" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getType()";
                                $requiredEntityObjects [] = '$' . $customAttribute[self::REQUIRED_ENTITY_REFERENCE];
                            }
                        }
                    }

                    if ($hookObject) {
                        $createEntityFunctionCall = sprintf("\t\t\$this->%s->createEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t\$this->%s = new DataPersistenceHandler($%s",
                            $key,
                            $entity
                        );
                    } else {
                        $createEntityFunctionCall = sprintf("\t\t\$%s->createEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t$%s = new DataPersistenceHandler($%s",
                            $key,
                            $entity
                        );
                    }

                    if (isset($customActionAttributes['storeCode'])) {
                        $createEntityFunctionCall .= sprintf("\"%s\");\n", $customActionAttributes['storeCode']);
                    } else {
                        $createEntityFunctionCall .= ");\n";
                    }

                    //If required-entities are defined, reassign dataObject to not overwrite the static definition.
                    //Also, DataPersistenceHandler needs to be defined with customData array.
                    if (!empty($requiredEntities)) {
                        $dataPersistenceHandlerFunctionCall .= sprintf(
                            ", [%s]);\n",
                            implode(', ', $requiredEntityObjects)
                        );
                    } else {
                        $dataPersistenceHandlerFunctionCall .= ");\n";
                    }
                    $testSteps .= $dataPersistenceHandlerFunctionCall;
                    $testSteps .= $createEntityFunctionCall;
                    break;
                case "deleteData":
                    $key = $customActionAttributes['createDataKey'];
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"delete entity that has the createDataKey: %s\");\n",
                        $actor,
                        $key
                    );

                    if ($hookObject) {
                        $testSteps .= sprintf("\t\t\$this->%s->deleteEntity();\n", $key);
                    } else {
                        $testSteps .= sprintf("\t\t$%s->deleteEntity();\n", $key);
                    }
                    break;
                case "updateData":
                    $entity = $customActionAttributes['entity'];
                    $originalEntity = null;
                    if (isset($customActionAttributes['createDataKey'])) {
                        $originalEntity = $customActionAttributes['createDataKey'];
                    }
                    $key = $steps->getMergeKey();
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"update entity that has the mergeKey: %s\");\n",
                        $actor,
                        $key
                    );
                    //Get Entity from Static data.
                    $testSteps .= sprintf(
                        "\t\t$%s = DataObjectHandler::getInstance()->getObject(\"%s\");\n",
                        $entity,
                        $entity
                    );

                    if ($hookObject) {
                        $updateEntityFunctionCall = sprintf("\t\t\$this->%s->updateEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t\$this->%s = new DataPersistenceHandler($%s, [\$this->%s]);\n",
                            $key,
                            $entity,
                            $originalEntity
                        );
                    } else {
                        $updateEntityFunctionCall = sprintf("\t\t\$%s->updateEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t$%s = new DataPersistenceHandler($%s, [$%s]);\n",
                            $key,
                            $entity,
                            $originalEntity
                        );
                    }

                    if (isset($customActionAttributes['storeCode'])) {
                        $updateEntityFunctionCall .= sprintf("\"%s\");\n", $customActionAttributes['storeCode']);
                    } else {
                        $updateEntityFunctionCall .= ");\n";
                    }

                    $testSteps .= $dataPersistenceHandlerFunctionCall;
                    $testSteps .= $updateEntityFunctionCall;
                    break;
                case "getData":
                    $entity = $customActionAttributes['entity'];
                    $key = $steps->getMergeKey();
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"get entity that has the mergeKey: %s\");\n",
                        $actor,
                        $key
                    );
                    //Get Entity from Static data.
                    $testSteps .= sprintf(
                        "\t\t$%s = DataObjectHandler::getInstance()->getObject(\"%s\");\n",
                        $entity,
                        $entity
                    );

                    //HookObject End-Product needs to be created in the Class/Cest scope,
                    //otherwise create them in the Test scope.
                    //Determine if there are required-entities and create array of required-entities for merging.
                    $requiredEntities = [];
                    $requiredEntityObjects = [];
                    foreach ($customActionAttributes as $customAttribute) {
                        if (is_array($customAttribute) && $customAttribute['nodeName'] = 'required-entity') {
                            if ($hookObject) {
                                $requiredEntities [] = "\$this->" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getName() => " . "\$this->" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getType()";
                                $requiredEntityObjects [] = '$this->' . $customAttribute
                                    [self::REQUIRED_ENTITY_REFERENCE];
                            } else {
                                $requiredEntities [] = "\$" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE]
                                    . "->getName() => " . "\$" . $customAttribute[self::REQUIRED_ENTITY_REFERENCE] .
                                    "->getType()";
                                $requiredEntityObjects [] = '$' . $customAttribute[self::REQUIRED_ENTITY_REFERENCE];
                            }
                        }
                    }

                    if ($hookObject) {
                        $getEntityFunctionCall = sprintf("\t\t\$this->%s->getEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t\$this->%s = new DataPersistenceHandler($%s",
                            $key,
                            $entity
                        );
                    } else {
                        $getEntityFunctionCall = sprintf("\t\t\$%s->getEntity(", $key);
                        $dataPersistenceHandlerFunctionCall = sprintf(
                            "\t\t$%s = new DataPersistenceHandler($%s",
                            $key,
                            $entity
                        );
                    }

                    if (isset($customActionAttributes['index'])) {
                        $getEntityFunctionCall .= sprintf("%s", (int)$customActionAttributes['index']);
                    } else {
                        $getEntityFunctionCall .= 'null';
                    }

                    if (isset($customActionAttributes['storeCode'])) {
                        $getEntityFunctionCall .= sprintf(", \"%s\");\n", $customActionAttributes['storeCode']);
                    } else {
                        $getEntityFunctionCall .= ");\n";
                    }

                    //If required-entities are defined, reassign dataObject to not overwrite the static definition.
                    //Also, DataPersistenceHandler needs to be defined with customData array.
                    if (!empty($requiredEntities)) {
                        $dataPersistenceHandlerFunctionCall .= sprintf(
                            ", [%s]);\n",
                            implode(', ', $requiredEntityObjects)
                        );
                    } else {
                        $dataPersistenceHandlerFunctionCall .= ");\n";
                    }

                    $testSteps .= $dataPersistenceHandlerFunctionCall;
                    $testSteps .= $getEntityFunctionCall;
                    break;
                case "dontSeeCurrentUrlEquals":
                case "dontSeeCurrentUrlMatches":
                case "seeInPopup":
                case "saveSessionSnapshot":
                case "seeCurrentUrlEquals":
                case "seeCurrentUrlMatches":
                case "seeInTitle":
                case "seeInCurrentUrl":
                case "switchToIFrame":
                case "switchToWindow":
                case "typeInPopup":
                case "dontSee":
                case "see":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $input, $selector);
                    break;
                case "switchToNextTab":
                case "switchToPreviousTab":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $this->stripWrappedQuotes($input));
                    break;
                case "clickWithLeftButton":
                case "clickWithRightButton":
                case "moveMouseOver":
                case "scrollTo":
                    if (!$selector) {
                        $selector = 'null';
                    }
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $x, $y);
                    break;
                case "dontSeeCookie":
                case "resetCookie":
                case "seeCookie":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $input, $parameterArray);
                    break;
                case "grabCookie":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $returnVariable,
                        $actor,
                        $actionName,
                        $input,
                        $parameterArray
                    );
                    break;
                case "dontSeeElement":
                case "dontSeeElementInDOM":
                case "dontSeeInFormFields":
                case "seeElement":
                case "seeElementInDOM":
                case "seeInFormFields":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $parameterArray);
                    break;
                case "pressKey":
                case "selectOption":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $input, $parameterArray);
                    break;
                case "submitForm":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $parameterArray, $button);
                    break;
                case "dragAndDrop":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector1, $selector2);
                    break;
                case "executeInSelenium":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $function);
                    break;
                case "executeJS":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $this->wrapWithDoubleQuotes($function));
                    break;
                case "performOn":
                case "waitForElementChange":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $function, $time);
                    break;
                case "waitForJS":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionName,
                        $this->wrapWithDoubleQuotes($function),
                        $time
                    );
                    break;
                case "wait":
                case "waitForAjaxLoad":
                case "waitForElement":
                case "waitForElementVisible":
                case "waitForElementNotVisible":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $time);
                    break;
                case "waitForPageLoad":
                case "waitForText":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $input, $time, $selector);
                    break;
                case "formatMoney":
                case "mSetLocale":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $input, $locale);
                    break;
                case "grabAttributeFrom":
                case "grabMultiple":
                case "grabFromCurrentUrl":
                    if (isset($returnVariable)) {
                        $testSteps .= $this->wrapFunctionCallWithReturnValue(
                            $returnVariable,
                            $actor,
                            $actionName,
                            $selector,
                            $input
                        );
                    } else {
                        $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $input);
                    }
                    break;
                case "grabValueFrom":
                    if (isset($returnVariable)) {
                        $testSteps .= $this->wrapFunctionCallWithReturnValue(
                            $returnVariable,
                            $actor,
                            $actionName,
                            $selector
                        );
                    } else {
                        $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector);
                    }
                    break;
                case "loginAsAdmin":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $username, $password);
                    break;
                case "resizeWindow":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $width, $height);
                    break;
                case "searchAndMultiSelectOption":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionName,
                        $selector,
                        $input,
                        $parameterArray,
                        $requiredAction
                    );
                    break;
                case "seeLink":
                case "dontSeeLink":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $input, $url);
                    break;
                case "setCookie":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionName,
                        $selector,
                        $input,
                        $value,
                        $parameterArray
                    );
                    break;
                case "amOnPage":
                case "amOnSubdomain":
                case "amOnUrl":
                case "appendField":
                case "attachFile":
                case "click":
                case "dontSeeInField":
                case "dontSeeInCurrentUrl":
                case "dontSeeInTitle":
                case "dontSeeInPageSource":
                case "dontSeeOptionIsSelected":
                case "fillField":
                case "loadSessionSnapshot":
                case "seeInField":
                case "seeOptionIsSelected":
                case "unselectOption":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $input);
                    break;
                case "seeNumberOfElements":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionName,
                        $selector,
                        $this->stripWrappedQuotes($input),
                        $parameterArray
                    );
                    break;
                case "seeInPageSource":
                case "seeInSource":
                case "dontSeeInSource":
                    // TODO: Need to fix xml parser to allow parsing html.
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $html);
                    break;
                case "conditionalClick":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $dependentSelector, $visible);
                    break;
                default:
                    if ($returnVariable) {
                        $testSteps .= $this->wrapFunctionCallWithReturnValue(
                            $returnVariable,
                            $actor,
                            $actionName,
                            $selector,
                            $input,
                            $parameter
                        );
                    } else {
                        $testSteps .= $this->wrapFunctionCall($actor, $actionName, $selector, $input, $parameter);
                    }
            }
        }

        return $testSteps;
    }

    /**
     * Resolves replacement of $input$ and $$input$$ in given function, recursing and replacing individual arguments
     * Also determines if each argument requires any quote replacement.
     * @param string $inputString
     * @param array $args
     * @return string
     */
    private function resolveTestVariable($inputString, $args)
    {
        $outputString = $inputString;

        //Loop through each argument, replace and then replace
        foreach ($args as $arg) {
            $outputArg = $arg;
            // Match on any $$data.key$$ found inside arg, matches[0] will be array of $$data.key$$
            preg_match_all("/\\$\\$[\w.\[\]]+\\$\\$/", $outputArg, $matches);
            $this->replaceMatchesIntoArg($matches[0], $outputArg, "$$");

            // Match on any $data.key$ found inside arg, matches[0] will be array of $data.key$
            preg_match_all("/\\$[\w.\[\]]+\\$/", $outputArg, $matches);
            $this->replaceMatchesIntoArg($matches[0], $outputArg, "$");

            $outputString = str_replace($arg, $outputArg, $outputString);
        }

        return $outputString;
    }

    /**
     * Replaces all matches into given outputArg with. Variable scope determined by delimiter given
     * @param array $matches
     * @param string &$outputArg
     * @param string $delimiter
     * @return void
     * @throws \Exception
     */
    private function replaceMatchesIntoArg($matches, &$outputArg, $delimiter)
    {
        // Remove Duplicate $matches from array. Duplicate matches are replaced all in one go.
        $matches = array_unique($matches);
        foreach ($matches as $match) {
            $replacement = null;
            $variable = $this->stripAndSplitReference($match, $delimiter);
            if (count($variable) != 2) {
                throw new \Exception(
                    "Invalid Persisted Entity Reference: {$match}. 
                Test persisted entity references must follow {$delimiter}entityMergeKey.field{$delimiter} format."
                );
            }
            if ($delimiter == "$") {
                $replacement = sprintf("$%s->getCreatedDataByName('%s')", $variable[0], $variable[1]);
            } elseif ($delimiter == "$$") {
                $replacement = sprintf("\$this->%s->getCreatedDataByName('%s')", $variable[0], $variable[1]);
            }

            //Determine if quoteBreak check is necessary. Assume replacement is surrounded in quotes, then override
            if (strpos($outputArg, "\"") !== false) {
                $outputArg = $this->processQuoteBreaks($match, $outputArg, $replacement);
            } else {
                $outputArg = str_replace($match, $replacement, $outputArg);
            }
        }
    }

    /**
     * Processes an argument for $data.key$ and determines if it needs quote breaks on either ends.
     * Returns an output with quote breaks and replacement already done.
     * @param string $match
     * @param string $argument
     * @param string $replacement
     * @return string
     */
    private function processQuoteBreaks($match, $argument, $replacement)
    {
        $outputArg = $argument;
        $beforeIndex = strpos($outputArg, $match) - 1;
        $afterIndex = $beforeIndex + strlen($match) + 1;

        // Determine if there is a " before/after the $match, and if there is only one " before/after match.
        $quoteBefore = $argument[$beforeIndex] == '"' && substr_count($argument, '"', 0, $beforeIndex)<1;
        $quoteAfter = $argument[$afterIndex] == '"' && substr_count($argument, '"', $afterIndex+1)<1;

        //Remove quotes at either end of argument if they aren't necessary. Add double-quote concatenation if needed.
        if ($quoteBefore) {
            $outputArg = substr($outputArg, 0, $beforeIndex) . substr($outputArg, $beforeIndex+1);
            $afterIndex--;
        } else {
            $replacement = '" . ' . $replacement;
        }
        if ($quoteAfter) {
            $outputArg = substr($outputArg, 0, $afterIndex) . substr($outputArg, $afterIndex+1);
        } else {
            $replacement = $replacement . ' . "';
        }
        $outputArg = str_replace($match, $replacement, $outputArg);
        return $outputArg;
    }

    /**
     * Performs str_replace on variable reference, dependent on delimiter and returns exploded array.
     * @param string $reference
     * @param string $delimiter
     * @return array
     */
    private function stripAndSplitReference($reference, $delimiter)
    {
        $strippedReference = str_replace($delimiter, '', $reference);
        return explode('.', $strippedReference);
    }

    /**
     * Creates a PHP string for the _before/_after methods if the Test contains an <before> or <after> block.
     * @param array $hookObjects
     * @return string
     */
    private function generateHooksPhp($hookObjects)
    {
        $hooks = "";
        $createData = false;
        foreach ($hookObjects as $hookObject) {
            $type = $hookObject->getType();
            $dependencies = 'AcceptanceTester $I';

            foreach ($hookObject->getActions() as $step) {
                if (($step->getType() == "createData")
                    || ($step->getType() == "updateData")
                    || ($step->getType() == "getData")
                ) {
                    $hooks .= "\t/**\n";
                    $hooks .= sprintf("\t  * @var DataPersistenceHandler $%s;\n", $step->getMergeKey());
                    $hooks .= "\t */\n";
                    $hooks .= sprintf("\tprotected $%s;\n\n", $step->getMergeKey());
                    $createData = true;
                } elseif ($step->getType() == "entity") {
                    $hooks .= "\t/**\n";
                    $hooks .= sprintf("\t  * @var EntityDataObject $%s;\n", $step->getMergeKey());
                    $hooks .= "\t */\n";
                    $hooks .= sprintf("\tprotected $%s;\n\n", $step->getCustomActionAttributes()['name']);
                }
            }

            try {
                $steps = $this->generateStepsPhp(
                    $hookObject->getActions(),
                    $hookObject->getCustomData(),
                    $createData
                );
            } catch (TestReferenceException $e) {
                throw new TestReferenceException($e->getMessage() . " in Element \"" . $type . "\"");
            }

            if ($type == "after") {
                $hooks .= sprintf("\tpublic function _after(%s)\n", $dependencies);
                $hooks .= "\t{\n";
                $hooks .= $steps;
                $hooks .= "\t}\n\n";
            }

            if ($type == "before") {
                $hooks .= sprintf("\tpublic function _before(%s)\n", $dependencies);
                $hooks .= "\t{\n";
                $hooks .= $steps;
                $hooks .= "\t}\n\n";
            }

            $hooks .= "";
        }

        return $hooks;
    }

    /**
     * Creates a PHP string for the Test Annotations block if the Test contains an <annotations> block.
     *
     * @param array $testAnnotationsObject
     * @return string
     */
    private function generateTestAnnotationsPhp($testAnnotationsObject)
    {
        $testAnnotationsPhp = "\t/**\n";

        foreach ($testAnnotationsObject as $annotationType => $annotationName) {
            if ($annotationType == "features") {
                $features = "";

                foreach ($annotationName as $name) {
                    $features .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $features .= ", ";
                    }
                }

                $testAnnotationsPhp .= sprintf("\t * @Features({%s})\n", $features);
            }

            if ($annotationType == "stories") {
                $stories = "";

                foreach ($annotationName as $name) {
                    $stories .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $stories .= ", ";
                    }
                }

                $testAnnotationsPhp .= sprintf("\t * @Stories({%s})\n", $stories);
            }

            if ($annotationType == "title") {
                $testAnnotationsPhp .= sprintf("\t * @Title(\"%s\")\n", $annotationName[0]);
            }

            if ($annotationType == "description") {
                $testAnnotationsPhp .= sprintf("\t * @Description(\"%s\")\n", $annotationName[0]);
            }

            if ($annotationType == "severity") {
                $testAnnotationsPhp .= sprintf(
                    "\t * @Severity(level = SeverityLevel::%s)\n",
                    $annotationName[0]
                );
            }

            if ($annotationType == "testCaseId") {
                $testAnnotationsPhp .= sprintf("\t * @TestCaseId(\"%s\")\n", $annotationName[0]);
            }
        }

        $testAnnotationsPhp .= sprintf(
            "\t * @Parameter(name = \"%s\", value=\"$%s\")\n",
            "AcceptanceTester",
            "I"
        );

        foreach ($testAnnotationsObject as $annotationType => $annotationName) {
            if ($annotationType == "group") {
                foreach ($annotationName as $name) {
                    $testAnnotationsPhp .= sprintf("\t * @group %s\n", $name);
                }
            }

            if ($annotationType == "env") {
                foreach ($annotationName as $env) {
                    $testAnnotationsPhp .= sprintf("\t * @env %s\n", $env);
                }
            }
        }

        $testAnnotationsPhp .= sprintf("\t * @param %s $%s\n", "AcceptanceTester", "I");
        $testAnnotationsPhp .= "\t * @return void\n";
        $testAnnotationsPhp .= "\t */\n";

        return $testAnnotationsPhp;
    }

    /**
     * Creates a PHP string based on a <test> block.
     * Concatenates the Test Annotations PHP and Test PHP for a single Test.
     * @param array $testsObject
     * @return string
     */
    private function generateTestsPhp($testsObject)
    {
        $testPhp = "";

        foreach ($testsObject as $test) {
            $testName = $test->getName();
            $testName = str_replace(' ', '', $testName);
            $testAnnotations = $this->generateTestAnnotationsPhp($test->getAnnotations());
            $dependencies = 'AcceptanceTester $I';
            try {
                $steps = $this->generateStepsPhp($test->getOrderedActions(), $test->getCustomData());
            } catch (TestReferenceException $e) {
                throw new TestReferenceException($e->getMessage() . " in Test \"" . $test->getName() . "\"");
            }

            $testPhp .= $testAnnotations;
            $testPhp .= sprintf("\tpublic function %s(%s)\n", $testName, $dependencies);
            $testPhp .= "\t{\n";
            $testPhp .= $steps;
            $testPhp .= "\t}\n";

            if (sizeof($testsObject) > 1) {
                $testPhp .= "\n";
            }
        }

        return $testPhp;
    }

    /**
     * Add uniqueness function call to input string based on regex pattern.
     *
     * @param string $input
     * @return string
     */
    private function addUniquenessFunctionCall($input)
    {
        $output = '';

        preg_match('/' . EntityDataObject::CEST_UNIQUE_FUNCTION . '\("[\w]+"\)/', $input, $matches);
        if (!empty($matches)) {
            $parts = preg_split('/' . EntityDataObject::CEST_UNIQUE_FUNCTION . '\("[\w]+"\)/', $input, -1);
            for ($i = 0; $i < count($parts); $i++) {
                $parts[$i] = $this->stripWrappedQuotes($parts[$i]);
            }
            if (!empty($parts[0])) {
                $output = $this->wrapWithDoubleQuotes($parts[0]);
            }
            $output .= $output === '' ? $matches[0] : '.' . $matches[0];
            if (!empty($parts[1])) {
                $output .= '.' . $this->wrapWithDoubleQuotes($parts[1]);
            }
        } else {
            $output = $this->wrapWithDoubleQuotes($input);
        }

        return $output;
    }

    /**
     * Wrap input string with double quotes, and replaces " with \" to prevent broken PHP when generated.
     *
     * @param string $input
     * @return string
     */
    private function wrapWithDoubleQuotes($input)
    {
        if ($input == null) {
            return '';
        }
        //Only replace &quot; with \" so that it doesn't break outer string.
        $input = str_replace('"', '\"', $input);
        return sprintf('"%s"', $input);
    }

    /**
     * Strip beginning and ending double quotes of input string.
     *
     * @param string $input
     * @return string
     */
    private function stripWrappedQuotes($input)
    {
        if (empty($input)) {
            return '';
        }
        if (substr($input, 0, 1) === '"') {
            $input = substr($input, 1);
        }
        if (substr($input, -1, 1) === '"') {
            $input = substr($input, 0, -1);
        }
        return $input;
    }

    /**
     * Add dollar sign at the beginning of input string.
     *
     * @param string $input
     * @return string
     */
    private function addDollarSign($input)
    {
        return sprintf("$%s", $input);
    }

    // @codingStandardsIgnoreStart

    /**
     * Wrap parameters into a function call.
     *
     * @param string $actor
     * @param string $action
     * @param array ...$args
     * @return string
     */
    private function wrapFunctionCall($actor, $action, ...$args)
    {
        $isFirst = true;
        $output = sprintf("\t\t$%s->%s(", $actor, $action);
        for ($i = 0; $i < count($args); $i++) {
            if (null === $args[$i]) {
                continue;
            }
            if (!$isFirst) {
                $output .= ', ';
            }
            $output .= $args[$i];
            $isFirst = false;
        }
        $output .= ");\n";

        return $this->resolveTestVariable($output, $args);
    }

    /**
     * Wrap parameters into a function call with a return value.
     *
     * @param string $returnVariable
     * @param string $actor
     * @param string $action
     * @param array ...$args
     * @return string
     */
    private function wrapFunctionCallWithReturnValue($returnVariable, $actor, $action, ...$args)
    {
        $isFirst = true;
        $output = sprintf("\t\t$%s = $%s->%s(", $returnVariable, $actor, $action);
        for ($i = 0; $i < count($args); $i++) {
            if (null === $args[$i]) {
                continue;
            }
            if (!$isFirst) {
                $output .= ', ';
            }
            $output .= $args[$i];
            $isFirst = false;
        }
        $output .= ");\n";

        return $this->resolveTestVariable($output, $args);
    }
    // @codingStandardsIgnoreEnd
}
