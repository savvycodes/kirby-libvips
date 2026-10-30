(function () {
  const maxBytes = 10 * 1024 * 1024;

  function validateImage(file) {
    const imageExtension = /\.(avif|bmp|gif|heic|heif|jpe?g|png|svg|tiff?|webp)$/i;
    if (!file.type.startsWith("image/") && !imageExtension.test(file.name)) return;
    if (file.size > maxBytes) {
      throw new Error(window.panel.$t("savvycodes.libvips.upload.tooLarge"));
    }
  }

  // Kirby 4 funnels picker, drag/drop and replacements through this method.
  panel.plugin("savvycodes/kirby-libvips", {
    use: [function (Vue) {
      // Render functions also work with Kirby's runtime-only Vue build.
      for (const name of ["k-upload-dialog", "k-upload-replace-dialog"]) {
        const Original = Vue.component(name);
        Vue.component(name, Original.extend({
          render(h) {
            const dialog = Original.options.render.call(this, h);
            const content = dialog.componentOptions || dialog;
            content.children = [
              h("p", { class: "k-libvips-upload-notice", attrs: { role: "note" } },
                this.$t("savvycodes.libvips.upload.notice")),
              ...(content.children || [])
            ];
            return dialog;
          }
        }));
      }
    }],
    created: function (app) {
      const uploader = app.$panel.upload;
      const createFile = uploader.file;
      uploader.file = function (source) {
        const file = createFile.call(this, source);
        try {
          validateImage(source);
        } catch (error) {
          file.error = error.message;
        }
        return file;
      };
      const upload = uploader.upload;
      uploader.upload = async function (file, attributes) {
        try {
          validateImage(file.src);
        } catch (error) {
          file.error = error.message;
          file.progress = 0;
          return;
        }
        return upload.call(this, file, attributes);
      };
    }
  });
})();
