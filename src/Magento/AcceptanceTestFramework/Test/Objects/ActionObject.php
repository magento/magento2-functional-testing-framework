<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

class ActionObject
{
    private $mergeKey;
    private $type;
    private $actionAttributes = [];
    private $linkedAction;
    private $orderOffset = 0;

    public function __construct($mergeKey, $type, $actionAttributes, $linkedAction = null, $order = 0)
    {
        $this->mergeKey = $mergeKey;
        $this->type = $type;
        $this->actionAttributes = $actionAttributes;
        $this->linkedAction = $linkedAction;

        if ($order == 'after') {
            $this->orderOffset = 1;
        }
    }

    public function getMergeKey()
    {
        return $this->mergeKey;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * This function returns an array of action attributes mapped by key. For example
     * the tag <seeNumberOfElements selector="value1" expected="value2" mergeKey=""/> has 3 attributes,
     * only 2 of which are specific to the 'seeNumberOfElements' tag. As a result this function would
     * return the array would return [selector => value1, expected => value2]
     * @return array
     */
    public function getCustomActionAttributes()
    {
        return $this->actionAttributes;
    }

    public function getLinkedAction()
    {
        return $this->linkedAction;
    }

    public function getOrderOffset()
    {
        return $this->orderOffset;
    }
}
