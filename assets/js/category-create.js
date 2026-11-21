(function () {
    const categoryName = document.getElementById('category_name');
    const slug = document.getElementById('slug');
    let manuallyEdited = slug.value.trim() !== '' && slug.value !== slugifyCategoryName(categoryName.value);

    function slugifyCategoryName(value) {
      return value.trim().toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }

    categoryName.addEventListener('input', function () {
      if (!manuallyEdited) {
        slug.value = slugifyCategoryName(categoryName.value);
      }
    });
    slug.addEventListener('input', function () {
      manuallyEdited = slug.value.trim() !== '' && slug.value !== slugifyCategoryName(categoryName.value);
    });
  })();
