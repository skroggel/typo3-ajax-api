<?php
namespace Madj2k\AjaxApi\Helper;
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Madj2k\AjaxApi\Controller\AjaxControllerInterface;

/**
 * Class AjaxHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_AjaxApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
class AjaxHelper extends AjaxHelperAbstract
{


    /**
     * @var \Madj2k\AjaxApi\Controller\AjaxControllerInterface|null
     */
    protected ?AjaxControllerInterface $frontendController = null;


    /**
     * Gets the key
     *
     * @return string
     */
    public function getKey (): string
    {
        /** Leads to strange behaviour since TYPO3 v10 - so we generate the key new every time now
            if (! $this->key) {
                $this->calculateKey();
            }
            return $this->key;
        */

        $this->calculateKey();
        return $this->key;
    }


    /**
     * Calculates the key
     *
     * @return void
     */
    protected function calculateKey (): void
    {

        $plaintextKey = $this->getContentUid();
        if (
            ($this->frontendController)
            && ($this->frontendController->getRequest())
        ) {
            $plaintextKey = $this->getContentUid() . '_' .
                $this->frontendController->getRequest()->getControllerExtensionName() .  '_' .
                $this->frontendController->getRequest()->getPluginName() .  '_' .
                $this->frontendController->getRequest()->getControllerName() . '_' .
                $this->frontendController->getRequest()->getControllerActionName() . '_';
               // serialize($this->getFrontendController()->getSettings());
        }

        $this->key = sha1($plaintextKey);

        $this->getLogger()->log(
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG,
            sprintf(
                'Calculated key "%s", plaintext: "%s".',
                $this->key,
                $plaintextKey
            )
        );
    }


    /**
     * Gets the frontend controller
     *
     * @return \Madj2k\AjaxApi\Controller\AjaxControllerInterface
     */
    public function getFrontendController (): ?AjaxControllerInterface
    {
        return $this->frontendController;
    }


    /**
     * Sets the frontend controller
     *
     * @param \Madj2k\AjaxApi\Controller\AjaxControllerInterface $frontendController
     * @return void
     */
    public function setFrontendController (AjaxControllerInterface $frontendController): void
    {

        $this->frontendController = $frontendController;
        if (
            ($this->frontendController->getConfigurationManager())
            // @extensionScannerIgnoreLine
            && ($contentObject = $this->frontendController->getConfigurationManager()->getContentObject())
            && ($contentUid = $contentObject->data['uid'])
        ) {
            $this->setContentUid($contentUid);
        }
    }
}
