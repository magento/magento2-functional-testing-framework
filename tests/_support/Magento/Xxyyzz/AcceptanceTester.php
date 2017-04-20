<?php
namespace Magento\Xxyyzz;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */
    public static $loadingMask     = '.loading-mask';
    public static $gridLoadingMask = '.admin__data-grid-loading-mask';

    public function waitForLoadingMaskToDisappear()
    {
        $I = $this;
        $I->waitForElementNotVisible(self::$loadingMask, 30);
        $I->waitForElementNotVisible(self::$gridLoadingMask, 30);
    }

    public function scrollToTopOfPage()
    {
        $I = $this;
        $I->executeJS('window.scrollTo(0,0);');
    }
}
