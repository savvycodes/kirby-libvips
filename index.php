<?php

use Kirby\Cms\App;
use Kirby\Image\Darkroom;
use Bitbetter\Libvips\Vips;
use Bitbetter\Libvips\UploadPolicy;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Filesystem\Filename;

if (class_exists(Vips::class) === false) {
	require_once __DIR__ . '/classes/Vips.php';
}
require_once __DIR__ . '/classes/UploadPolicy.php';

// register the darkroom driver for `thumbs.driver => 'vips'`
// ('vipsthumbnail' is kept as a legacy alias)
Darkroom::$types['vips'] ??= Vips::class;
Darkroom::$types['vipsthumbnail'] ??= Vips::class;

App::plugin('savvycodes/kirby-libvips', [
	'hooks' => [
		'file.create:before' => function ($file, $upload) {
			UploadPolicy::validate($upload->root());
		},
		'file.replace:before' => function ($file, $upload) {
			UploadPolicy::validate($upload->root());
		},
	],
	'components' => [
		'thumb' => function (App $kirby, string $src, string $dst, array $options): string {
			$driver = Darkroom::factory($kirby->option('thumbs.driver', 'vips'), $kirby->option('thumbs', []));
			$options = $driver->preprocess($src, $options);
			$root = (new Filename($src, $dst, $options))->toString();

			return Vips::withLock(function () use ($driver, $src, $root, $options) {
				if (is_file($root)) {
					return $root;
				}
				Dir::make(dirname($root));
				$staging = dirname($root) . '/.' . F::name($root) . '-' . bin2hex(random_bytes(8)) . '.' . F::extension($root);
				try {
					if (F::copy($src, $staging) !== true) {
						throw new RuntimeException('Cannot stage the thumbnail');
					}
					$driver->process($staging, $options);
					if (rename($staging, $root) !== true) {
						throw new RuntimeException('Cannot publish the thumbnail');
					}
				} finally {
					F::remove($staging);
				}
				return $root;
			});
		},
	],
]);
