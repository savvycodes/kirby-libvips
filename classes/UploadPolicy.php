<?php

namespace Bitbetter\Libvips;

use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\F;
use Kirby\Toolkit\I18n;

final class UploadPolicy
{
	public const MAX_BYTES = 10 * 1024 * 1024;

	// MIME and byte-size validation only; no image decoding or resolution cap.
	public static function validate(string $path): void
	{
		$mime = F::mime($path);
		if (str_starts_with($mime ?? '', 'image/') === false) {
			return;
		}
		if (filesize($path) > self::MAX_BYTES) {
			throw new InvalidArgumentException(I18n::translate('savvycodes.libvips.upload.tooLarge'));
		}
	}
}
