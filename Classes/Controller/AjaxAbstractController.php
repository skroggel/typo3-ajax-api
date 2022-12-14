<?php
namespace Madj2k\AjaxApi\Controller;

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

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Madj2k\CoreExtended\Utility\GeneralUtility as GeneralUtility;

/**
 * Class AjaxAbstractController
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_AjaxApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
abstract class AjaxAbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController implements AjaxControllerInterface
{

    /**
     * The default view object to use if none of the resolved views can render
     * a response for the current request.
     *
     * @var string
     * @api
     */
    protected $defaultViewObjectName = \Madj2k\AjaxApi\View\AjaxView::class;

    /**
     * @var \Madj2k\AjaxApi\Domain\Repository\ContentRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $contentRepository;

    /**
     * @var \Madj2k\AjaxApi\Helper\AjaxHelper
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $ajaxHelper;


    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        parent::injectConfigurationManager($configurationManager);
        $this->loadSettingsFromFlexForm();
    }


    /**
     * Returns the settings of the controller
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Returns the configurationManager of the controller
     *
     * @return \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    public function getConfigurationManager(): ConfigurationManagerInterface
    {
        return $this->configurationManager;
    }

    /**
     * Returns the requestObject of the controller
     *
     * @return \TYPO3\CMS\Extbase\Mvc\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }


    /**
     * Assign ajaxHelper with all relevant values
     *
     * @param ViewInterface $view The view to be initialized
     * @api
     */
    protected function initializeView(ViewInterface $view)
    {
        // set controller
        $this->ajaxHelper->setFrontendController($this);

        /** @var \Madj2k\AjaxApi\View\AjaxView $view  */
        $view->setAjaxHelper($this->ajaxHelper);
    }



    /**
     * Loads settings from Flexform and adds them to settings array
     */
    protected function loadSettingsFromFlexForm()
    {

        // load data from ContentObjectRender or via database
        $flexFormData = '';
        if (
            (! $this->configurationManager->getContentObject())
            || (! $flexFormData = $this->configurationManager->getContentObject()->data['pi_flexform'])
        ){

            /** @var \Madj2k\AjaxApi\Domain\Model\Content $content */
            if (
                ($this->contentRepository)
                && ($content = $this->contentRepository->findByIdentifier($this->ajaxHelper->getContentUid()))
            ){
                $flexFormData = $content->getPiFlexform();
            }
        }

        // merge FlexForm settings with TypoScript settings
        if ($flexFormData) {

            /** @var FlexFormService $flexFormService */
            $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
            $flexFormSettings = $flexFormService->convertFlexFormContentToArray($flexFormData);
            if (key_exists('settings', $flexFormSettings)) {
                $this->settings = GeneralUtility::arrayMergeRecursiveDistinct($this->settings, $flexFormSettings['settings']);
            }
        }
    }

}
