<?php

namespace Bitbetter\Libvips;

use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\F;

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
			throw new InvalidArgumentException('De afbeelding is te groot. Verklein deze tot maximaal 10 MiB voordat je uploadt.');
		}
	}
}
