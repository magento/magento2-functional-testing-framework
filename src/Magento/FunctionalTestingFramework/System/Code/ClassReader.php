<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\System\Code;

/**
 * Class ClassReader
 *
 * @internal
 */
class ClassReader
{
    /**
     * Read class method signature
     *
     * @param string $className
     * @param string $method
     * @return array|null
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getParameters($className, $method)
    {
        $class = new \ReflectionClass($className);
        $result = null;
        $method = $class->getMethod($method);
        if ($method) {
            $result = [];
            /** @var $parameter \ReflectionParameter */
            foreach ($method->getParameters() as $parameter) {
                try {
                    $paramType = $parameter->getType();
                    $name = ($paramType && method_exists($paramType, 'isBuiltin') && !$paramType->isBuiltin())
                        ? new \ReflectionClass($paramType->getName())
                        : null;
                    $result[$parameter->getName()] = [
                        $parameter->getName(),
                        $name !== null ? $name->getName() : null,
                        !$parameter->isOptional(),
                        $parameter->isOptional() ?
                            $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null :
                            null
                    ];
                } catch (\ReflectionException $e) {
                    $message = $e->getMessage();
                    throw new \ReflectionException($message, 0, $e);
                }
            }
        }

        return $result;
    }
}
