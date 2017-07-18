<?php

use Magento\AcceptanceTestFramework\Test\CestDataManager;
require_once '../../../../entryPoint.php';

function loadAllCestObjects()
{
    $cestOutput = CestDataManager::getCestData();
    return $cestOutput;
}

function createAllCestFiles()
{
    $cestPhpArray = assembleAllCestPhp();

    foreach ($cestPhpArray as $cestPhpFile)
    {
        createCestFile($cestPhpFile[1], $cestPhpFile[0]);
    }
}

createAllCestFiles();

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

function assembleCestPhp($cestObject)
{
    $namespacePath       = "Magento\AcceptanceTest\Backend";
    $usePhp              = generateUseStatementsPhp($cestObject->getUseStatements());
    $classAnnotationsPhp = generateClassAnnotationsPhp($cestObject->getAnnotations());
    $className           = $cestObject->getName();
    $hookPhp             = generateHooksPhp($cestObject->getHooks());
    $testsPhp            = generateTestsPhp($cestObject->getTests());

    $cestPhp  = "<?php\n";
    $cestPhp .= sprintf("namespace %s;\n\n", $namespacePath);
    $cestPhp .= $usePhp;
    $cestPhp .= $classAnnotationsPhp;
    $cestPhp .= sprintf("class %s\n", $className);
    $cestPhp .= "{\n";
    $cestPhp .= $hookPhp;;
    $cestPhp .= $testsPhp;
    $cestPhp .= "}\n";

    return $cestPhp;
}

function generateUseStatementsPhp($useStatementsObject)
{
    $useStatementsPhp = "";
    $allureStatements = ["Yandex\Allure\Adapter\Annotation\Features;",
                         "Yandex\Allure\Adapter\Annotation\Stories;",
                         "Yandex\Allure\Adapter\Annotation\Title;",
                         "Yandex\Allure\Adapter\Annotation\Description;",
                         "Yandex\Allure\Adapter\Annotation\Parameter;",
                         "Yandex\Allure\Adapter\Annotation\Severity;",
                         "Yandex\Allure\Adapter\Model\SeverityLevel;",
                         "Yandex\Allure\Adapter\Annotation\TestCaseId;\n"];

    foreach ($useStatementsObject as $useStatement)
    {
        if ($useStatement == 'AcceptanceTester')
        {
            $useStatementsPhp .= sprintf("use Magento\%s;\n", $useStatement);
        } else {
            //Edited to fit Page Objects, may use old paths again if we revisit this
            $useStatementsPhp .= sprintf("use Magento\%s;\n", $useStatement);
        }
    }

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

function generateDependencyString($dependenciesObject)
{
    $dependencyString = "";

    if ($dependenciesObject)
    {
        foreach ($dependenciesObject as $name => $actor)
        {
            $dependencyString .= sprintf("%s $%s", $name, $actor);

            if (next($dependenciesObject))
            {
                $dependencyString .= ", ";
            }
        }
    }

    return $dependencyString;
}

function generateStepsPhp($stepsObject)
{
    $stepsPhp = "";

    foreach ($stepsObject as $steps)
    {
        $actor          = $steps->getActor();
        $returnVariable = $steps->getReturnVariable();
        $function       = $steps->getFunction();
        $parameter      = $steps->getParameter();
        $userInput      = $steps->getUserInput();
        $selector       = $steps->getSelector();

        if (!$actor)
        {
            $actor = "I";
        }

        if ($returnVariable)
        {
            if ($userInput)
            {
                $stepsPhp .= sprintf("\t\t$%s = $%s->%s(\"%s\", \"%s\");\n", $returnVariable, $actor, $function, $selector, $userInput);
            } else if ($parameter)
            {
                $stepsPhp .= sprintf("\t\t$%s = $%s->%s(\"%s\", %s);\n", $returnVariable, $actor, $function, $selector, $parameter);
            } else if ($selector)
            {
                $stepsPhp .= sprintf("\t\t$%s = $%s->%s(\"%s\");\n", $returnVariable, $actor, $function, $selector);
            } else
            {
                $stepsPhp .= sprintf("\t\t$%s = $%s->%s();\n", $returnVariable, $actor, $function);
            }
        } else if ($selector)
        {
            if ($userInput)
            {
                $stepsPhp .= sprintf("\t\t$%s->%s(\"%s\", \"%s\");\n", $actor, $function, $selector, $userInput);
            } else if ($parameter)
            {
                $stepsPhp .= sprintf("\t\t$%s->%s(\"%s\", %s);\n", $actor, $function, $selector, $parameter);
            } else
            {
                $stepsPhp .= sprintf("\t\t$%s->%s(\"%s\");\n", $actor, $function, $selector);
            }
        } else
        {
            if ($userInput)
            {
                $stepsPhp .= sprintf("\t\t$%s->%s(\"%s\");\n", $actor, $function, $userInput);
            } else if ($parameter)
            {
                $stepsPhp .= sprintf("\t\t$%s->%s(%s);\n", $actor, $function, $parameter);
            } else
            {
                $stepsPhp .= sprintf("\t\t$%s->%s();\n", $actor, $function);
            }
        }
    }

    return $stepsPhp;
}

function generateHooksPhp($hookObjects)
{
    $hooks = "";
    foreach ($hookObjects as $hookObject)
    {
        foreach ($hookObject as $hookItem)
        {
            $type         = $hookItem->getType();
            $dependencies = generateDependencyString($hookItem->getDependencies());
            $steps        = generateStepsPhp($hookItem->getActions());

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
    }

    return $hooks;
}

function generateTestAnnotationsPhp($testAnnotationsObject, $testDependenciesObject)
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

    foreach ($testDependenciesObject as $name => $actor)
    {
        if ($name)
        {
            $testAnnotationsPhp .= sprintf("\t * @Parameter(name = \"%s\", value = \"$%s\")\n", $name, $actor);
        }
    }

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

    foreach ($testDependenciesObject as $name => $actor)
    {
        if ($name)
        {
            $testAnnotationsPhp .= sprintf("\t * @param %s $%s\n", $name, $actor);
        }
    }

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
        $testAnnotations = generateTestAnnotationsPhp($test->getAnnotations(), $test->getDependencies());
        $dependencies    = generateDependencyString($test->getDependencies());
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