function updateSwiperColours(element, slug) {
  addSlug(element, '.swn-icon svg', ['has-' + slug + '-fill']);
  addSlug(element, '.swiper-pagination-bullet', [
    'has-' + slug + '-border-colour',
  ]);
  addSlug(element, '.swiper-pagination-bullet svg', [
    'has-' + slug + '-fill',
  ]);

  addSlug(element, '.fn-fraction', ['has-' + slug + '-colour']);
}