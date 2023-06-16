<?php
defined('TYPO3_MODE') || die('Access denied.');



call_user_func(
    function($extKey)
    {
        //=================================================================
        // Add TypoScript automatically
        //=================================================================
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'AJAX API',
            'constants',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:ajax_api/Configuration/TypoScript/constants.typoscript">'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'AJAX API',
            'setup',
            '<INCLUDE_TYPOSCRIPT: source="FILE: EXT:ajax_api/Configuration/TypoScript/setup.typoscript">'
        );

        //=================================================================
        // Configure Logger
        //=================================================================
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['Madj2k']['AjaxApi']['writerConfiguration'] = array(

            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::WARNING => array(
                // add a FileWriter
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
                    // configuration for the writer
                    'logFile' => \TYPO3\CMS\Core\Core\Environment::getVarPath() .'/log/tx_ajaxapi.log'
                )
            ),
        );

    },
    $_EXTKEY
);


