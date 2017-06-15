<?php
namespace Magento\AcceptanceTestFramework\Generate;

/**
 * Page classes generator.
 */
class GeneratePage extends AbstractGenerate
{
    /**
     * Launch generation of all page classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->get('page') as $name => $data) {
            $this->generateClass($name, $data);
        }

        \Magento\AcceptanceTestFramework\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Generate single page class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        $classNameParts = explode('\\', $className);
        $classDataKey = 'page/' . end($classNameParts);

        if (!$this->configData->get($classDataKey)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            end($classNameParts), $this->configData->get($classDataKey)
        );
    }

    /**
     * Generate page class from XML source.
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($name, array $data)
    {
        $className = ucfirst($name);
        $mca = isset($data['mca']) ? $data['mca'] : '';
        $area = isset($data['area']) ? $data['area'] : '';
        $areaPage = $this->getParentPage($area, $mca);
        $folderPath = str_replace('_', '/AcceptanceTest/', $data['module']) . '/Page';
        $folderPath .= (empty($area) ? '' : ('/' . $area));
        $class = str_replace('/', '\\', $folderPath . '/' . $className);
        $blocks = isset($data['block']) ? $data['block'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$this->getNamespace($class)};\n\n";
        $content .= "use Magento\\AcceptanceTestFramework\\Page\\{$areaPage};\n\n";

        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$areaPage}\n";
        $content .= "{\n";
        $content .= "    const MCA = '{$mca}';\n\n";


        $content .= "    /**\n";
        $content .= "     * Blocks' config\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$blocks = [\n";
        foreach ($blocks as $blockName => $block) {
            $block['class'] = $block['class'] . '\\' . ucfirst($blockName);

            $blocks[$blockName]['class'] = $block['class'];
            $this->generateBlock($blockName, $block);
            $content .= $this->generatePageClassBlock($blockName, $block, '        ');
        }
        $content .= "    ];\n";

        foreach ($blocks as $blockName => $block) {
            $content .= "\n    /**\n";
            $content .= "     * @return \\" . $block['class'] . "\n";
            $content .= "     */\n";
            $content .= '    public function get' . ucfirst($blockName) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getBlockInstance('{$blockName}');\n";
            $content .= "    }\n";
        }

        $content .= "}\n";

        return $this->createClass($class, $content);
    }

    /**
     * Generate block for page class.
     *
     * @param string $blockName
     * @param array $params
     * @param string $indent
     * @return string
     */
    protected function generatePageClassBlock($blockName, array $params, $indent = '')
    {
        $content = $indent . "'{$blockName}' => [\n";
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $content .= $this->generatePageClassBlock($key, $value, $indent . '    ');
            } else {
                $escaped = str_replace('\'', '"', $value);
                $content .= $indent . "    '{$key}' => '{$escaped}',\n";
            }
        }
        $content .= $indent . "],\n";

        return $content;
    }

    /**
     * Determine parent page class.
     *
     * @param string $area
     * @param string $mca
     * @return string
     */
    protected function getParentPage($area, $mca)
    {
        if (strpos($area, 'Adminhtml') === false) {
            if (strpos($mca, 'http') === false) {
                $areaPage = 'FrontendPage';
            } else {
                $areaPage = 'ExternalPage';
            }
        } else {
            $areaPage = 'AdminPage';
        }
        return $areaPage;
    }

    /**
     * Generate block class from XML source.
     *
     * @param string $blockName
     * @param array $block
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateBlock($blockName, array $block)
    {
        $className = ucfirst($blockName);
        $parentClass = "Magento\\AcceptanceTestFramework\\Page\\Block\\Block";
        if (isset($block['parent_class'])) {
            $parentClass = $block['parent_class'];
        }
        $parentClassName = $this->getShortClassName($parentClass);

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$this->getNamespace($block['class'])};\n\n";
        $content .= "use {$parentClass};\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$parentClassName}\n";
        $content .= "{\n";

        if (isset($block['element'])) {
            $elements = $block['element'];
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

        return $this->createClass($block['class'], $content);
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
