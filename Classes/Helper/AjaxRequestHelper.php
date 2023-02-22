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

use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class AjaxRequestHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel
 * @package Madj2k_AjaxApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
class AjaxRequestHelper extends AjaxHelperAbstract
{

    /**
     * @var array
     */
    protected array $idList = [];


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initFromGetPost();
    }


    /**
     * Gets the idList
     *
     * @return array
     */
    public function getIdList (): array
    {
        return $this->idList;
    }


    /**
     * Sets the idList
     *
     * @param array $idList
     * @return void
     */
    public function setIdList (array $idList): void
    {
        $this->idList = $idList;
    }


    /**
     * Init values based on GET-/POST-Params or on given params and settings
     *
     * @return void
     */
    public function initFromGetPost (): void
    {
        if (GeneralUtility::_GP('ajax_api')) {
            $values = GeneralUtility::_GP('ajax_api');

            if ($contentUid = $values['cid']) {
                $this->setContentUid($contentUid);
            }
            if ($key = $values['key']) {
                $this->setKey($key);
            }
            if ($idList = GeneralUtility::trimExplode(',', $values['idl'])) {
                $this->setIdList($idList);
            }
        }
    }
}
