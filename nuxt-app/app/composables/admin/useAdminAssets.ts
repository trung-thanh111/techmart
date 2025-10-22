interface LoadCSSOptions {
  href: string;
}
interface LoadJSOptions {
  src: string;
  async?: boolean;
}
type LoadJSResolve = () => void;
export const useAdminAssets = () => {
  const loadCSS = (href: string): void => {
    if (!document.querySelector(`link[href="${href}"]`)) {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = href;
      document.head.appendChild(link);
    }
  };
  const loadJS = (src: string): Promise<void> => {
    return new Promise<void>((resolve: LoadJSResolve) => {
      if (!document.querySelector(`script[src="${src}"]`)) {
        const script = document.createElement("script");
        script.src = src;
        script.async = false;
        script.onload = () => resolve();
        document.body.appendChild(script);
      } else {
        resolve();
      }
    });
  };
  const loadAdminAssets = async (): Promise<void> => {
    // const cssFiles: string[] = ["/admin/libs/jsvectormap/css/jsvectormap.min.css", "/admin/libs/swiper/swiper-bundle.min.css", "/admin/css/bootstrap.min.css", "/admin/css/icons.min.css", "/admin/css/app.min.css", "/admin/css/custom.min.css"];
    // const jsFiles: string[] = ["/admin/libs/bootstrap/js/bootstrap.bundle.min.js", "/admin/libs/simplebar/simplebar.min.js", "/admin/libs/node-waves/waves.min.js", "/admin/libs/feather-icons/feather.min.js", "/admin/js/pages/plugins/lord-icon-2.1.0.js", "/admin/libs/apexcharts/apexcharts.min.js", "/admin/libs/jsvectormap/js/jsvectormap.min.js", "/admin/libs/jsvectormap/maps/world-merc.js", "/admin/libs/swiper/swiper-bundle.min.js", "/admin/js/app.js"];
    // cssFiles.forEach(loadCSS);
    // for (const src of jsFiles) {
    //   await loadJS(src);
    // }
    // await loadJS("/admin/js/pages/dashboard-ecommerce.init.js");
  };
  return {
    loadAdminAssets,
    loadCSS,
    loadJS,
  };
};
