function showCategory(category) {
    const allProducts = document.querySelectorAll('.product-card');
    allProducts.forEach(product => {
      product.style.display = 'block';
    });
    const selectedCategoryProducts = document.querySelectorAll(`.product-card:not(.${category})`);
    selectedCategoryProducts.forEach(product => {
      product.style.display = 'none';
    });
  }
