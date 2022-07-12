<?php

/**
 * Extension Manager/Repository config file for ext "file_metadata_overlay_aspect".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'File Metadata Overlay Aspect',
    'description' => 'Extend FileMetadataOverlayAspect to fix core  translation problem  forge.typo3.org issue 93025',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'fluid_styled_content' => '10.4.0-10.4.99',
            'rte_ckeditor' => '10.4.0-10.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Fr\\FileMetadataOverlayAspect\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Vladimir Falcon Piva',
    'author_email' => 'v.falcon@familie-redlich.de',
    'author_company' => 'familie redlich',
    'version' => '1.0.2',
];
