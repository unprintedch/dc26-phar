(function (wp) {
  if (!wp || !wp.blocks || !wp.domReady) {
    return;
  }

  wp.domReady(function () {
    wp.blocks.registerBlockStyle('core/template-part', {
      name: 'sticky-header',
      label: 'Sticky header',
    });
  });
})(window.wp);
