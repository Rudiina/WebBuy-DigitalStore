//HAP DRITAREN E RE
function openProductDetails(productId) {
    const modal = document.getElementById('product-modal');
    const product = products[productId];

    if (product) {
        document.getElementById('modal-image').src = product.image;
        document.getElementById('modal-title').textContent = product.title;
        document.getElementById('modal-description').textContent = product.description;
        document.getElementById('modal-price').textContent = product.price;
        modal.style.display = 'block';
    }
}

function closeProductDetails() {
    const modal = document.getElementById('product-modal');
    modal.style.display = 'none';
}

window.onclick = function (event) {
    const modal = document.getElementById('product-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
// search bari
document.querySelector('.search-bar button').addEventListener('click', function () {
    const searchInput = document.querySelector('.search-bar input').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        const title = product.querySelector('h3').textContent.toLowerCase();

        
        if (title.includes(searchInput)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
});
document.querySelector('.search-bar input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        document.querySelector('.search-bar button').click();
    }
});

