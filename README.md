# Kirby libvips

SavvyCodes' MIT-licensed fork of [bitbetterde/kirby-libvips](https://github.com/bitbetterde/kirby-libvips), originally by Moritz Stueckler and Florian Karsten.

This fork generates Kirby thumbnails using the **libvips CLI only**, with bounded conversion concurrency/runtime, atomic publication and image upload safeguards. It includes fixes for Kirby 4 crop coordinates, EXIF orientation/dimensions and `vipsthumbnail`'s `--export-profile` option. The PHP namespace remains `Bitbetter\Libvips` for compatibility.

## Requirements

- Kirby 4 (validated with 4.9.6) and PHP >= 8.1. Recheck the Panel integration before upgrading Kirby.
- libvips >= 8.15, including `vips` and `vipsthumbnail` with the codecs you need.
- GNU `timeout` on `PATH` and PHP's `exec()` enabled. FFI/php-vips is not used.

On Debian: `apt install libvips-tools coreutils`. On macOS: `brew install vips coreutils` and add Homebrew coreutils' `libexec/gnubin` directory to `PATH` so `timeout` is available.

## Installation

Remove the upstream `bitbetter/kirby-libvips` package before installing this fork; both install to `site/plugins/kirby-libvips` and must not coexist.

```sh
composer config repositories.savvycodes-libvips vcs https://github.com/savvycodes/kirby-libvips
composer require savvycodes/kirby-libvips:1.0.5
```

Alternatively, copy this repository to `site/plugins/kirby-libvips` (no Composer autoloader required).

Enable the driver in `site/config/config.php`:

```php
return [
    'thumbs' => [
        'driver' => 'vips',
        'quality' => 80,
        'threads' => 1,
    ],
];
```

`vipsthumbnail` remains a legacy driver alias. The plugin ID is `savvycodes/kirby-libvips`. A separate application-local wrapper is not needed.

## Thumbnail behavior

Supports resizing, center/positional/focal cropping, optional attention/entropy smartcrop, EXIF autorotation, grayscale, blur and sharpen. Output support depends on installed libvips codecs; JPG, PNG, WebP and AVIF are validated on Debian libvips 8.16.1.

Additional `thumbs` options:

| Option | Default | Meaning |
| --- | --- | --- |
| `autoOrient` | `true` | Apply EXIF orientation before calculating display dimensions/crops |
| `bin` | `vipsthumbnail` | Thumbnail binary |
| `vipsBin` | `vips` | Binary used for crops/effects |
| `threads` | `1` | libvips processing threads per command |
| `profile` | `srgb` | Export profile; use `false` to disable |
| `interlace` | `false` | Progressive JPEG/PNG output |
| `smartcrop` | `false` | `attention` or `entropy` for plain center crops |
| `strip` | `true` | Remove thumbnail metadata |

The Kirby thumbnail component serializes generation using a filesystem lock in the PHP temporary directory (one job per container when that directory is private). Cached thumbnails bypass the lock. Waiting requests also check for newly published output, so duplicate requests can return without waiting for unrelated conversions. Lock wait is limited to **60 seconds**, measured with a monotonic clock, to accommodate bursts of uncached AVIF thumbnails. Each conversion still has a separate **60-second total command budget**, with a **5-second kill grace**. Sustained overload can still exceed the bounded wait; failed requests raise an error and can be retried. Web-server/request timeouts are not changed.

Each command disables the operation cache and sets cache-memory/file hints. These are **not a hard RSS limit**: codecs and native operations may allocate additional memory. No PHP, Apache, container or host memory settings are changed.

Thumbnails are generated in hidden staging files next to the destination and published by atomic rename only after success. Failures clean up staging/intermediate files rather than publishing an unprocessed original. Existing thumbnail URLs/cache and source originals are preserved; there is no cache reset or original migration. The plugin overrides Kirby's `thumb` component; do not combine it with another plugin that overrides the same component.

## Image upload policy

Installing this plugin also enables global `file.create:before` and `file.replace:before` hooks:

- All images (including SVGs): maximum **10 MiB** (10,485,760 bytes), with **no resolution limit**.
- Non-images: unaffected.

Server validation checks MIME and file size without decoding a bitmap or reading its dimensions. Oversized uploads are rejected with Dutch error messages, not resized; rejected replacements preserve the existing file. Existing originals are not changed.

The Kirby 4 Panel integration shows a **10 MiB per image** notice in upload and replacement dialogs and flags oversized images immediately on selection/drop. It also wraps the central upload method to reject images over 10 MiB **before network transfer**, covering picker, drag/drop and replacement uploads. It checks file size only, without browser image decoding or format-support checks. Kirby's own file validation still applies. Server hooks remain authoritative for API uploads and other clients. Interactive Panel smoke testing is recommended before merging/upgrading.

Upload limits and bounded conversion concurrency reduce image-processing risk, but do not guarantee the application cannot exhaust host memory. Apache/PHP request concurrency is a separate operational concern.

## License

MIT; see [LICENSE](LICENSE). Original author attribution is retained in `composer.json` and the license.
