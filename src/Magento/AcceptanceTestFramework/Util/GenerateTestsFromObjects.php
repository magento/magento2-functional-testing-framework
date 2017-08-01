<?php

use Magento\AcceptanceTestFramework\Test\Managers\CestArrayProcessor;
require_once '../../../../bootstrap.php';

function loadAllCestObjects()
{
    $cestOutput = CestArrayProcessor::getInstance()->getCestData();
    return $cestOutput;
}

function createCestFile($cestPhp, $filename)
{
    $exportDirectory = TESTS_BP . "/tests/acceptance/Magento/AcceptanceTest/_generated";
    $exportFilePath  = sprintf("%s/%s.php", $exportDirectory, $filename);

    if (!is_dir($exportDirectory))
    {
        mkdir($exportDirectory, 0777, true);
    }

    $file = fopen($exportFilePath, 'w') or die('Unable to open file!');
    fwrite($file, $cestPhp);
    fclose($file);
}

function createAllCestFiles()
{
    $cestPhpArray = assembleAllCestPhp();

    foreach ($cestPhpArray as $cestPhpFile)
    {
        createCestFile($cestPhpFile[1], $cestPhpFile[0]);
    }
}

function assembleCestPhp($cestObject)
{
    $usePhp              = generateUseStatementsPhp();
    $classAnnotationsPhp = generateClassAnnotationsPhp($cestObject->getAnnotations());
    $className           = $cestObject->getName();
    $hookPhp             = generateHooksPhp($cestObject->getHooks());
    $testsPhp            = generateTestsPhp($cestObject->getTests());

    $cestPhp  = "<?php\n";
    $cestPhp .= "namespace Magento\AcceptanceTest\Backend;\n\n";
    $cestPhp .= $usePhp;
    $cestPhp .= $classAnnotationsPhp;
    $cestPhp .= sprintf("class %s\n", $className);
    $cestPhp .= "{\n";
    $cestPhp .= $hookPhp;;
    $cestPhp .= $testsPhp;
    $cestPhp .= "}\n";

    return $cestPhp;
}

function assembleAllCestPhp()
{
    $cestObjects  = loadAllCestObjects();
    $cestPhpArray = [];

    foreach ($cestObjects as $cest)
    {
        $name = $cest->getName();
        $php  = assembleCestPhp($cest);
        $cestPhpArray[] = [$name, $php];
    }

    return $cestPhpArray;
}

function generateUseStatementsPhp()
{
    $useStatementsPhp = "use Magento\AcceptanceTestFramework\AcceptanceTester;\n";

    $allureStatements = ["Yandex\Allure\Adapter\Annotation\Features;",
        "Yandex\Allure\Adapter\Annotation\Stories;",
        "Yandex\Allure\Adapter\Annotation\Title;",
        "Yandex\Allure\Adapter\Annotation\Description;",
        "Yandex\Allure\Adapter\Annotation\Parameter;",
        "Yandex\Allure\Adapter\Annotation\Severity;",
        "Yandex\Allure\Adapter\Model\SeverityLevel;",
        "Yandex\Allure\Adapter\Annotation\TestCaseId;\n"];

    foreach ($allureStatements as $allureUseStatement)
    {
        $useStatementsPhp .= sprintf("use %s\n", $allureUseStatement);
    }

    return $useStatementsPhp;
}

function generateClassAnnotationsPhp($classAnnotationsObject)
{
    $classAnnotationsPhp = "/**\n";

    foreach ($classAnnotationsObject as $annotationType => $annotationName)
    {
        if ($annotationType == "features")
        {
            $features = "";

            foreach ($annotationName as $name)
            {
                $features .= sprintf("\"%s\"", $name);

                if (next($annotationName))
                {
                    $features .= ", ";
                }
            }

            $classAnnotationsPhp .= sprintf(" * @Features({%s})\n", $features);
        }

        if ($annotationType == "stories")
        {
            $stories = "";

            foreach ($annotationName as $name)
            {
                $stories .= sprintf("\"%s\"", $name);

                if (next($annotationName))
                {
                    $stories .= ", ";
                }
            }

            $classAnnotationsPhp .= sprintf(" * @Stories({%s})\n", $stories);
        }

        if ($annotationType == "title")
        {
            $classAnnotationsPhp .= sprintf(" * @Title(\"%s\")\n", ucwords($annotationType), $annotationName[0]);
        }

        if ($annotationType == "description")
        {
            $classAnnotationsPhp .= sprintf(" * @Description(\"%s\")\n", $annotationName[0]);
        }

        if ($annotationType == "severity")
        {
            $classAnnotationsPhp .= sprintf(" * @Severity(level = SeverityLevel::%s)\n", $annotationName[0]);
        }

        if ($annotationType == "testCaseId")
        {
            $classAnnotationsPhp .= sprintf(" * TestCaseId(\"%s\")", $annotationName[0]);
        }

        if ($annotationType == "group")
        {
            foreach ($annotationName as $group)
            {
                $classAnnotationsPhp .= sprintf(" * @group %s\n", $group);
            }
        }

        if ($annotationType == "env")
        {
            foreach ($annotationName as $env)
            {
                $classAnnotationsPhp .= sprintf(" * @env %s\n", $env);
            }
        }
    }

    $classAnnotationsPhp .= " */\n";

    return $classAnnotationsPhp;
}

function generateStepsPhp($stepsObject)
{
    $testSteps = "";

    foreach ($stepsObject as $steps)
    {
        $actor          = "I";
        $actionName     = $steps->getType();
        $selector       = null;
        $input          = null;
        $parameterArray = null;
        $returnVariable = null;
        $x              = null;
        $y              = null;
        $html           = null;
        $url            = null;
        $function       = null;
        $time           = null;

        if (isset($steps->getCustomActionAttributes()['returnVariable'])) {
            $returnVariable = $steps->getCustomActionAttributes()['returnVariable'];
        }

        if (isset($steps->getCustomActionAttributes()['url']) && isset($steps->getCustomActionAttributes()['userInput'])) {
            $input = sprintf("\"%s\"", $steps->getCustomActionAttributes()['userInput']);
        } else if (isset($steps->getCustomActionAttributes()['userInput'])) {
            $input = sprintf("\"%s\"", $steps->getCustomActionAttributes()['userInput']);
        } else if (isset($steps->getCustomActionAttributes()['url'])) {
            $input = sprintf("\"%s\"", $steps->getCustomActionAttributes()['url']);
        } else if (isset($steps->getCustomActionAttributes()['time'])) {
            $input = sprintf("\"%s\"", $steps->getCustomActionAttributes()['time']);
        }

        if (isset($steps->getCustomActionAttributes()['parameterArray'])) {
            $parameterArray = $steps->getCustomActionAttributes()['parameterArray'];
        }

        if (isset($steps->getCustomActionAttributes()['selectorArray'])) {
            $selector = sprintf("%s", $steps->getCustomActionAttributes()['selectorArray']);
        } else if (isset($steps->getCustomActionAttributes()['selector'])) {
            $selector = sprintf("\"%s\"", $steps->getCustomActionAttributes()['selector']);
        }

        if (isset($steps->getCustomActionAttributes()['x'])) {
            $x = $steps->getCustomActionAttributes()['x'];
        }

        if (isset($steps->getCustomActionAttributes()['y'])) {
            $y = $steps->getCustomActionAttributes()['y'];
        }

        if (isset($steps->getCustomActionAttributes()['function'])) {
            $function = $steps->getCustomActionAttributes()['function'];
        }

        if (isset($steps->getCustomActionAttributes()['html'])) {
            $html = $steps->getCustomActionAttributes()['html'];
        }

        if (isset($steps->getCustomActionAttributes()['time'])) {
            $time = $steps->getCustomActionAttributes()['time'];
        }

        switch ($actionName) {
            case "amOnPage":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "amOnSubdomain":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "amOnUrl":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "click":
                if ($input && $selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $selector);
                } else if ($input && !$selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                } else if (!$input && $selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "clickWithLeftButton":
                if ($selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $selector, $x, $y);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(null, %s, %s);\n", $actor, $actionName, $x, $y);
                }
                break;
            case "clickWithRightButton":
                if ($selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $selector, $x, $y);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(null, %s, %s);\n", $actor, $actionName, $x, $y);
                }
                break;
            case "dontSee":
                if ($selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $selector);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "dontSeeCookie":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "dontSeeCurrentUrlEquals":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "dontSeeCurrentUrlMatches":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "dontSeeElement":
                if ($parameterArray) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "dontSeeElementInDOM":
                if ($parameterArray) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "dontSeeInCurrentUrl":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "dontSeeInField":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $input);
                break;
            case "dontSeeInFormFields":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                break;
            case "dontSeeInSource":
                // TODO: Solve the HTML parsing issue.
                $testSteps .= sprintf("\t\t$%s->%s(\"%s\");\n", $actor, $actionName, $html);
                break;
            case "dontSeeLink":
                if (isset($steps->getCustomActionAttributes()['url'])) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\");\n", $actor, $actionName, $input, $steps->getCustomActionAttributes()['url']);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "dragAndDrop":
                $testSteps .= sprintf("\t\t$%s->%s(\"%s\", \"%s\");\n", $actor, $actionName, $steps->getCustomActionAttributes()['selector1'], $steps->getCustomActionAttributes()['selector2']);
                break;
            case "executeInSelenium":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $function);
                break;
            case "executeJS":
                $testSteps .= sprintf("\t\t$%s->%s(\"%s\");\n", $actor, $actionName, $function);
                break;
            case "fillField":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $input);
                break;
            case "grabCookie":
                if (isset($returnVariable)) {
                    if (isset($parameterArray)) {
                        $testSteps .= sprintf("\t\t$%s = $%s->%s(%s, %s);\n", $returnVariable, $actor, $actionName, $input, $parameterArray);
                    } else {
                        $testSteps .= sprintf("\t\t$%s = $%s->%s(%s);\n", $returnVariable, $actor, $actionName, $input);
                    }
                } else {
                    if (isset($parameterArray)) {
                        $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $parameterArray);
                    } else {
                        $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                    }
                }
                break;
            case "grabFromCurrentUrl":
                if (isset($returnVariable)) {
                    if ($input) {
                        $testSteps .= sprintf("\t\t$%s = $%s->%s(%s);\n", $returnVariable, $actor, $actionName, $input);
                    } else {
                        $testSteps .= sprintf("\t\t$%s = $%s->%s();\n", $returnVariable, $actor, $actionName);
                    }
                } else {
                    if ($input) {
                        $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                    } else {
                        $testSteps .= sprintf("\t\t$%s->%s();\n", $actor, $actionName);
                    }
                }
                break;
            case "grabValueFrom":
                if (isset($returnVariable)) {
                    $testSteps .= sprintf("\t\t$%s = $%s->%s(%s);\n", $returnVariable, $actor, $actionName, $selector);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "moveMouseOver":
                if ($selector) {
                    if (isset($step['x']) || isset($step['y'])) {
                        $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $selector, $x, $y);
                    } else {
                        $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                    }
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(null, %s, %s);\n", $actor, $actionName, $x, $y);
                }
                break;
            case "performOn":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $function);
                break;
            case "pressKey":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $input);
                }
                break;
            case "resetCookie":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "resizeWindow":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $steps->getCustomActionAttributes()['width'], $steps->getCustomActionAttributes()['height']);
                break;
            case "scrollTo":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $selector, $x, $y);
                break;
            case "see":
                if (isset($steps->getCustomActionAttributes()['selector'])) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $selector);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "seeCookie":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "seeCurrentUrlEquals":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "seeCurrentUrlMatches":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "seeElement":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "seeElementInDOM":
                if (isset($parameterArray)) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                }
                break;
            case "seeInCurrentUrl":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                break;
            case "seeInField":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $input);
                break;
            case "seeInFormFields":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                break;
            case "seeInPageSource":
                // TODO: Solve the HTML parsing issue.
                break;
            case "seeInSource":
                // TODO: Solve the HTML parsing issue.
                break;
            case "seeLink":
                if (isset($step['url'])) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\");\n", $actor, $actionName, $input, $steps->getCustomActionAttributes()['url']);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $input);
                }
                break;
            case "seeNumberOfElements":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $steps->getCustomActionAttributes()['userInput']);
                break;
            case "selectOption":
                if ($parameterArray) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $input);
                }
                break;
            case "setCookie":
                if ($parameterArray) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\", %s);\n", $actor, $actionName, $input, $steps->getCustomActionAttributes()['value'], $parameterArray);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\");\n", $actor, $actionName, $input, $steps->getCustomActionAttributes()['value']);
                }
                break;
            case "submitForm":
                if (isset($step['button'])) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\", \"%s\");\n", $actor, $actionName, $selector, $parameterArray, $steps->getCustomActionAttributes()['button']);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\");\n", $actor, $actionName, $selector, $parameterArray);
                }
                break;
            case "wait":
                $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $time);
                break;
            case "waitForElement":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $time);
                break;
            case "waitForElementChange":
                $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $selector, $function, $time);
                break;
            case "waitForJS":
                $testSteps .= sprintf("\t\t$%s->%s(\"%s\", %s);\n", $actor, $actionName, $function, $time);
                break;
            case "waitForText":
                if ($selector) {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s, %s);\n", $actor, $actionName, $input, $time, $selector);
                } else {
                    $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $input, $time);
                }
                break;
            default:
                if ($returnVariable) {
                    if ($selector) {
                        if (isset($steps->getCustomActionAttributes()['userInput'])) {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s(%s, \"%s\");\n", $returnVariable, $actor, $actionName, $selector, $steps->getCustomActionAttributes()['userInput']);
                        } else if (isset($steps->getCustomActionAttributes()['parameter'])) {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s(%s, %s);\n", $returnVariable, $actor, $actionName, $selector, $steps->getCustomActionAttributes()['parameter']);
                        } else {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s(%s);\n", $returnVariable, $actor, $actionName, $selector);
                        }
                    } else {
                        if (isset($steps->getCustomActionAttributes()['userInput'])) {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s(\"%s\");\n", $returnVariable, $actor, $actionName, $steps->getCustomActionAttributes()['userInput']);
                        } else if (isset($steps->getCustomActionAttributes()['parameter'])) {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s(%s);\n", $returnVariable, $actor, $actionName, $steps->getCustomActionAttributes()['parameter']);
                        } else {
                            $testSteps .= sprintf("\t\t$%s = $%s->%s();\n", $returnVariable, $actor, $actionName);
                        }
                    }
                } else {
                    if ($selector) {
                        if (isset($steps->getCustomActionAttributes()['userInput'])) {
                            $testSteps .= sprintf("\t\t$%s->%s(%s, \"%s\");\n", $actor, $actionName, $selector, $steps->getCustomActionAttributes()['userInput']);
                        } else if (isset($steps->getCustomActionAttributes()['parameter'])) {
                            $testSteps .= sprintf("\t\t$%s->%s(%s, %s);\n", $actor, $actionName, $selector, $steps->getCustomActionAttributes()['parameter']);
                        } else {
                            $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $selector);
                        }
                    } else {
                        if (isset($steps->getCustomActionAttributes()['userInput'])) {
                            $testSteps .= sprintf("\t\t$%s->%s(\"%s\");\n", $actor, $actionName, $steps->getCustomActionAttributes()['userInput']);
                        } else if (isset($steps->getCustomActionAttributes()['parameter'])) {
                            $testSteps .= sprintf("\t\t$%s->%s(%s);\n", $actor, $actionName, $steps->getCustomActionAttributes()['parameter']);
                        } else {
                            $testSteps .= sprintf("\t\t$%s->%s();\n", $actor, $actionName);
                        }
                    }
                }
        }
    }

    return $testSteps;
}

function generateHooksPhp($hookObjects)
{
    $hooks = "";
    foreach ($hookObjects as $hookObject)
    {
        $type         = $hookObject->getType();
        $dependencies = 'AcceptanceTester $I';
        $steps        = generateStepsPhp($hookObject->getActions());

        if ($type == "after")
        {
            $hooks .= sprintf("\tpublic function _after(%s)\n", $dependencies);
            $hooks .= "\t{\n";
            $hooks .= $steps;
            $hooks .= "\t}\n\n";
        }

        if ($type == "before")
        {
            $hooks .= sprintf("\tpublic function _before(%s)\n", $dependencies);
            $hooks .= "\t{\n";
            $hooks .= $steps;
            $hooks .= "\t}\n\n";
        }

        $hooks .= "";
    }

    return $hooks;
}

function generateTestAnnotationsPhp($testAnnotationsObject)
{
    $testAnnotationsPhp = "\t/**\n";

    foreach ($testAnnotationsObject as $annotationType => $annotationName)
    {
        if ($annotationType == "features")
        {
            $features = "";

            foreach ($annotationName as $name)
            {
                $features .= sprintf("\"%s\"", $name);

                if (next($annotationName))
                {
                    $features .= ", ";
                }
            }

            $testAnnotationsPhp .= sprintf("\t * @Features({%s})\n", $features);
        }

        if ($annotationType == "stories")
        {
            $stories = "";

            foreach ($annotationName as $name)
            {
                $stories .= sprintf("\"%s\"", $name);

                if (next($annotationName))
                {
                    $stories .= ", ";
                }
            }

            $testAnnotationsPhp .= sprintf("\t * @Stories({%s})\n", $stories);
        }

        if ($annotationType == "title")
        {
            $testAnnotationsPhp .= sprintf("\t * @Title(\"%s\")\n", ucwords($annotationType), $annotationName[0]);
        }

        if ($annotationType == "description")
        {
            $testAnnotationsPhp .= sprintf("\t * @Description(\"%s\")\n", $annotationName[0]);
        }

        if ($annotationType == "severity")
        {
            $testAnnotationsPhp .= sprintf("\t * @Severity(level = SeverityLevel::%s)\n", $annotationName[0]);
        }

        if ($annotationType == "testCaseId")
        {
            $testAnnotationsPhp .= sprintf("\t * @TestCaseId(\"%s\")\n", $annotationName[0]);
        }
    }

    $testAnnotationsPhp .= sprintf("\t * @Parameter(name = \"%s\", value=\"$%s\")\n", "AcceptanceTester", "I");

    foreach ($testAnnotationsObject as $annotationType => $annotationName) {
        if ($annotationType == "group")
        {
            foreach ($annotationName as $name)
            {
                $testAnnotationsPhp .= sprintf("\t * @group %s\n", $name);
            }
        }

        if ($annotationType == "env")
        {
            foreach ($annotationName as $env)
            {
                $testAnnotationsPhp .= sprintf("\t * @env %s\n", $env);
            }
        }
    }

    $testAnnotationsPhp .= sprintf("\t * @param %s $%s\n", "AcceptanceTester", "I");
    $testAnnotationsPhp .= "\t * @return void\n";
    $testAnnotationsPhp .= "\t */\n";

    return $testAnnotationsPhp;
}

function generateTestsPhp($testsObject)
{
    $testPhp = "";

    foreach ($testsObject as $test)
    {
        $testName        = $test->getName();
        $testAnnotations = generateTestAnnotationsPhp($test->getAnnotations());
        $dependencies    = 'AcceptanceTester $I';
        $steps           = generateStepsPhp($test->getOrderedActions());

        $testPhp        .= $testAnnotations;
        $testPhp        .= sprintf("\tpublic function %s(%s)\n", $testName, $dependencies);
        $testPhp        .= "\t{\n";
        $testPhp        .= $steps;
        $testPhp        .= "\t}\n";

        if (sizeof($testsObject) > 1)
        {
            $testPhp .= "\n";
        }
    }

    return $testPhp;
}

createAllCestFiles();
