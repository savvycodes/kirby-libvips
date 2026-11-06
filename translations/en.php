<?php

return [
	'savvycodes.libvips.upload.notice' => 'Images: maximum 10 MiB per file.',
	'savvycodes.libvips.upload.tooLarge' => 'The image is too large. Reduce it to a maximum of 10 MiB before uploading.',
	'savvycodes.libvips.error.lock' => 'Cannot open the image conversion lock',
	'savvycodes.libvips.error.busy' => 'Image processing is busy; retry the image request shortly',
	'savvycodes.libvips.error.write' => 'Cannot write the converted image',
	'savvycodes.libvips.error.stage' => 'Cannot stage the thumbnail',
	'savvycodes.libvips.error.publish' => 'Cannot publish the thumbnail',
	'savvycodes.libvips.error.deadline' => 'Image conversion exceeded its 60-second deadline',
	'savvycodes.libvips.error.command' => 'The vips command failed (exit code {exitCode}, elapsed {elapsed}s, command budget {budget}s): {command} ({output})',
	'savvycodes.libvips.error.avifEffort' => 'AVIF encoding effort must be an integer between 0 and 9',
];
