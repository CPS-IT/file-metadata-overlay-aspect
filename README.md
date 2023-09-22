# Core Fix
==============================================================

Extend FileMetadataOverlayAspect to fix core translation problem when fallbackType: free

See: https://forge.typo3.org/issues/93025


## Iinstalation

1. Add package repository to composer

```json
{
  "repositories": {
		"file-metadata-overlay-aspect": {
			"type": "vcs",
			"url": "git@github.com:CPS-IT/file-metadata-overlay-aspect.git"
		}
	}
}
```

2. Require the package with composer `composer req fr/file-metadata-overlay-aspect`


