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
        $this->start = microtime(true);
        foreach ($this->configData->get('page') as $name => $data) {
            $this->generateClass($name, $data);
        }
        $this->end = microtime(true);
        $time = $this->end - $this->start;

        \Magento\AcceptanceTestFramework\Generate\GenerateResult::addResult('Page Classes', $this->cnt, $time);
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
        $blocks = isset($data['block']) ? $data['block'] : [];
        foreach ($blocks as $blockClass => $block) {
            $blockName = lcfirst($this->getShortClassName($blockClass));
            $block['class'] = $blockClass;
            $content .= $this->generatePageClassBlock($blockName, $block, '        ');
        }
        $content .= "    ];\n";

        foreach ($blocks as $blockClass => $block) {
            $blockName = lcfirst($this->getShortClassName($blockClass));
            $content .= "\n    /**\n";
            $content .= "     * @return \\" . $blockClass . "\n";
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
}
