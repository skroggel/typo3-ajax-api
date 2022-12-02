<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        //=================================================================
        // Add TypoScript
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'Ajax API',
            'constants',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:ajax_api/Configuration/TypoScript/constants.typoscript">'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'Ajax API',
            'setup',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:ajax_api/Configuration/TypoScript/setup.typoscript">'
        );

        //=================================================================
        // Configure Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Madj2k']['AjaxApi']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::DEBUG => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => 'typo3temp/var/logs/tx_ajaxapi.log'
                )
            ),
        );

    },
    $_EXTKEY
);


