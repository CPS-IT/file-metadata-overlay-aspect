<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'File Metadata Overlay Aspect',
    'description' => 'Extend FileMetadataOverlayAspect to fix core  translation problem  forge.typo3.org issue 93025',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Fr\\FileMetadataOverlayAspect\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'author' => 'Vladimir Falcon Piva',
    'author_email' => 'v.falcon@familie-redlich.de',
    'author_company' => 'familie redlich',
    'version' => '2.0.1',
];
