<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use FilesystemIterator;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\CestObject;
use RecursiveDirectoryIterator;

class TestGenerator
{

    /**
     * Path to the export dir.
     *
     * @var string
     */
    private $exportDirectory;

    /**
     * Test generator.
     *
     * @var TestGenerator
     */
    private static $testGenerator;

    /**
     * TestGenerator constructor.
     * @param string $exportDir
     */
    private function __construct($exportDir)
    {
        // private constructor for singleton
        $this->exportDirectory = $exportDir;
    }

    /**
     * Method used to clean export dir if needed and create new empty export dir.
     *
     * @return void
     */
    private function setupExportDir()
    {
        if (file_exists($this->exportDirectory)) {
            $this->rmDirRecursive($this->exportDirectory);
        }

        mkdir($this->exportDirectory, 0777, true);
    }

    /**
     * Takes a directory path and recursively deletes all files and folders.
     *
     * @param string $directory
     * @return void
     */
    private function rmdirRecursive($directory)
    {
        $it = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

        while ($it->valid()) {
            $path = $directory . DIRECTORY_SEPARATOR . $it->getFilename();
            if ($it->isDir()) {
                $this->rmDirRecursive($path);
            } else {
                unlink($path);
            }

            $it->next();
        }

        rmdir($directory);
    }

    /**
     * Singleton method to retrieve Test Generator
     *
     * @return TestGenerator
     */
    public static function getInstance()
    {
        if (!self::$testGenerator) {
            self::$testGenerator = new TestGenerator(TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . "_generated");
        }

        return self::$testGenerator;
    }

    /**
     * Load all Cest files as Objects using the Cest Array Processor.
     *
     * @return array
     */
    private function loadAllCestObjects()
    {
        return CestObjectHandler::getInstance()->getAllObjects();
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
     * @return void
     */
    public function createAllCestFiles()
    {
        $this->setupExportDir();
        $cestPhpArray = $this->assembleAllCestPhp();

        foreach ($cestPhpArray as $cestPhpFile) {
            $this->createCestFile($cestPhpFile[1], $cestPhpFile[0]);
        }
    }

    /**
     * Assemble the entire PHP string for a single Test based on a Cest Object.
     * Create all of the PHP strings for a Test. Concatenate the strings together.
     *
     * @param \Magento\FunctionalTestingFramework\Test\Objects\CestObject $cestObject
     * @return string
     */
    private function assembleCestPhp($cestObject)
    {
        $usePhp = $this->generateUseStatementsPhp($cestObject);
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
        $cestPhp .= "namespace Magento\AcceptanceTest\Backend;\n\n";
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
     * @return array
     */
    private function assembleAllCestPhp()
    {
        $cestObjects = $this->loadAllCestObjects();
        $cestPhpArray = [];

        // create our manifest file here
        $testManifest = new TestManifest($this->exportDirectory);

        foreach ($cestObjects as $cest) {
            $name = $cest->getName();
            $name = $string = str_replace(' ', '', $name);
            $php = $this->assembleCestPhp($cest);
            $cestPhpArray[] = [$name, $php];

            //write to manifest here
            $testManifest->recordCest($cest->getName(), $cest->getTests());
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
        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Api\EntityApiHandler;\n";
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
                $paramsWithUniqueness = [];
                $params = explode(
                    ',',
                    $this->stripWrappedQuotes(rtrim(ltrim($customActionAttributes['parameterArray'], '['), ']'))
                );
                foreach ($params as $param) {
                    $paramsWithUniqueness[] = $this->addUniquenessFunctionCall($param);
                }
                $parameterArray = '[' . implode(',', $paramsWithUniqueness) . ']';
            }

            if (isset($customActionAttributes['requiredAction'])) {
                $requiredAction = $customActionAttributes['requiredAction'];
            }

            if (isset($customActionAttributes['selectorArray'])) {
                $selector = $customActionAttributes['selectorArray'];
            } elseif (isset($customActionAttributes['selector'])) {
                $selector = $this->wrapWithDoubleQuotes($customActionAttributes['selector']);
            }

            if (isset($customActionAttributes['selector1'])) {
                $selector1 = $this->wrapWithDoubleQuotes($customActionAttributes['selector1']);
            }

            if (isset($customActionAttributes['selector2'])) {
                $selector2 = $this->wrapWithDoubleQuotes($customActionAttributes['selector2']);
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
                                $requiredEntities [] = "\$this->" . $customAttribute['name'] . "->getName() => " .
                                    "\$this->" . $customAttribute['name'] . "->getType()";
                                $requiredEntityObjects [] = '$this->' . $customAttribute['name'];
                            } else {
                                $requiredEntities [] = "\$" . $customAttribute['name'] . "->getName() => "
                                    . "\$" . $customAttribute['name'] . "->getType()";
                                $requiredEntityObjects [] = '$' . $customAttribute['name'];
                            }
                        }
                    }
                    //If required-entities are defined, reassign dataObject to not overwrite the static definition.
                    //Also, EntityApiHandler needs to be defined with customData array.
                    if (!empty($requiredEntities)) {
                        $testSteps .= sprintf(
                            "\t\t$%s = new EntityDataObject($%s->getName(), $%s->getType(), $%s->getData()
                            , array_merge($%s->getLinkedEntities(), [%s]), $%s->getUniquenessData());\n",
                            $entity,
                            $entity,
                            $entity,
                            $entity,
                            $entity,
                            implode(", ", $requiredEntities),
                            $entity
                        );

                        if ($hookObject) {
                            $testSteps .= sprintf(
                                "\t\t\$this->%s = new EntityApiHandler($%s, [%s]);\n",
                                $key,
                                $entity,
                                implode(', ', $requiredEntityObjects)
                            );
                            $testSteps .= sprintf("\t\t\$this->%s->createEntity();\n", $key);
                        } else {
                            $testSteps .= sprintf(
                                "\t\t$%s = new EntityApiHandler($%s, [%s]);\n",
                                $key,
                                $entity,
                                implode(', ', $requiredEntityObjects)
                            );
                            $testSteps .= sprintf("\t\t$%s->createEntity();\n", $key);
                        }
                    } else {
                        if ($hookObject) {
                            $testSteps .= sprintf(
                                "\t\t\$this->%s = new EntityApiHandler($%s);\n",
                                $key,
                                $entity
                            );
                            $testSteps .= sprintf("\t\t\$this->%s->createEntity();\n", $key);
                        } else {
                            $testSteps .= sprintf("\t\t$%s = new EntityApiHandler($%s);\n", $key, $entity);
                            $testSteps .= sprintf("\t\t$%s->createEntity();\n", $key);
                        }
                    }

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
                case "entity":
                    $entityData = '[';
                    foreach ($stepsData[$customActionAttributes['name']] as $dataKey => $dataValue) {
                        $variableReplace = $this->resolveTestVariable($dataValue, true);
                        $entityData .= sprintf("\"%s\" => \"%s\", ", $dataKey, $variableReplace);
                    }
                    $entityData .= ']';
                    if ($hookObject) {
                        // no uniqueness attributes for data allowed within entity defined in cest.
                        $testSteps .= sprintf(
                            "\t\t\$this->%s = new EntityDataObject(\"%s\",\"%s\",%s,null,null);\n",
                            $customActionAttributes['name'],
                            $customActionAttributes['name'],
                            $customActionAttributes['type'],
                            $entityData
                        );
                    } else {
                        // no uniqueness attributes for data allowed within entity defined in cest.
                        $testSteps .= sprintf(
                            "\t\t$%s = new EntityDataObject(\"%s\",\"%s\",%s,null,null);\n",
                            $customActionAttributes['name'],
                            $customActionAttributes['name'],
                            $customActionAttributes['type'],
                            $entityData
                        );
                    }
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
     * Resolves replacement of $input$ and $$input$$ in given string.
     * Can be given a boolean to surround replacement with quote breaking.
     * @param string $inputString
     * @param bool $quoteBreak
     * @return string
     * @throws \Exception
     */
    private function resolveTestVariable($inputString, $quoteBreak = false)
    {
        $outputString = $inputString;
        $replaced = false;

        // Check for Cest-scope variables first, stricter regex match.
        preg_match_all("/\\$\\$[\w.]+\\$\\$/", $outputString, $matches);
        foreach ($matches[0] as $match) {
            $replacement = null;
            $variable = $this->stripAndSplitReference($match, '$$');
            if (count($variable) != 2) {
                throw new \Exception(
                    "Invalid Persisted Entity Reference: " . $match .
                    ". Hook persisted entity references must follow \$\$entityMergeKey.field\$\$ format."
                );
            }
            $replacement = sprintf("\$this->%s->getCreatedDataByName('%s')", $variable[0], $variable[1]);
            if ($quoteBreak) {
                $replacement = '" . ' . $replacement . ' . "';
            }
            $outputString = str_replace($match, $replacement, $outputString);
            $replaced = true;
        }

        // Check Test-scope variables
        preg_match_all("/\\$[\w.]+\\$/", $outputString, $matches);
        foreach ($matches[0] as $match) {
            $replacement = null;
            $variable = $this->stripAndSplitReference($match, '$');
            if (count($variable) != 2) {
                throw new \Exception(
                    "Invalid Persisted Entity Reference: " . $match .
                    ". Test persisted entity references must follow \$entityMergeKey.field\$ format."
                );
            }
            $replacement = sprintf("$%s->getCreatedDataByName('%s')", $variable[0], $variable[1]);
            if ($quoteBreak) {
                $replacement = '" . ' . $replacement . ' . "';
            }
            $outputString = str_replace($match, $replacement, $outputString);
            $replaced = true;
        }

        return $outputString;
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
                if ($step->getType() == "createData") {
                    $hooks .= "\t/**\n";
                    $hooks .= sprintf("\t  * @var EntityApiHandler $%s;\n", $step->getMergeKey());
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
        if (empty($input)) {
            return '';
        }
        //Only replace &quot; with \" so that it doesn't break outer string.
        $input = str_replace('"', '\"', $input);
        return sprintf('"%s"', $input);
    }

    /**
     * Strip beginning and ending quotes of input string.
     *
     * @param string $input
     * @return string
     */
    private function stripWrappedQuotes($input)
    {
        if (empty($input)) {
            return '';
        }
        if (substr($input, 0, 1) === '"' || substr($input, 0, 1) === "'") {
            $input = substr($input, 1);
        }
        if (substr($input, -1, 1) === '"' || substr($input, -1, 1) === "'") {
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

        // TODO put in condiional to prevent unncessary quote break (i.e. there are no strings to be appended to
        // variable call.
        return $this->resolveTestVariable($output, true);
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

        // TODO put in condiional to prevent unncessary quote break (i.e. there are no strings to be appended to
        // variable call.
        return $output = $this->resolveTestVariable($output, true);
    }
    // @codingStandardsIgnoreEnd
}
