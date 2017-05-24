<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Code;

/**
 * Class Generator.
 *
 * Classes generator.
 */
class Generator
{
    /**#@+
     * Generation statuses.
     */
    const GENERATION_SUCCESS = 'success';
    const GENERATION_ERROR = 'error';
    const GENERATION_SKIP = 'skip';
    /**#@- */

    /**
     * @var string[]
     */
    protected $generatedEntities;

    /**
     * @param array $generatedEntities
     */
    public function __construct(array $generatedEntities = [])
    {
        $this->generatedEntities = $generatedEntities;
    }

    /**
     * Get generated entities.
     *
     * @return string[]
     */
    public function getGeneratedEntities()
    {
        return $this->generatedEntities;
    }

    /**
     * Generate class.
     *
     * @param string $className
     * @return string
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function generateClass($className)
    {
        $classNameRegexp = '/\\\Test\\\([^\\\]+)(?:\\\[^\\\]+)+$/';

        if (preg_match($classNameRegexp, $className, $matches)) {
            $classType = lcfirst($matches[1]);

            if (!isset($this->generatedEntities[$classType])) {
                throw new \InvalidArgumentException(
                    sprintf('Cannot generate class "%s". Unknown type %s', $className, $classType)
                );
            }

            /** @var \Magento\TestFramework\Util\Generate\AbstractGenerate $generator */
            $generator = $this->createGeneratorInstance($this->generatedEntities[$classType]);

            $classFilePath = $generator->generate($className);

            if (!$classFilePath) {
                throw new \Exception(implode(' ', $generator->getErrors()));
            }

            $this->includeFile($classFilePath);

            return self::GENERATION_SUCCESS;
        }

        return self::GENERATION_SKIP;
    }

    /**
     * @param string $fileName
     * @return void
     */
    public function includeFile($fileName)
    {
        include $fileName;
    }

    /**
     * Create entity generator.
     *
     * @param string $generatorClass
     * @return \Magento\TestFramework\Util\Generate\AbstractGenerate
     */
    protected function createGeneratorInstance($generatorClass)
    {
        return \Magento\TestFramework\ObjectManager::getInstance()->get($generatorClass);
    }
}
