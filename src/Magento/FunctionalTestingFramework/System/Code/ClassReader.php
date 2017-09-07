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
                    $result[$parameter->getName()] = [
                        $parameter->getName(),
                        ($parameter->getClass() !== null) ? $parameter->getClass()->getName() : null,
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
