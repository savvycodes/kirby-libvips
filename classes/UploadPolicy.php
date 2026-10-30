<?php

namespace Bitbetter\Libvips;

use Kirby\Exception\InvalidArgumentException;
use Kirby\Filesystem\F;

final class UploadPolicy
{
	public const MAX_BYTES = 8 * 1024 * 1024;
	public const MAX_DIMENSION = 4096;

	// Header-only validation: never decode a full bitmap just to reject it.
	public static function validate(string $path): void
	{
		$mime = F::mime($path);
		if (str_starts_with($mime ?? '', 'image/') === false) {
			return;
		}
		if (filesize($path) > self::MAX_BYTES) {
			throw new InvalidArgumentException('De afbeelding is te groot. Verklein deze tot maximaal 8 MiB voordat je uploadt.');
		}
		if ($mime === 'image/svg+xml') {
			return;
		}
		$size = @getimagesize($path);
		if ($size === false) {
			throw new InvalidArgumentException('De afmetingen van de afbeelding kunnen niet worden gelezen. Exporteer deze als JPG, PNG, WebP of AVIF.');
		}
		if ($size[0] > self::MAX_DIMENSION || $size[1] > self::MAX_DIMENSION) {
			throw new InvalidArgumentException('De afbeelding is te groot. Verklein deze tot maximaal 4096 × 4096 pixels voordat je uploadt.');
		}
	}
}
