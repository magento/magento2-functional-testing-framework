<?php
namespace Magento\AcceptanceTestFramework\Page;

use Magento\AcceptanceTestFramework\Page\Block\BlockFactory;
use Magento\AcceptanceTestFramework\Page\Block\BlockInterface;
use Magento\AcceptanceTestFramework\AcceptanceTester;

/**
 * Classes which implement this interface are expected to store all blocks of application page
 * and provide public getter methods to provide access to blocks.
 */
abstract class Page implements PageInterface
{
    /**
     * Page mca url.
     */
    const MCA = '';

    /**
     * Current page url.
     *
     * @var string
     */
    protected $url;

    /**
     * Block Factory instance.
     *
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Page blocks definitions array.
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * Page blocks instances.
     *
     * @var BlockInterface[]
     */
    protected $blockInstances = [];

    /**
     * Codeception actor.
     *
     * @var AcceptanceTester
     */
    protected $acceptanceTester;

    /**
     * Page load timeout in seconds.
     *
     * @var string
     */
    protected $pageLoadTimeout;

    /**
     * Set block factory, codeception actor and call initUrl method.
     *
     * @constructor
     * @param BlockFactory $blockFactory
     * @param AcceptanceTester $acceptanceTester
     */
    public function __construct(
        BlockFactory $blockFactory,
        AcceptanceTester $acceptanceTester
    ) {
        $this->blockFactory = $blockFactory;
        $this->acceptanceTester = $acceptanceTester;
        $this->pageLoadTimeout = $this->acceptanceTester->getConfiguration('pageload_timeout');
        $this->initUrl();
    }

    /**
     * Init page and set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        //
    }

    /**
     * Open page using browser.
     *
     * @param array $params
     * @return $this
     */
    public function open(array $params = [])
    {
        $url = $this->url;

        foreach ($params as $paramName => $paramValue) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= $paramName . '=' . $paramValue;
        }

        $this->acceptanceTester->amOnPage($url);

        return $this;
    }

    /**
     * Retrieve an instance of block.
     *
     * @param string $blockName
     * @return BlockInterface
     * @throws \InvalidArgumentException
     */
    public function getBlockInstance($blockName)
    {
        if (!isset($this->blockInstances[$blockName])) {
            $blockMeta = isset($this->blocks[$blockName]) ? $this->blocks[$blockName] : [];
            $class = isset($blockMeta['class']) ? $blockMeta['class'] : false;
            if ($class) {
                $element = $this->acceptanceTester->findElement($blockMeta['locator'])[0];
                $block = $this->blockFactory->create(
                    $class,
                    [
                        'element' => $element,
                        $this->acceptanceTester
                    ]
                );
            } else {
                throw new \InvalidArgumentException(
                    sprintf('There is no such block "%s" declared for the page "%s" ', $blockName, $class)
                );
            }

            $this->blockInstances[$blockName] = $block;
        }

        return $this->blockInstances[$blockName];
    }
}
