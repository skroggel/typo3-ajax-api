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
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '9.5.1',
    'constraints' => [
		'depends' => [
			'typo3' => '8.7.0-8.7.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];
