<?php


namespace Magento\FunctionalTestingFramework\Helper;


use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class HelperContainer
{
    private $helpers = [];

    public function __construct(array $helpers = [])
    {
        $this->helpers = $helpers;
    }

    public function get(string $className)
    {
        if ($this->has($className)) {
            return $this->helpers[$className];
        }
        throw new TestFrameworkException('Custom helper ' . $className . 'not found.');
    }

    public function has(string $className)
    {
        return array_key_exists($className, $this->helpers);
    }
}
