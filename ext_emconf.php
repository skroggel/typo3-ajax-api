<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'Ajax API',
	'description' => 'Extension with basic AJAX functions for usage with TYPO3',
	'category' => 'fe',
	'author' => 'Steffen Kroggel',
	'author_email' => 'developer@steffenkroggel.de',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '9.5.4',
    'constraints' => [
		'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'core_extended' => '9.5.4-9.5.99',
        ],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];
