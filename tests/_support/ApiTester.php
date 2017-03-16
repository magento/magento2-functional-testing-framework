<?php

//use \Codeception\Util\HttpCode;

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
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * @var string
     */
    protected $superAdminUsername = 'admin';
    /**
     * @var string
     */
    protected $superAdminPassword = '123123q';
    /**
     * @var string
     */
    protected $superAdminToken;

    /**
     * @param \Codeception\Scenario $scenario
     * @inheritdoc
     */
    public function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST('integration/admin/token', ['username' => $this->superAdminUsername, 'password' => $this->superAdminPassword]);
        $this->superAdminToken = $this->grabDataFromResponseByJsonPath('$..*');
        $this->superAdminToken = substr($this->grabResponse(), 1, strlen($this->grabResponse())-2);
        $this->seeResponseCodeIs(200);
    }

    /**
     * Get admin auth token
     *
     * @return string
     */
    public function getAuthToke()
    {
        return $this->superAdminToken;
    }
}
