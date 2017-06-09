<?php
namespace Magento\AcceptanceTestFramework\Page\Block;

/**
 * Factory for Blocks.
 */
class BlockFactory
{
    /**
     * @param string $class
     * @param array $arguments
     * @return BlockInterface
     * @throws \UnexpectedValueException
     */
    public function create($class, array $arguments = [])
    {
        // normalize namespace.
        $class = ltrim($class, '\\');

        if (class_exists($class)) {
            $reflectedClass = new \ReflectionClass($class);
            $reflectedConstructor = $reflectedClass->getConstructor();
            if (is_null($reflectedConstructor)) {
                $object = new $class;
            } else {
                $object = $reflectedClass->newInstanceArgs($arguments);
            }
            if (!$object instanceof BlockInterface) {
                $interfaceClass = '\Magento\AcceptanceTestFramework\Page\Block\BlockInterface';
                throw new \UnexpectedValueException(
                    sprintf('Block class "%s" has to implement '. $interfaceClass . 'interface.', $class)
                );
            }
        } else {
            throw new \UnexpectedValueException(
                sprintf('Class "%s" does not exist.', $class)
            );
        }
        return $object;
    }
}
