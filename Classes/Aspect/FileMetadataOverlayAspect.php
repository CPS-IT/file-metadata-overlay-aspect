<?php

declare(strict_types=1);
/*
 * This file is part of the file_metadata_overlay_aspect project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Fr\FileMetadataOverlayAspect\Aspect;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\RootLevelRestriction;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\Event\EnrichFileMetaDataEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class deals with metadata translation as an event listener which reacts on an event MetadataRepository.
 * Fixes translation problem with fallbackType set to free.
 * The listener injects user permissions and mount points into the storage
 * based on user or group configuration.
 *
 * Back ported from https://forge.typo3.org/issues/93025
 */
final class FileMetadataOverlayAspect
{
    /**
     * Do translation and workspace overlay
     *
     * @param EnrichFileMetaDataEvent $event
     */
    public function frLanguageAndWorkspaceOverlay(EnrichFileMetaDataEvent $event): void
    {
        // Should only be in Frontend, but not in eID context
        if (!($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            || !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
            || isset($_REQUEST['eID'])
        ) {
            return;
        }

        $recordData = $event->getRecord();

        $this->getPageRepository()->versionOL('sys_file_metadata', $recordData);

        $recordData = $this->getTranslatedMetadata($recordData);

        $event->setRecord($recordData);
    }

    protected function getTranslatedMetadata(array $recordData): array
    {
        $languageAspect = $this->getLanguageAspect();

        if (!$languageAspect->getContentId()) {
            return $recordData;
        }

        if ($languageAspect->doOverlays()) {
            return $this->doLanguageOverlay($recordData);
        }

        return $this->getTranslationForStrictMode($recordData, $languageAspect->getContentId());
    }

    protected function doLanguageOverlay(array $recordData): array
    {
        $overlaidMetaData = $this->getPageRepository()->getLanguageOverlay('sys_file_metadata', $recordData);
        return $overlaidMetaData ?: $recordData;
    }

    protected function getTranslationForStrictMode(array $recordData, int $contentLanguageUid): array
    {
        $parentUid = (int)$recordData['uid'];
        $translation = $this->findTranslationByFileUid($parentUid, $contentLanguageUid);
        return $translation ?: $recordData;
    }

    protected function findTranslationByFileUid(int $parentUid, int $languageUid): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file_metadata');
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(RootLevelRestriction::class));
        $record = $queryBuilder
            ->select('*')
            ->from('sys_file_metadata')->where($queryBuilder->expr()->eq(
                'l10n_parent',
                $queryBuilder->createNamedParameter($parentUid, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
            ), $queryBuilder->expr()->eq(
                'sys_language_uid',
                $queryBuilder->createNamedParameter($languageUid, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
            ), $queryBuilder->expr()->eq(
                't3ver_wsid',
                $queryBuilder->createNamedParameter(0, \TYPO3\CMS\Core\Database\Connection::PARAM_INT)
            ))->executeQuery()
            ->fetchAssociative();

        if ($record) {
            $this->getPageRepository()->versionOL('sys_file_metadata', $record);
        }

        return $record ?: null;
    }

    protected function getLanguageAspect(): LanguageAspect
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getAspect('language');
    }

    protected function getPageRepository(): PageRepository
    {
        return GeneralUtility::makeInstance(PageRepository::class);
    }
}
