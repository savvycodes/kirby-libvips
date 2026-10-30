(function () {
  const maxBytes = 8 * 1024 * 1024;
  const maxDimension = 4096;

  async function validateImage(file) {
    const imageExtension = /\.(avif|bmp|gif|heic|heif|jpe?g|png|svg|tiff?|webp)$/i;
    if (!file.type.startsWith("image/") && !imageExtension.test(file.name)) return;
    if (file.size > maxBytes) {
      throw new Error("De afbeelding is te groot. Verklein deze tot maximaal 8 MiB voordat je uploadt.");
    }
    if (file.type === "image/svg+xml" || /\.svg$/i.test(file.name)) return;

    const url = URL.createObjectURL(file);
    try {
      const image = new Image();
      await new Promise((resolve, reject) => {
        image.onload = resolve;
        image.onerror = () => reject(new Error("De afbeelding kan niet worden gelezen. Exporteer deze als JPG, PNG, WebP of AVIF."));
        image.src = url;
      });
      if (image.naturalWidth > maxDimension || image.naturalHeight > maxDimension) {
        throw new Error("De afbeelding is te groot. Verklein deze tot maximaal 4096 × 4096 pixels voordat je uploadt.");
      }
    } finally {
      URL.revokeObjectURL(url);
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
