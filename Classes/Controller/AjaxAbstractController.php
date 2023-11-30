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

use Madj2k\AjaxApi\Domain\Repository\ContentRepository;
use Madj2k\AjaxApi\Helper\AjaxHelper;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Madj2k\CoreExtended\Utility\GeneralUtility as GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
     * @var \Madj2k\AjaxApi\Domain\Repository\ContentRepository|null
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?ContentRepository $contentRepository = null;


    /**
     * @var \Madj2k\AjaxApi\Helper\AjaxHelper|null
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?AjaxHelper $ajaxHelper = null;


    /**
     * @param \Madj2k\AjaxApi\Domain\Repository\ContentRepository $contentRepository
     * @return void
     */
    public function injectContentRepository(ContentRepository $contentRepository): void
    {
        $this->contentRepository = $contentRepository;
    }


    /**
     * @param \Madj2k\AjaxApi\Helper\AjaxHelper $ajaxHelper
     * @return void
     */
    public function injectAjaxHelper(AjaxHelper $ajaxHelper): void
    {
        $this->ajaxHelper = $ajaxHelper;
    }


    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
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
     * @return void
     * @api
     */
    protected function initializeView(ViewInterface $view): void
    {
        // set controller
        $this->ajaxHelper->setFrontendController($this);

        /** @var \Madj2k\AjaxApi\View\AjaxView $view  */
        $view->setAjaxHelper($this->ajaxHelper);
    }


    /**
     * Loads settings from Flexform and adds them to settings array
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    protected function loadSettingsFromFlexForm(): void
    {

        // load data from ContentObjectRender or via database
        $flexFormData = '';
        if (
            // @extensionScannerIgnoreLine
            (! $this->configurationManager->getContentObject())
            // @extensionScannerIgnoreLine
            || (! $flexFormData = $this->configurationManager->getContentObject()->data['pi_flexform'])
        ){

            /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

            /** @var \Madj2k\AjaxApi\Domain\Repository\ContentRepository $contentRepository */
            $contentRepository = $objectManager->get(ContentRepository::class);

            /** @var \Madj2k\AjaxApi\Helper\AjaxHelper $ajaxHelper */
            $ajaxHelper = $objectManager->get(AjaxHelper::class);

            /** @var \Madj2k\AjaxApi\Domain\Model\Content $content */
            if ($content = $contentRepository->findByIdentifier($ajaxHelper->getContentUid())){
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
