document.addEventListener('DOMContentLoaded', function () {
    const cartList = document.getElementById('cart-list');
    const totalPriceElement = document.getElementById('total-price');
    const checkoutButton = document.getElementById('checkout');

    let totalPrice = 0;
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            const productId = addToCartBtn.dataset.id; 
            const productTitle = document.getElementById('modal-title').innerText;
            const productPrice = parseFloat(document.getElementById('modal-price').innerText.replace('$', '').trim());

            console.log({
                id: productId,
                title: productTitle,
                price: productPrice
            });

            if (!productId || !productTitle || isNaN(productPrice)) {
                alert('Invalid product details. Cannot add to cart.');
                return;
            }
            fetch("http://localhost/DetyraProjektuse/backend/getProducts.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: productId,
                    title: productTitle,
                    price: productPrice
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart successfully!');
                    fetchCartItems(); 
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add product to cart. Please try again.');
            });
        });
    }

    function fetchCartItems() {
        fetch("http://localhost/DetyraProjektuse/backend/getCarItems.php")
            .then(response => response.json())
            .then(data => {
                console.log('Cart data:', data); 
                if (data.success && Array.isArray(data.items)) {
                   cartList.innerHTML = '';
                    totalPrice = 0;
                    data.items.forEach(item => {
                        const itemPrice = parseFloat(item.price);
                        if (!isNaN(itemPrice)) {
                            totalPrice += itemPrice;
                            const listItem = document.createElement('li');
                            listItem.innerHTML = `
                                <span class="item-name">${item.title}</span>
                                <span class="item-price">$${itemPrice.toFixed(2)}</span>
                            `;
                            cartList.appendChild(listItem);
                        } else {
                            console.error('Invalid item price:', item.price);
                        }
                    });

                    totalPriceElement.innerHTML = `$${totalPrice.toFixed(2)}`;
                } else {
                    throw new Error('Failed to load cart items or data format is incorrect.');
                }
            })     
    }

    fetchCartItems();
});
