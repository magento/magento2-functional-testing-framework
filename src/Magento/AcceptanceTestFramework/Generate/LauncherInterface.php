<?php
namespace Magento\AcceptanceTestFramework\Generate;

/**
 * Interface for launch generators
 */
interface LauncherInterface
{
    /**
     * Launch generation
     *
     * @return mixed
     */
    public function launch();
}
