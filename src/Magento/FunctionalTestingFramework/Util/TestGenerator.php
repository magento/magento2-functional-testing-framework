<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\BaseTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;

/**
 * Class TestGenerator
 * @SuppressWarnings(PHPMD)
 */
class TestGenerator
{
    const REQUIRED_ENTITY_REFERENCE = 'createDataKey';
    const GENERATED_DIR = '_generated';
    const DEFAULT_DIR = 'default';
    const TEST_SCOPE = 'test';
    const HOOK_SCOPE = 'hook';
    const SUITE_SCOPE = 'suite';
    const PRESSKEY_ARRAY_ANCHOR_KEY = '987654321098765432109876543210';

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
     * Array of testObjects to be generated
     *
     * @var array
     */
    private $tests;

    /**
     * Symfony console output interface.
     *
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    private $consoleOutput;

    /**
     * Debug flag.
     *
     * @var boolean
     */
    private $debug;

    /**
     * Current generation scope.
     *
     * @var string
     */
    private $currentGenerationScope;

    /**
     * TestGenerator constructor.
     *
     * @param string  $exportDir
     * @param array   $tests
     * @param boolean $debug
     */
    private function __construct($exportDir, $tests, $debug = false)
    {
        // private constructor for factory
        $this->exportDirName = $exportDir ?? self::DEFAULT_DIR;
        $exportDir = $exportDir ?? self::DEFAULT_DIR;
        $this->exportDirectory = TESTS_MODULE_PATH
            . DIRECTORY_SEPARATOR
            . self::GENERATED_DIR
            . DIRECTORY_SEPARATOR
            . $exportDir;
        $this->tests = $tests;
        $this->consoleOutput = new \Symfony\Component\Console\Output\ConsoleOutput();
        $this->debug = $debug;
    }

    /**
     * Singleton method to retrieve Test Generator
     *
     * @param string  $dir
     * @param array   $tests
     * @param boolean $debug
     * @return TestGenerator
     */
    public static function getInstance($dir = null, $tests = [], $debug = false)
    {
        return new TestGenerator($dir, $tests, $debug);
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
     * Load all Test files as Objects using the Test Object Handler, additionally validates test references being loaded
     * for validity.
     *
     * @param array $testsToIgnore
     * @return array
     */
    private function loadAllTestObjects($testsToIgnore)
    {
        if ($this->tests === null || empty($this->tests)) {
            $testObjects = TestObjectHandler::getInstance()->getAllObjects();
            return array_diff_key($testObjects, $testsToIgnore);
        }

        // If we have a custom configuration, we need to check the tests passed in to insure that we can generate
        // them in the current context.
        $invalidTestObjects = array_intersect_key($this->tests, $testsToIgnore);
        if (!empty($invalidTestObjects)) {
            throw new TestReferenceException(
                "Cannot reference test configuration for generation without accompanying suite.",
                ['tests' => array_keys($invalidTestObjects)]
            );
        }

        return $this->tests;
    }

    /**
     * Create a single PHP file containing the $cestPhp using the $filename.
     * If the _generated directory doesn't exist it will be created.
     *
     * @param string $testPhp
     * @param string $filename
     * @return void
     * @throws \Exception
     */
    private function createCestFile($testPhp, $filename)
    {
        $exportFilePath = $this->exportDirectory . DIRECTORY_SEPARATOR . $filename . ".php";
        $file = fopen($exportFilePath, 'w');

        if (!$file) {
            throw new \Exception("Could not open the file.");
        }

        fwrite($file, $testPhp);
        fclose($file);
    }

    /**
     * Assemble ALL PHP strings using the assembleAllTestPhp function. Loop over and pass each array item
     * to the createCestFile function.
     *
     * @param BaseTestManifest $testManifest
     * @param array            $testsToIgnore
     * @return void
     * @throws TestReferenceException
     * @throws \Exception
     */
    public function createAllTestFiles($testManifest = null, $testsToIgnore = null)
    {
        if ($this->tests === null) {
            // no-op if the test configuration is null
            return;
        }

        DirSetupUtil::createGroupDir($this->exportDirectory);
        if ($testsToIgnore === null) {
            $testsToIgnore = SuiteObjectHandler::getInstance()->getAllTestReferences();
        }

        $testPhpArray = $this->assembleAllTestPhp($testManifest, $testsToIgnore);
        foreach ($testPhpArray as $testPhpFile) {
            $this->createCestFile($testPhpFile[1], $testPhpFile[0]);
        }
    }

    /**
     * Assemble the entire PHP string for a single Test based on a Test Object.
     * Create all of the PHP strings for a Test. Concatenate the strings together.
     *
     * @param \Magento\FunctionalTestingFramework\Test\Objects\TestObject $testObject
     * @return string
     * @throws TestReferenceException
     * @throws \Exception
     */
    private function assembleTestPhp($testObject)
    {
        $usePhp = $this->generateUseStatementsPhp();
        $classAnnotationsPhp = $this->generateAnnotationsPhp($testObject->getAnnotations());

        $className = $testObject->getCodeceptionName();
        try {
            if (!$testObject->isSkipped()) {
                $hookPhp = $this->generateHooksPhp($testObject->getHooks());
            } else {
                $hookPhp = null;
            }
            $testsPhp = $this->generateTestPhp($testObject);
        } catch (TestReferenceException $e) {
            throw new TestReferenceException($e->getMessage() . "\n" . $testObject->getFilename());
        }

        $cestPhp = "<?php\n";
        $cestPhp .= "namespace Magento\AcceptanceTest\\_" . $this->exportDirName . "\Backend;\n\n";
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
     * Load ALL Test objects. Loop over and pass each to the assembleTestPhp function.
     *
     * @param BaseTestManifest $testManifest
     * @param array            $testsToIgnore
     * @return array
     */
    private function assembleAllTestPhp($testManifest, array $testsToIgnore)
    {
        /** @var TestObject[] $testObjects */
        $testObjects = $this->loadAllTestObjects($testsToIgnore);
        $cestPhpArray = [];

        foreach ($testObjects as $test) {
            // Do not generate test if it is an extended test and parent does not exist
            if ($test->isSkipped() && !empty($test->getParentName())) {
                try {
                    TestObjectHandler::getInstance()->getObject($test->getParentName());
                } catch (TestReferenceException $e) {
                    print("{$test->getName()} will not be generated. Parent {$e->getMessage()} \n");
                    continue;
                }
            }

            $this->debug("<comment>Start creating test: " . $test->getCodeceptionName() . "</comment>");
            $php = $this->assembleTestPhp($test);
            $cestPhpArray[] = [$test->getCodeceptionName(), $php];

            $debugInformation = $test->getDebugInformation();
            $this->debug($debugInformation);
            $this->debug("<comment>Finish creating test: " . $test->getCodeceptionName() . "</comment>" . PHP_EOL);

            //write to manifest here if manifest is not null
            if ($testManifest != null) {
                $testManifest->addTest($test);
            }
        }

        return $cestPhpArray;
    }

    /**
     * Output information in console when debug flag is enabled.
     *
     * @param array|string $messages
     * @return void
     */
    private function debug($messages)
    {
        if ($this->debug && $messages) {
            $messages = (array)$messages;
            foreach ($messages as $message) {
                $this->consoleOutput->writeln($message);
            }
        }
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
        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;\n";
        $useStatementsPhp .= "use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;\n";
        $useStatementsPhp .= "use \Codeception\Util\Locator;\n";

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
     * Generates Annotations PHP for given object, using given scope to determine indentation and additional output.
     *
     * @param array   $annotationsObject
     * @param boolean $isMethod
     * @return string
     */
    private function generateAnnotationsPhp($annotationsObject, $isMethod = false)
    {
        //TODO: Refactor to deal with PHPMD.CyclomaticComplexity
        if ($isMethod) {
            $indent = "\t";
        } else {
            $indent = "";
        }

        $annotationsPhp = "{$indent}/**\n";

        foreach ($annotationsObject as $annotationType => $annotationName) {
            //Remove conditional and output useCaseId upon completion of MQE-588
            if ($annotationType == "useCaseId") {
                continue;
            }
            if (!$isMethod) {
                $annotationsPhp .= $this->generateClassAnnotations($annotationType, $annotationName);
            } else {
                $annotationsPhp .= $this->generateMethodAnnotations($annotationType, $annotationName);
            }
        }

        if ($isMethod) {
            $annotationsPhp .= $this->generateMethodAnnotations();
        }

        $annotationsPhp .= "{$indent} */\n";

        return $annotationsPhp;
    }

    /**
     * Method which returns formatted method level annotation based on type and name(s).
     *
     * @param string      $annotationType
     * @param string|null $annotationName
     * @return null|string
     */
    private function generateMethodAnnotations($annotationType = null, $annotationName = null)
    {
        $annotationToAppend = null;
        $indent = "\t";

        switch ($annotationType) {
            case "features":
                $features = "";
                foreach ($annotationName as $name) {
                    $features .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $features .= ", ";
                    }
                }
                $annotationToAppend .= sprintf("{$indent} * @Features({%s})\n", $features);
                break;

            case "stories":
                $stories = "";
                foreach ($annotationName as $name) {
                    $stories .= sprintf("\"%s\"", $name);

                    if (next($annotationName)) {
                        $stories .= ", ";
                    }
                }
                $annotationToAppend .= sprintf("{$indent} * @Stories({%s})\n", $stories);
                break;

            case "severity":
                $annotationToAppend = sprintf("{$indent} * @Severity(level = SeverityLevel::%s)\n", $annotationName[0]);
                break;

            case null:
                $annotationToAppend = sprintf(
                    "{$indent} * @Parameter(name = \"%s\", value=\"$%s\")\n",
                    "AcceptanceTester",
                    "I"
                );
                $annotationToAppend .= sprintf("{$indent} * @param %s $%s\n", "AcceptanceTester", "I");
                $annotationToAppend .= "{$indent} * @return void\n";
                $annotationToAppend .= "{$indent} * @throws \Exception\n";
                break;
        }

        return $annotationToAppend;
    }

    /**
     * Method which return formatted class level annotations based on type and name(s).
     *
     * @param string $annotationType
     * @param string $annotationName
     * @return null|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function generateClassAnnotations($annotationType, $annotationName)
    {
        $annotationToAppend = null;

        switch ($annotationType) {
            case "title":
                $annotationToAppend = sprintf(" * @Title(\"%s\")\n", $annotationName[0]);
                break;

            case "description":
                $annotationToAppend = sprintf(" * @Description(\"%s\")\n", $annotationName[0]);
                break;

            case "testCaseId":
                $annotationToAppend = sprintf(" * @TestCaseId(\"%s\")\n", $annotationName[0]);
                break;

            case "useCaseId":
                $annotationToAppend = sprintf(" * @UseCaseId(\"%s\")\n", $annotationName[0]);
                break;

            case "group":
                foreach ($annotationName as $group) {
                    $annotationToAppend .= sprintf(" * @group %s\n", $group);
                }
                break;
        }

        return $annotationToAppend;
    }

    /**
     * Creates a PHP string for the actions contained withing a <test> block.
     * Since nearly half of all Codeception methods don't share the same signature I had to setup a massive Case
     * statement to handle each unique action. At the bottom of the case statement there is a generic function that can
     * construct the PHP string for nearly half of all Codeception actions.
     *
     * @param array  $actionObjects
     * @param string $generationScope
     * @param string $actor
     * @return string
     * @throws TestReferenceException
     * @throws \Exception
     * @SuppressWarnings(PHPMD)
     */
    public function generateStepsPhp($actionObjects, $generationScope = TestGenerator::TEST_SCOPE, $actor = "I")
    {
        //TODO: Refactor Method according to PHPMD warnings, remove @SuppressWarnings accordingly.
        $testSteps = "";
        $this->currentGenerationScope = $generationScope;

        foreach ($actionObjects as $actionObject) {
            $stepKey = $actionObject->getStepKey();
            $customActionAttributes = $actionObject->getCustomActionAttributes();
            $attribute = null;
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
            $command = null;
            $arguments = null;
            $sortOrder = null;
            $storeCode = null;
            $format = null;

            $assertExpected = null;
            $assertActual = null;
            $assertMessage = null;
            $assertIsStrict = null;
            $assertDelta = null;

            // Validate action attributes and print notice messages on violation.
            $this->validateXmlAttributesMutuallyExclusive($stepKey, $actionObject->getType(), $customActionAttributes);

            if (isset($customActionAttributes['command'])) {
                $command = $this->addUniquenessFunctionCall($customActionAttributes['command']);
            }
            if (isset($customActionAttributes['arguments'])) {
                $arguments = $this->addUniquenessFunctionCall($customActionAttributes['arguments']);
            }

            if (isset($customActionAttributes['attribute'])) {
                $attribute = $customActionAttributes['attribute'];
            }

            if (isset($customActionAttributes['sortOrder'])) {
                $sortOrder = $customActionAttributes['sortOrder'];
            }

            if (isset($customActionAttributes['userInput']) && isset($customActionAttributes['url'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['userInput']);
                $url = $this->addUniquenessFunctionCall($customActionAttributes['url']);
            } elseif (isset($customActionAttributes['userInput'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['userInput']);
            } elseif (isset($customActionAttributes['url'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['url']);
                $url = $this->addUniquenessFunctionCall($customActionAttributes['url']);
            } elseif (isset($customActionAttributes['expectedValue'])) {
                //For old Assert backwards Compatibility, remove when deprecating
                $assertExpected = $this->addUniquenessFunctionCall($customActionAttributes['expectedValue']);
            } elseif (isset($customActionAttributes['regex'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['regex']);
            }

            if (isset($customActionAttributes['date']) && isset($customActionAttributes['format'])) {
                $input = $this->addUniquenessFunctionCall($customActionAttributes['date']);
                if ($input === "") {
                    $input = "\"Now\"";
                }
                $format = $this->addUniquenessFunctionCall($customActionAttributes['format']);
                if ($format === "") {
                    $format = "\"r\"";
                }
            }

            if (isset($customActionAttributes['expected'])) {
                $assertExpected = $this->resolveValueByType(
                    $customActionAttributes['expected'],
                    isset($customActionAttributes['expectedType']) ? $customActionAttributes['expectedType'] : null
                );
            }
            if (isset($customActionAttributes['actual'])) {
                $assertActual = $this->resolveValueByType(
                    $customActionAttributes['actual'],
                    isset($customActionAttributes['actualType']) ? $customActionAttributes['actualType'] : null
                );
            }
            if (isset($customActionAttributes['message'])) {
                $assertMessage = $this->addUniquenessFunctionCall($customActionAttributes['message']);
            }
            if (isset($customActionAttributes['delta'])) {
                $assertDelta = $this->resolveValueByType($customActionAttributes['delta'], "float");
            }
            if (isset($customActionAttributes['strict'])) {
                $assertIsStrict = $this->resolveValueByType($customActionAttributes['strict'], "bool");
            }

            if (isset($customActionAttributes['time'])) {
                $time = $customActionAttributes['time'];
            }
            if (isset($customActionAttributes['timeout'])) {
                $time = $customActionAttributes['timeout'];
            }
            $time = $time ?? ActionObject::getDefaultWaitTimeout();

            if (isset($customActionAttributes['parameterArray']) && $actionObject->getType() != 'pressKey') {
                // validate the param array is in the correct format
                $this->validateParameterArray($customActionAttributes['parameterArray']);

                $parameterArray = "[";
                $parameterArray .= $this->addUniquenessToParamArray($customActionAttributes['parameterArray']);
                $parameterArray .= "]";
            }

            if (isset($customActionAttributes['requiredAction'])) {
                $requiredAction = $customActionAttributes['requiredAction'];
            }

            if (isset($customActionAttributes['selectorArray'])) {
                $selector = $customActionAttributes['selectorArray'];
            } elseif (isset($customActionAttributes['selector'])) {
                $selector = $this->addUniquenessFunctionCall($customActionAttributes['selector']);
                $selector = $this->resolveLocatorFunctionInAttribute($selector);
            }

            if (isset($customActionAttributes['selector1']) || isset($customActionAttributes['filterSelector'])) {
                $selectorOneValue = $customActionAttributes['selector1'] ?? $customActionAttributes['filterSelector'];
                $selector1 = $this->addUniquenessFunctionCall($selectorOneValue);
                $selector1 = $this->resolveLocatorFunctionInAttribute($selector1);
            }

            if (isset($customActionAttributes['selector2']) || isset($customActionAttributes['optionSelector'])) {
                $selectorTwoValue = $customActionAttributes['selector2'] ?? $customActionAttributes['optionSelector'];
                $selector2 = $this->addUniquenessFunctionCall($selectorTwoValue);
                $selector2 = $this->resolveLocatorFunctionInAttribute($selector2);
            }

            if (isset($customActionAttributes['x'])) {
                $x = $customActionAttributes['x'];
            }

            if (isset($customActionAttributes['y'])) {
                $y = $customActionAttributes['y'];
            }

            if (isset($customActionAttributes['function'])) {
                $function = $this->addUniquenessFunctionCall($customActionAttributes['function']);
                if (in_array($actionObject->getType(), ActionObject::FUNCTION_CLOSURE_ACTIONS)) {
                    // Argument must be a closure function, not a string.
                    $function = trim($function, '"');
                }
                // turn $javaVariable => \$javaVariable but not {$mftfVariable}
                if ($actionObject->getType() == "executeJS") {
                    $function = preg_replace('/(?<!{)(\$[A-Za-z._]+)(?![A-z.]*+\$)/', '\\\\$1', $function);
                }
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
                $dependentSelector = $this->addUniquenessFunctionCall($customActionAttributes['dependentSelector']);
            }

            if (isset($customActionAttributes['visible'])) {
                $visible = $customActionAttributes['visible'];
            }

            if (isset($customActionAttributes['storeCode'])) {
                $storeCode = $customActionAttributes['storeCode'];
            }
            switch ($actionObject->getType()) {
                case "createData":
                    $entity = $customActionAttributes['entity'];
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"create entity that has the stepKey: %s\");\n",
                        $actor,
                        $stepKey
                    );

                    //TODO refactor entity field override to not be individual actionObjects
                    $customEntityFields =
                        $customActionAttributes[ActionObjectExtractor::ACTION_OBJECT_PERSISTENCE_FIELDS] ?? [];

                    $requiredEntityKeys = [];
                    foreach ($actionObject->getCustomActionAttributes() as $actionAttribute) {
                        if (is_array($actionAttribute) && $actionAttribute['nodeName'] == 'requiredEntity') {
                            //append ActionGroup if provided
                            $requiredEntityActionGroup = $actionAttribute['actionGroup'] ?? null;
                            $requiredEntityKeys[] = $actionAttribute['createDataKey'] . $requiredEntityActionGroup;
                        }
                    }
                    // Build array of requiredEntities
                    $requiredEntityKeysArray = "";
                    if (!empty($requiredEntityKeys)) {
                        $requiredEntityKeysArray = '"' . implode('", "', $requiredEntityKeys) . '"';
                    }
                    //Determine Scope
                    $scope = PersistedObjectHandler::TEST_SCOPE;
                    if ($generationScope == TestGenerator::HOOK_SCOPE) {
                        $scope = PersistedObjectHandler::HOOK_SCOPE;
                    } elseif ($generationScope == TestGenerator::SUITE_SCOPE) {
                        $scope = PersistedObjectHandler::SUITE_SCOPE;
                    }
                    
                    $createEntityFunctionCall = "\t\tPersistedObjectHandler::getInstance()->createEntity(";
                    $createEntityFunctionCall .= "\n\t\t\t\"{$stepKey}\",";
                    $createEntityFunctionCall .= "\n\t\t\t\"{$scope}\",";
                    $createEntityFunctionCall .= "\n\t\t\t\"{$entity}\"";
                    $createEntityFunctionCall .= ",\n\t\t\t[{$requiredEntityKeysArray}]";
                    if (count($customEntityFields) > 1) {
                        $createEntityFunctionCall .= ",\n\t\t\t\${$stepKey}Fields";
                    } else {
                        $createEntityFunctionCall .= ",\n\t\t\tnull";
                    }
                    if ($storeCode !== null) {
                        $createEntityFunctionCall .= ",\n\t\t\t\"{$storeCode}\"";
                    }
                    $createEntityFunctionCall .= "\n\t\t);\n";
                    $testSteps .= $createEntityFunctionCall;
                    break;
                case "deleteData":
                    if (isset($customActionAttributes['createDataKey'])) {
                        $key = $this->resolveStepKeyReferences(
                            $customActionAttributes['createDataKey'],
                            $actionObject->getActionOrigin(),
                            true
                        );
                        $actionGroup = $actionObject->getCustomActionAttributes()['actionGroup'] ?? null;
                        $key .= $actionGroup;
                        //Add an informative statement to help the user debug test runs
                        $contextSetter = sprintf(
                            "\t\t$%s->amGoingTo(\"delete entity that has the createDataKey: %s\");\n",
                            $actor,
                            $key
                        );

                        //Determine Scope
                        $scope = PersistedObjectHandler::TEST_SCOPE;
                        if ($generationScope == TestGenerator::HOOK_SCOPE) {
                            $scope = PersistedObjectHandler::HOOK_SCOPE;
                        } elseif ($generationScope == TestGenerator::SUITE_SCOPE) {
                            $scope = PersistedObjectHandler::SUITE_SCOPE;
                        }

                        $deleteEntityFunctionCall = "\t\tPersistedObjectHandler::getInstance()->deleteEntity(";
                        $deleteEntityFunctionCall .= "\n\t\t\t\"{$key}\",";
                        $deleteEntityFunctionCall .= "\n\t\t\t\"{$scope}\"";
                        $deleteEntityFunctionCall .= "\n\t\t);\n";

                        $testSteps .= $contextSetter;
                        $testSteps .= $deleteEntityFunctionCall;
                    } else {
                        $url = $this->resolveAllRuntimeReferences([$url])[0];
                        $url = $this->resolveTestVariable([$url], null)[0];
                        $output = sprintf(
                            "\t\t$%s->deleteEntityByUrl(%s);\n",
                            $actor,
                            $url
                        );
                        $testSteps .= $output;
                    }
                    break;
                case "updateData":
                    $key = $this->resolveStepKeyReferences(
                        $customActionAttributes['createDataKey'],
                        $actionObject->getActionOrigin(),
                        true
                    );
                    $updateEntity = $customActionAttributes['entity'];
                    $actionGroup = $actionObject->getCustomActionAttributes()['actionGroup'] ?? null;
                    $key .= $actionGroup;

                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"update entity that has the createdDataKey: %s\");\n",
                        $actor,
                        $key
                    );
                    
                    // Build array of requiredEntities
                    $requiredEntityKeys = [];
                    foreach ($actionObject->getCustomActionAttributes() as $actionAttribute) {
                        if (is_array($actionAttribute) && $actionAttribute['nodeName'] == 'requiredEntity') {
                            //append ActionGroup if provided
                            $requiredEntityActionGroup = $actionAttribute['actionGroup'] ?? null;
                            $requiredEntityKeys[] = $actionAttribute['createDataKey'] . $requiredEntityActionGroup;
                        }
                    }
                    $requiredEntityKeysArray = "";
                    if (!empty($requiredEntityKeys)) {
                        $requiredEntityKeysArray = '"' . implode('", "', $requiredEntityKeys) . '"';
                    }

                    $scope = PersistedObjectHandler::TEST_SCOPE;
                    if ($generationScope == TestGenerator::HOOK_SCOPE) {
                        $scope = PersistedObjectHandler::HOOK_SCOPE;
                    } elseif ($generationScope == TestGenerator::SUITE_SCOPE) {
                        $scope = PersistedObjectHandler::SUITE_SCOPE;
                    }

                    $updateEntityFunctionCall = "\t\tPersistedObjectHandler::getInstance()->updateEntity(";
                    $updateEntityFunctionCall .= "\n\t\t\t\"{$key}\",";
                    $updateEntityFunctionCall .= "\n\t\t\t\"{$scope}\",";
                    $updateEntityFunctionCall .= "\n\t\t\t\"{$updateEntity}\"";
                    $updateEntityFunctionCall .= ",\n\t\t\t[{$requiredEntityKeysArray}]";
                    if ($storeCode !== null) {
                        $updateEntityFunctionCall .= ",\n\t\t\t\"{$storeCode}\"";
                    }
                    $updateEntityFunctionCall .= "\n\t\t);\n";
                    $testSteps .= $updateEntityFunctionCall;

                    break;
                case "getData":
                    $entity = $customActionAttributes['entity'];
                    $index = null;
                    if (isset($customActionAttributes['index'])) {
                        $index = (int)$customActionAttributes['index'];
                    }
                    //Add an informative statement to help the user debug test runs
                    $testSteps .= sprintf(
                        "\t\t$%s->amGoingTo(\"get entity that has the stepKey: %s\");\n",
                        $actor,
                        $stepKey
                    );

                    // Build array of requiredEntities
                    $requiredEntityKeys = [];
                    foreach ($actionObject->getCustomActionAttributes() as $actionAttribute) {
                        if (is_array($actionAttribute) && $actionAttribute['nodeName'] == 'requiredEntity') {
                            $requiredEntityActionGroup = $actionAttribute['actionGroup'] ?? null;
                            $requiredEntityKeys[] = $actionAttribute['createDataKey'] . $requiredEntityActionGroup;
                        }
                    }
                    $requiredEntityKeysArray = "";
                    if (!empty($requiredEntityKeys)) {
                        $requiredEntityKeysArray = '"' . implode('", "', $requiredEntityKeys) . '"';
                    }

                    //Determine Scope
                    $scope = PersistedObjectHandler::TEST_SCOPE;
                    if ($generationScope == TestGenerator::HOOK_SCOPE) {
                        $scope = PersistedObjectHandler::HOOK_SCOPE;
                    } elseif ($generationScope == TestGenerator::SUITE_SCOPE) {
                        $scope = PersistedObjectHandler::SUITE_SCOPE;
                    }

                    //Create Function
                    $getEntityFunctionCall = "\t\tPersistedObjectHandler::getInstance()->getEntity(";
                    $getEntityFunctionCall .= "\n\t\t\t\"{$stepKey}\",";
                    $getEntityFunctionCall .= "\n\t\t\t\"{$scope}\",";
                    $getEntityFunctionCall .= "\n\t\t\t\"{$entity}\"";
                    $getEntityFunctionCall .= ",\n\t\t\t[{$requiredEntityKeysArray}]";
                    if ($storeCode !== null) {
                        $getEntityFunctionCall .= ",\n\t\t\t\"{$storeCode}\"";
                    } else {
                        $getEntityFunctionCall .= ",\n\t\t\tnull";
                    }
                    if ($index !== null) {
                        $getEntityFunctionCall .= ",\n\t\t\t{$index}";
                    }
                    $getEntityFunctionCall .= "\n\t\t);\n";
                    $testSteps .= $getEntityFunctionCall;

                    break;
                case "assertArrayIsSorted":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $parameterArray,
                        $this->wrapWithDoubleQuotes($sortOrder)
                    );
                    break;
                case "seeCurrentUrlEquals":
                case "seeCurrentUrlMatches":
                case "dontSeeCurrentUrlEquals":
                case "dontSeeCurrentUrlMatches":
                case "seeInPopup":
                case "saveSessionSnapshot":
                case "seeInTitle":
                case "seeInCurrentUrl":
                case "switchToIFrame":
                case "switchToWindow":
                case "typeInPopup":
                case "dontSee":
                case "see":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $input, $selector);
                    break;
                case "switchToNextTab":
                case "switchToPreviousTab":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $input);
                    break;
                case "clickWithLeftButton":
                case "clickWithRightButton":
                case "moveMouseOver":
                case "scrollTo":
                    if (!$selector) {
                        $selector = 'null';
                    }
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $selector, $x, $y);
                    break;
                case "dontSeeCookie":
                case "resetCookie":
                case "seeCookie":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $input,
                        $parameterArray
                    );
                    break;
                case "grabCookie":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
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
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $parameterArray
                    );
                    break;
                case "pressKey":
                    $parameterArray = $customActionAttributes['parameterArray'] ?? null;
                    if ($parameterArray) {
                        $parameterArray = $this->processPressKey($parameterArray);
                    }
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $input,
                        $parameterArray
                    );
                    break;
                case "selectOption":
                case "unselectOption":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $input,
                        $parameterArray
                    );
                    break;
                case "submitForm":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $parameterArray,
                        $button
                    );
                    break;
                case "dragAndDrop":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector1,
                        $selector2,
                        $x,
                        $y
                    );
                    break;
                case "selectMultipleOptions":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector1,
                        $selector2,
                        $input,
                        $parameterArray
                    );
                    break;
                case "executeInSelenium":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $function);
                    break;
                case "executeJS":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
                        $function
                    );
                    break;
                case "performOn":
                case "waitForElementChange":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $function,
                        $time
                    );
                    break;
                case "waitForJS":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $function,
                        $time
                    );
                    break;
                case "wait":
                case "waitForAjaxLoad":
                case "waitForElement":
                case "waitForElementVisible":
                case "waitForElementNotVisible":
                case "waitForPwaElementVisible":
                case "waitForPwaElementNotVisible":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $selector, $time);
                    break;
                case "waitForPageLoad":
                case "waitForText":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $input,
                        $time,
                        $selector
                    );
                    break;
                case "formatMoney":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
                        $input,
                        $locale
                    );
                    break;
                case "mSetLocale":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $input, $locale);
                    break;
                case "grabAttributeFrom":
                case "grabMultiple":
                case "grabFromCurrentUrl":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
                        $selector,
                        $input
                    );
                    break;
                case "grabTextFrom":
                case "grabValueFrom":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
                        $selector
                    );
                    break;
                case "grabPageSource":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject
                    );
                    break;
                case "resizeWindow":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $width, $height);
                    break;
                case "searchAndMultiSelectOption":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $input,
                        $parameterArray,
                        $requiredAction
                    );
                    break;
                case "seeLink":
                case "dontSeeLink":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $input, $url);
                    break;
                case "setCookie":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
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
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $selector, $input);
                    break;
                case "seeNumberOfElements":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $input,
                        $parameterArray
                    );
                    break;
                case "seeInPageSource":
                case "seeInSource":
                case "dontSeeInSource":
                    // TODO: Need to fix xml parser to allow parsing html.
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $html);
                    break;
                case "conditionalClick":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $dependentSelector,
                        $visible
                    );
                    break;
                case "assertEquals":
                case "assertGreaterOrEquals":
                case "assertGreaterThan":
                case "assertGreaterThanOrEqual":
                case "assertInternalType":
                case "assertLessOrEquals":
                case "assertLessThan":
                case "assertLessThanOrEqual":
                case "assertNotEquals":
                case "assertInstanceOf":
                case "assertNotInstanceOf":
                case "assertNotRegExp":
                case "assertNotSame":
                case "assertRegExp":
                case "assertSame":
                case "assertStringStartsNotWith":
                case "assertStringStartsWith":
                case "assertArrayHasKey":
                case "assertArrayNotHasKey":
                case "assertCount":
                case "assertContains":
                case "assertNotContains":
                case "expectException":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $assertExpected,
                        $assertActual,
                        $assertMessage,
                        $assertDelta
                    );
                    break;
                case "assertElementContainsAttribute":
                    // If a blank string or null is passed in we need to pass a blank string to the function.
                    if (empty($assertExpected)) {
                        $assertExpected = '""';
                    }

                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $this->wrapWithDoubleQuotes($attribute),
                        $assertExpected
                    );
                    break;
                case "assertEmpty":
                case "assertFalse":
                case "assertFileExists":
                case "assertFileNotExists":
                case "assertIsEmpty":
                case "assertNotEmpty":
                case "assertNotNull":
                case "assertNull":
                case "assertTrue":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $assertActual,
                        $assertMessage
                    );
                    break;
                case "assertArraySubset":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $assertExpected,
                        $assertActual,
                        $assertIsStrict,
                        $assertMessage
                    );
                    break;
                case "fail":
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $assertMessage
                    );
                    break;
                case "magentoCLI":
                    $testSteps .= $this->wrapFunctionCallWithReturnValue(
                        $stepKey,
                        $actor,
                        $actionObject,
                        $command,
                        $arguments
                    );
                    $testSteps .= sprintf(
                        "\t\t$%s->comment(\$%s);\n",
                        $actor,
                        $stepKey
                    );
                    break;
                case "field":
                    $fieldKey = $actionObject->getCustomActionAttributes()['key'];
                    $input = $this->resolveTestVariable(
                        [$input],
                        $actionObject->getActionOrigin()
                    )[0];
                    $argRef = "\t\t\$";
                    $argRef .= str_replace(ucfirst($fieldKey), "", $stepKey) . "Fields['{$fieldKey}'] = ${input};\n";
                    $testSteps .= $argRef;
                    break;
                case "generateDate":
                    $timezone = getenv("DEFAULT_TIMEZONE");
                    if (isset($customActionAttributes['timezone'])) {
                        $timezone = $customActionAttributes['timezone'];
                    }

                    $dateGenerateCode = "\t\t\$date = new \DateTime();\n";
                    $dateGenerateCode .= "\t\t\$date->setTimestamp(strtotime({$input}));\n";
                    $dateGenerateCode .= "\t\t\$date->setTimezone(new \DateTimeZone(\"{$timezone}\"));\n";
                    $dateGenerateCode .= "\t\t\${$stepKey} = \$date->format({$format});\n";

                    $testSteps .= $dateGenerateCode;
                    break;
                case "skipReadinessCheck":
                    $testSteps .= $this->wrapFunctionCall($actor, $actionObject, $customActionAttributes['state']);
                    break;
                default:
                    $testSteps .= $this->wrapFunctionCall(
                        $actor,
                        $actionObject,
                        $selector,
                        $input,
                        $parameter
                    );
            }
        }

        return $testSteps;
    }

    /**
     * Resolves Locator:: in given $attribute if it is found.
     *
     * @param string $attribute
     * @return string
     */
    private function resolveLocatorFunctionInAttribute($attribute)
    {
        if (strpos($attribute, "Locator::") !== false) {
            $attribute = $this->stripWrappedQuotes($attribute);
            $attribute = $this->wrapFunctionArgsWithQuotes("/Locator::[\w]+\(([\s\S]+)\)/", $attribute);
        }
        return $attribute;
    }

    /**
     * Resolves replacement of $input$ and $$input$$ in given function, recursing and replacing individual arguments
     * Also determines if each argument requires any quote replacement.
     *
     * @param array $args
     * @param array $actionOrigin
     * @return array
     * @throws \Exception
     */
    private function resolveTestVariable($args, $actionOrigin)
    {
        $newArgs = [];
        foreach ($args as $key => $arg) {
            if ($arg === null) {
                continue;
            }
            $outputArg = $arg;
            // Math on $data.key$ and $$data.key$$
            preg_match_all('/\${1,2}[\w.\[\]]+\${1,2}/', $outputArg, $matches);
            $this->replaceMatchesIntoArg($matches[0], $outputArg);

            //trim "{$variable}" into $variable
            $outputArg = $this->trimVariableIfNeeded($outputArg);

            $outputArg = $this->resolveStepKeyReferences($outputArg, $actionOrigin);

            $newArgs[$key] = $outputArg;
        }

        return $newArgs;
    }

    /**
     * Trims given $input of "{$var}" to $var if needed. Returns $input if format fails.
     *
     * @param string $input
     * @return string
     */
    private function trimVariableIfNeeded($input)
    {
        preg_match('/"{\$[a-z][a-zA-Z\d]+}"/', $input, $match);
        if (isset($match[0])) {
            return trim($input, '{}"');
        } else {
            return $input;
        }
    }

    /**
     * Replaces all matches into given outputArg with. Variable scope determined by delimiter given.
     *
     * @param array  $matches
     * @param string $outputArg
     * @return void
     * @throws \Exception
     */
    private function replaceMatchesIntoArg($matches, &$outputArg)
    {
        // Remove Duplicate $matches from array. Duplicate matches are replaced all in one go.
        $matches = array_unique($matches);
        foreach ($matches as $match) {
            $replacement = null;
            $delimiter = '$';
            $variable = $this->stripAndSplitReference($match, $delimiter);
            if (count($variable) != 2) {
                throw new \Exception(
                    "Invalid Persisted Entity Reference: {$match}. 
                Test persisted entity references must follow {$delimiter}entityStepKey.field{$delimiter} format."
                );
            }

            $replacement = "PersistedObjectHandler::getInstance()->retrieveEntityField";
            $replacement .= "('{$variable[0]}', '$variable[1]', '{$this->currentGenerationScope}')";

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
     *
     * @param string $match
     * @param string $argument
     * @param string $replacement
     * @return string
     */
    private function processQuoteBreaks($match, $argument, $replacement)
    {
        $outputArg = str_replace($match, '" . ' . $replacement . ' . "', $argument);

        //Sanitize string of any unnecessary '"" .' and '. ""'.
        //Regex means: Search for '"" . ' but not '\"" . '  and ' . ""'.
        //Matches on '"" . ' and ' . ""', but not on '\"" . ' and ' . "\"'.
        $outputArg = preg_replace('/(?(?<![\\\\])"" \. )| \. ""/', "", $outputArg);
        return $outputArg;
    }

    /**
     * Replaces any occurrences of stepKeys in input, if they are found within the given actionGroup.
     * Necessary to allow for use of grab/createData actions in actionGroups.
     * @param string $input
     * @param array  $actionGroupOrigin
     * @return string
     */
    private function resolveStepKeyReferences($input, $actionGroupOrigin, $matchAll = false)
    {
        if ($actionGroupOrigin == null) {
            return $input;
        }
        $output = $input;

        $actionGroup = ActionGroupObjectHandler::getInstance()->getObject(
            $actionGroupOrigin[ActionGroupObject::ACTION_GROUP_ORIGIN_NAME]
        );
        $stepKeys = $actionGroup->extractStepKeys();
        $testInvocationKey = ucfirst($actionGroupOrigin[ActionGroupObject::ACTION_GROUP_ORIGIN_TEST_REF]);

        foreach ($stepKeys as $stepKey) {
            // MQE-1011
            $stepKeyVarRef = "$" . $stepKey;
            $persistedVarRef = "PersistedObjectHandler::getInstance()->retrieveEntityField('{$stepKey}'"
                . ", 'field', 'test')";
            $persistedVarRefInvoked = "PersistedObjectHandler::getInstance()->retrieveEntityField('"
                . $stepKey . $testInvocationKey . "', 'field', 'test')";

            if (strpos($output, $stepKeyVarRef) !== false) {
                $output = str_replace($stepKeyVarRef, $stepKeyVarRef . $testInvocationKey, $output);
            }

            if (strpos($output, $persistedVarRef) !== false) {
                $output = str_replace($persistedVarRef, $persistedVarRefInvoked, $output);
            }

            if ($matchAll && strpos($output, $stepKey) !== false) {
                $output = str_replace($stepKey, $stepKey . $testInvocationKey, $output);
            }
        }
        return $output;
    }

    /**
     * Wraps all args inside function give with double quotes. Uses regex to locate arguments of function.
     *
     * @param string $functionRegex
     * @param string $input
     * @return string
     */
    private function wrapFunctionArgsWithQuotes($functionRegex, $input)
    {
        $output = $input;
        preg_match_all($functionRegex, $input, $matches);

        //If no Arguments were passed in
        if (!isset($matches[1][0])) {
            return $input;
        }

        $allArguments = explode(',', $matches[1][0]);
        foreach ($allArguments as $argument) {
            $argument = trim($argument);

            if ($argument[0] == "[") {
                $replacement = "[" . $this->addUniquenessToParamArray($argument) . "]";
            } elseif (is_numeric($argument)) {
                $replacement = $argument;
            } else {
                $replacement = $this->addUniquenessFunctionCall($argument);
            }

            //Replace only first occurrence of argument with "argument"
            $pos = strpos($output, $argument);
            $output = substr_replace($output, $replacement, $pos, strlen($argument));
        }

        return $output;
    }

    /**
     * Performs str_replace on variable reference, dependent on delimiter and returns exploded array.
     *
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
     *
     * @param TestHookObject[] $hookObjects
     * @return string
     * @throws TestReferenceException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function generateHooksPhp($hookObjects)
    {
        $hooks = "";

        foreach ($hookObjects as $hookObject) {
            $type = $hookObject->getType();
            $dependencies = 'AcceptanceTester $I';

            $hooks .= "\t/**\n";
            $hooks .= "\t  * @param AcceptanceTester \$I\n";
            $hooks .= "\t  * @throws \Exception\n";
            $hooks .= "\t  */\n";

            try {
                $steps = $this->generateStepsPhp(
                    $hookObject->getActions(),
                    TestGenerator::HOOK_SCOPE
                );
            } catch (TestReferenceException $e) {
                throw new TestReferenceException($e->getMessage() . " in Element \"" . $type . "\"");
            }

            $hooks .= sprintf("\tpublic function _{$type}(%s)\n", $dependencies);
            $hooks .= "\t{\n";
            $hooks .= $steps;
            $hooks .= "\t}\n\n";
        }

        return $hooks;
    }

    /**
     * Creates a PHP string based on a <test> block.
     * Concatenates the Test Annotations PHP and Test PHP for a single Test.
     *
     * @param TestObject $test
     * @return string
     * @throws TestReferenceException
     * @throws \Exception
     */
    private function generateTestPhp($test)
    {
        $testPhp = "";

        $testName = $test->getName();
        $testName = str_replace(' ', '', $testName);
        $testAnnotations = $this->generateAnnotationsPhp($test->getAnnotations(), true);
        $dependencies = 'AcceptanceTester $I';
        if ($test->isSkipped()) {
            $skipString = "This test is skipped due to the following issues:\\n";
            $issues = $test->getAnnotations()['skip'] ?? null;
            if (isset($issues)) {
                $skipString .= implode("\\n", $issues);
            } else {
                $skipString .= "No issues have been specified.";
            }
            $steps = "\t\t" . '$scenario->skip("' . $skipString . '");' . "\n";
            $dependencies .= ', \Codeception\Scenario $scenario';
        } else {
            try {
                $steps = $this->generateStepsPhp($test->getOrderedActions());
            } catch (\Exception $e) {
                throw new TestReferenceException($e->getMessage() . " in Test \"" . $test->getName() . "\"");
            }
        }

        $testPhp .= $testAnnotations;
        $testPhp .= sprintf("\tpublic function %s(%s)\n", $testName, $dependencies);
        $testPhp .= "\t{\n";
        $testPhp .= $steps;
        $testPhp .= "\t}\n";

        return $testPhp;
    }

    /**
     * Detects uniqueness function calls on given attribute, and calls addUniquenessFunctionCall on matches.
     *
     * @param string $input
     * @return string
     */
    private function addUniquenessToParamArray($input)
    {
        $tempInput = trim($input, "[]");
        $paramArray = explode(",", $tempInput);
        $result = [];

        foreach ($paramArray as $param) {
            // Determine if param has key/value array notation
            if (preg_match_all('/(.+)=>(.+)/', trim($param), $paramMatches)) {
                $param1 = $this->addUniquenessToParamArray($paramMatches[1][0]);
                $param2 = $this->addUniquenessToParamArray($paramMatches[2][0]);
                $result[] = trim($param1) . " => " . trim($param2);
                continue;
            }

            // Matches strings wrapped in ', we assume these are string literals
            if (preg_match('/^(["\']).*\1$/m', trim($param))) {
                $result[] = $param;
                continue;
            }

            $replacement = $this->addUniquenessFunctionCall(trim($param));

            $result[] = $replacement;
        }

        return implode(", ", $result);
    }

    /**
     * Process pressKey parameterArray attribute for uniqueness function call and necessary data resolutions
     *
     * @param string $input
     * @return string
     */
    private function processPressKey($input)
    {
        // validate the param array is in the correct format
        $input = trim($input);
        $this->validateParameterArray($input);
        // trim off the outer braces
        $input = substr($input, 1, strlen($input) - 2);

        $result = [];
        $arrayResult = [];
        $count = 0;

        // matches all arrays and replaces them with placeholder to prevent later param manipulation
        preg_match_all('/[\[][^\]]*?[\]]/', $input, $paramInput);
        if (!empty($paramInput)) {
            foreach ($paramInput[0] as $param) {
                $arrayResult[self::PRESSKEY_ARRAY_ANCHOR_KEY . $count] =
                    '[' . trim($this->addUniquenessToParamArray($param)) . ']';
                $input = str_replace($param, self::PRESSKEY_ARRAY_ANCHOR_KEY . $count, $input);
                $count++;
            }
        }

        $paramArray = explode(",", $input);
        foreach ($paramArray as $param) {
            // matches strings wrapped in ', we assume these are string literals
            if (preg_match('/^[\s]*(\'.*?\')[\s]*$/', $param)) {
                $result[] = trim($param);
                continue;
            }

            // matches \ for Facebook WebDriverKeys classes
            if (substr(trim($param), 0, 1) === '\\') {
                $result[] = trim($param);
                continue;
            }

            // matches numbers
            if (preg_match('/^[\s]*(\d+?)[\s]*$/', $param)) {
                $result[] = $param;
                continue;
            }

            $replacement = $this->addUniquenessFunctionCall(trim($param));

            $result[] = $replacement;
        }

        $result = implode(',', $result);
        // reinsert arrays into result
        if (!empty($arrayResult)) {
            foreach ($arrayResult as $key => $value) {
                $result = str_replace($key, $value, $result);
            }
        }
        return $result;
    }

    /**
     * Add uniqueness function call to input string based on regex pattern.
     *
     * @param string $input
     * @return string
     */
    private function addUniquenessFunctionCall($input)
    {
        $output = $this->wrapWithDoubleQuotes($input);

        //Match on msq(\"entityName\")
        preg_match_all('/' . EntityDataObject::CEST_UNIQUE_FUNCTION . '\(\\\\"[\w]+\\\\"\)/', $output, $matches);
        foreach (array_unique($matches[0]) as $match) {
            preg_match('/\\\\"([\w]+)\\\\"/', $match, $entityMatch);
            $entity = $entityMatch[1];
            $output = str_replace($match, '" . msq("' . $entity . '") . "', $output);
        }
        // trim unnecessary "" . and . ""
        return preg_replace('/(?(?<![\\\\])"" \. )| \. ""/', "", $output);
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
        return sprintf("$%s", ltrim($this->stripQuotes($input), '$'));
    }

    // @codingStandardsIgnoreStart

    /**
     * Wrap parameters into a function call.
     *
     * @param string $actor
     * @param actionObject $action
     * @param string $scope
     * @param array ...$args
     * @return string
     * @throws \Exception
     */
    private function wrapFunctionCall($actor, $action, ...$args)
    {
        $isFirst = true;
        $output = sprintf("\t\t$%s->%s(", $actor, $action->getType());
        for ($i = 0; $i < count($args); $i++) {
            if (null === $args[$i]) {
                continue;
            }
            if ($args[$i] === "") {
                $args[$i] = '"' . $args[$i] . '"';
            }
        }
        if (!is_array($args)) {
            $args = [$args];
        }
        $args = $this->resolveAllRuntimeReferences($args);
        $args = $this->resolveTestVariable($args, $action->getActionOrigin());
        $output .= implode(", ", array_filter($args, function($value) { return $value !== null; })) . ");\n";
        return $output;
    }

    /**
     * Wrap parameters into a function call with a return value.
     *
     * @param string $returnVariable
     * @param string $actor
     * @param string $action
     * @param string $scope
     * @param array ...$args
     * @return string
     * @throws \Exception
     */
    private function wrapFunctionCallWithReturnValue($returnVariable, $actor, $action, ...$args)
    {
        $isFirst = true;
        $output = sprintf("\t\t$%s = $%s->%s(", $returnVariable, $actor, $action->getType());
        for ($i = 0; $i < count($args); $i++) {
            if (null === $args[$i]) {
                continue;
            }
            if ($args[$i] === "") {
                $args[$i] = '"' . $args[$i] . '"';
            }
        }
        if (!is_array($args)) {
            $args = [$args];
        }
        $args = $this->resolveAllRuntimeReferences($args);
        $args = $this->resolveTestVariable($args, $action->getActionOrigin());
        $output .= implode(", ", array_filter($args, function($value) { return $value !== null; })) . ");\n";
        return $output;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Resolves {{_ENV.variable}} into getenv("variable") for test-runtime ENV referencing.
     * @param array  $args
     * @param string $regex
     * @param string $func
     * @return array
     */
    private function resolveRuntimeReference($args, $regex, $func)
    {
        $newArgs = [];

        foreach ($args as $key => $arg) {
            preg_match_all($regex, $arg, $matches);
            if (!empty($matches[0])) {
                $fullMatch = $matches[0][0];
                $refVariable = $matches[1][0];
                unset($matches);
                $replacement = "{$func}(\"{$refVariable}\")";

                $outputArg = $this->processQuoteBreaks($fullMatch, $arg, $replacement);
                $newArgs[$key] = $outputArg;
                continue;
            }
            $newArgs[$key] = $arg;
        }

        // override passed in args for use later.
        return $newArgs;
    }

    /**
     * Takes a predefined list of potentially matching special paramts and they needed function replacement and performs
     * replacements on the tests args.
     *
     * @param array $args
     * @return array
     */
    private function resolveAllRuntimeReferences($args)
    {
        $runtimeReferenceRegex = [
            "/{{_ENV\.([\w]+)}}/" => 'getenv',
            "/{{_CREDS\.([\w]+)}}/" => 'CredentialStore::getInstance()->getSecret'
        ];

        $argResult = $args;
        foreach ($runtimeReferenceRegex as $regex => $func) {
            $argResult = $this->resolveRuntimeReference($argResult, $regex, $func);
        }

        return $argResult;
    }

    /**
     * Validates parameter array format, making sure user has enclosed string with square brackets.
     *
     * @param string $paramArray
     * @return void
     * @throws TestReferenceException
     */
    private function validateParameterArray($paramArray)
    {
        if (substr($paramArray, 0, 1) != "[" || substr($paramArray, strlen($paramArray) - 1, 1) != "]") {
            throw new TestReferenceException("parameterArray must begin with `[` and end with `]");
        }
    }

    /**
     * Resolve value based on type.
     *
     * @param string $value
     * @param string $type
     * @return string
     * @throws TestReferenceException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function resolveValueByType($value, $type)
    {
        //TODO: Refactor to deal with PHPMD.CyclomaticComplexity, and remove @SuppressWarnings
        if (null === $value) {
            return null;
        }
        if (null === $type) {
            $type = 'const';
        }
        if ($type == "string") {
            return $this->addUniquenessFunctionCall($value);
        } elseif ($type == "bool") {
            return $this->toBoolean($value) ? "true" : "false";
        } elseif ($type == "int" || $type == "float") {
            return $this->toNumber($value);
        } elseif ($type == "array") {
            $this->validateParameterArray($value);
            return "[" . $this->addUniquenessToParamArray($value) . "]";
        } elseif ($type == "variable") {
            return $this->addDollarSign($value);
        } else {
            return $value;
        }
    }

    /**
     * Convert input string to boolean equivalent.
     *
     * @param string $inStr
     * @return boolean|null
     */
    private function toBoolean($inStr)
    {
        return boolval($this->stripQuotes($inStr));
    }

    /**
     * Convert input string to number equivalent.
     *
     * @param string $inStr
     * @return integer|float|null
     */
    private function toNumber($inStr)
    {
        $outStr = $this->stripQuotes($inStr);
        if (strpos($outStr, localeconv()['decimal_point']) === false) {
            return intval($outStr);
        } else {
            return floatval($outStr);
        }
    }

    /**
     * Strip single or double quotes from begin and end of input string.
     *
     * @param string $inStr
     * @return string
     */
    private function stripQuotes($inStr)
    {
        $unquoted = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $inStr);
        return $unquoted;
    }

    /**
     * Validate action attributes are either not set at all or only one is set for a given rule.
     *
     * @param string $key
     * @param string $tagName
     * @param array  $attributes
     * @return void
     */
    private function validateXmlAttributesMutuallyExclusive($key, $tagName, $attributes)
    {
        $rules = [
            [
                'attributes' => [
                    'selector',
                    'selectorArray',
                ]
            ],
            [
                'attributes' => [
                    'url',
                    'userInput',
                    'variable',
                ],
                'excludes' => [
                    'dontSeeLink',
                    'seeLink',
                ],
            ],
            [
                'attributes' => [
                    'userInput',
                    'parameterArray',
                    'variable'
                ],
                'excludes' => [
                    'dontSeeCookie',
                    'grabCookie',
                    'resetCookie',
                    'seeCookie',
                    'setCookie',
                ],
            ],
        ];
        foreach ($rules as $rule) {
            if (isset($rule['excludes']) && in_array($tagName, $rule['excludes'])) {
                continue;
            }
            $count = 0;
            foreach ($rule['attributes'] as $attribute) {
                if (isset($attributes[$attribute])) {
                    $count++;
                }
            }
            if ($count > 1) {
                $this->printRuleErrorToConsole($key, $tagName, $rule['attributes']);
            }
        }
    }

    /**
     * Print rule violation message to console.
     *
     * @param string $key
     * @param string $tagName
     * @param array  $attributes
     * @return void
     */
    private function printRuleErrorToConsole($key, $tagName, $attributes)
    {
        if (empty($tagName) || empty($attributes)) {
            return;
        }
        $message = 'On step with stepKey "' . $key . '", only one of the attributes: "';
        $message .= implode('", "', $attributes);
        $message .= '" can be use for action "' . $tagName . "\".\n";
        print $message;
    }
}
