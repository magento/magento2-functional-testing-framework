<?php
namespace Magento\AcceptanceTestFramework\Generate;

/**
 * Block classes generator.
 */
class GenerateBlock extends AbstractGenerate
{
    /**
     * Launch generation of all block classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;
        $this->start = microtime(true);
        foreach ($this->configData->get('block') as $name => $data) {
            $this->generateClass($name, $data);
        }
        $this->end = microtime(true);
        $time = $this->end - $this->start;

        \Magento\AcceptanceTestFramework\Generate\GenerateResult::addResult('Block Classes', $this->cnt, $time);
    }

    /**
     * Generate single block class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        $classNameParts = explode('\\', $className);
        $classDataKey = 'block/' . end($classNameParts);

        if (!$this->configData->get($classDataKey)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            end($classNameParts), $this->configData->get($classDataKey)
        );
    }

    /**
     * Generate block class from XML source.
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($name, array $data)
    {
        $class = str_replace('/', '\\', $name);
        $className = $this->getShortClassName($class);
        $parentClass = "Magento\\AcceptanceTestFramework\\Page\\Block\\Block";
        if (isset($data['parent_class'])) {
            $parentClass = $data['parent_class'];
        }
        $parentClassName = $this->getShortClassName($parentClass);

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$this->getNamespace($class)};\n\n";
        $content .= "use {$parentClass};\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$parentClassName}\n";
        $content .= "{\n";

        if (isset($data['element'])) {
            $elements = $data['element'];
            foreach ($elements as $element => $attributes) {
                $attributes['locator'] = str_replace('\'', '"', $attributes['locator']);
                /**
                 * Generate contents:
                 *     public static $productName = '.admin__field[data-index=name] input';
                 */
                $content .= "    public static $$element = '" . $attributes['locator'] . "';\n";
            }
            foreach ($elements as $element => $attributes) {
                switch ($attributes['type']) {
                    case 'input':
                        $content .= $this->generateCodeForInputAction($element, $attributes);
                        break;
                    case 'text':
                        $content .= $this->generateCodeForTextAction($element, $attributes);
                        break;
                    case 'button':
                        $content .= $this->generateCodeForButtonAction($element, $attributes);
                        break;
                    case 'checkbox':
                        $content .= $this->generateCodeForCheckboxAction($element, $attributes);
                        break;
                    case 'radio':
                        $content .= $this->generateCodeForRadioAction($element, $attributes);
                        break;
                    case 'select':
                        $content .= $this->generateCodeForSelectAction($element, $attributes);
                        break;
                    case 'multiselect':
                        $content .= $this->generateCodeForMultiSelectAction($element, $attributes);
                        break;
                    default:
                        break;
                }
            }
        }
        $content .= "}\n";

        return $this->createClass($class, $content);
    }

    /**
     * Generate code for input action; and
     * generate code for asserting value in input field; and
     * generate code for returning text from input field; and
     * generate waitForPageLoad() if it's needed.
     *
     * public function fillFieldProductName($value)
     * {
     *     $this->acceptanceTester->fillField(self::$productName, $value);
     * }
     *
     * public function seeProductName($expectedValue)
     * {
     *     $this->acceptanceTester->seeInField(self::$productName, $expectedValue);
     * }
     *
     * public function grabTextFromProductName($expectedValue)
     * {
     *     $result = $this->acceptanceTester->grabTextFrom(self::$productName);
     *     return $result;
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForInputAction($element, $attributes)
    {
        $waitRequired = false;
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $waitRequired = true;
        }
        $content = "\n";
        $content .= "    public function fillField" . ucfirst($element). "(\$value)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->fillField(self::$$element, \$value);\n";
        if ($waitRequired) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    public function seeInField" . ucfirst($element). "(\$expectedValue)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->seeInField(self::$$element, \$expectedValue);\n";
        if ($waitRequired) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        $content .= "\n";
        $content .= "    public function grabTextFrom" . ucfirst($element). "()\n";
        $content .= "    {\n";
        $content .= "        \$result = \$this->acceptanceTester->grabTextFrom(self::$$element);\n";
        if ($waitRequired) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "        return \$result;\n";
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for returning text from text field; and generate waitForPageLoad() if it's needed.
     *
     * public function grabTextFromProductName($expectedValue)
     * {
     *     $result = $this->acceptanceTester->grabTextFrom(self::$productName);
     *     $this->acceptanceTester->waitForPageLoad();
     *     return $result
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForTextAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function grabTextFrom" . ucfirst($element). "()\n";
        $content .= "    {\n";
        $content .= "        \$result = \$this->acceptanceTester->grabTextFrom(self::$$element);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "        return \$result;\n";
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for performing button action; and generate waitForPageLoad() if it's needed.
     *
     * public function clickButtonMyButton()
     * {
     *     $this->acceptanceTester->click(self::$myButton);
     *     $this->acceptanceTester->waitForPageLoad();
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForButtonAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function clickButton" . ucfirst($element). "()\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->click(self::$$element);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for performing checkbox action; and generate waitForPageLoad() if it's needed.
     *
     * public function clickCheckBoxMyCheckboxButton($optionsArray)
     * {
     *     $this->acceptanceTester->checkOption(self::$myCheckboxButton, $optionsArray);
     *     $this->acceptanceTester->waitForPageLoad();
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForCheckboxAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function clickCheckBox" . ucfirst($element). "(\$optionsArray)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->checkOption(self::$$element, \$optionsArray);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for performing radio button action; and generate waitForPageLoad() if it's needed.
     *
     * public function clickRadioButtonMyRadioButton($option)
     * {
     *     $this->acceptanceTester->selectOption(self::$myRadioButton, $option);
     *     $this->acceptanceTester->waitForPageLoad();
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForRadioAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function clickRadioButton" . ucfirst($element). "(\$option)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->selectOption(self::$$element, \$option);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for performing dropdown select action; and generate waitForPageLoad() if it's needed.
     *
     * public function selectOptionMySelectButton($option)
     * {
     *     $this->acceptanceTester->selectOption(self::$mySelectButton, $option);
     *     $this->acceptanceTester->waitForPageLoad();
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForSelectAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function selectOption" . ucfirst($element). "(\$option)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->selectOption(self::$$element, \$option);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        return $content;
    }

    /**
     * Generate code for performing search and multi-select action; and generate waitForPageLoad() if it's needed.
     * e.g. Categories multi-select element
     *
     * public function selectOptionMySelectButton($optionArray, $requireAction = false)
     * {
     *     $this->acceptanceTester->searchAndMultiSelectOption(self::$mySelectButton, $optionArray, $requireAction = false);
     *     $this->acceptanceTester->waitForPageLoad();
     * }
     *
     * @param string $element
     * @param array $attributes
     * @return string
     */
    private function generateCodeForMultiSelectAction($element, $attributes)
    {
        $content = "\n";
        $content .= "    public function clickCheckBox" . ucfirst($element). "(\$optionsArray)\n";
        $content .= "    {\n";
        $content .= "        \$this->acceptanceTester->searchAndMultiSelectOption(self::$$element, \$optionsArray, \$requireAction = false);\n";
        if (isset($attributes['wait_required']) && $attributes['wait_required']) {
            $content .= "        \$this->acceptanceTester->waitForPageLoad();\n";
        }
        $content .= "    }\n";
        return $content;
    }
}
