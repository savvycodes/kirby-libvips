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
	'savvycodes.libvips.error.command' => 'The vips command could not be executed: {command} ({output})',
];
