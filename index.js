(function () {
  const maxBytes = 10 * 1024 * 1024;

  async function validateImage(file) {
    const imageExtension = /\.(avif|bmp|gif|heic|heif|jpe?g|png|svg|tiff?|webp)$/i;
    if (!file.type.startsWith("image/") && !imageExtension.test(file.name)) return;
    if (file.size > maxBytes) {
      throw new Error("De afbeelding is te groot. Verklein deze tot maximaal 10 MiB voordat je uploadt.");
    }
  }

  // Kirby 4 funnels picker, drag/drop and replacements through this method.
  panel.plugin("savvycodes/kirby-libvips", {
    created: [function (app) {
      const uploader = app.$panel.upload;
      const upload = uploader.upload;
      uploader.upload = async function (file, attributes) {
        try {
          await validateImage(file.src);
        } catch (error) {
          file.error = error.message;
          file.progress = 0;
          return;
        }
        return upload.call(this, file, attributes);
      };
    }]
  });
})();
