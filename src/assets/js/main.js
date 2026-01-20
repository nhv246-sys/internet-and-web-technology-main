$(document).ready(function () {
    loadProducts();

    function loadProducts() {
        $.ajax({
            url: 'api.php?action=get_products',
            type: 'GET',
            dataType: 'json',
            success: function (products) {
                let html = '';
                if (products.length === 0) {
                    html = '<div class="no-products">No products found. Stay tuned!</div>';
                } else {
                    products.forEach(product => {
                        html += `
                            <div class="product-card" data-id="${product.id}">
                                <div class="product-img">
                                    <img src="${product.image || 'https://via.placeholder.com/300'}" alt="${product.name}">
                                </div>
                                <div class="product-info">
                                    <h3>${product.name}</h3>
                                    <p class="product-price">$${parseFloat(product.price).toFixed(2)}</p>
                                    <button class="btn btn-add-cart" data-id="${product.id}">Add to Cart</button>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#product-list').html(html);
            },
            error: function () {
                $('#product-list').html('<p>Error loading products. Please try again later.</p>');
            }
        });
    }

    // Cart logic (simplified for demo)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    updateCartCount();

    $(document).on('click', '.btn-add-cart', function () {
        const id = $(this).data('id');
        cart.push(id);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();

        // Simple micro-animation
        $(this).text('Added!').addClass('success');
        setTimeout(() => {
            $(this).text('Add to Cart').removeClass('success');
        }, 1500);
    });

    function updateCartCount() {
        $('#cart-count').text(cart.length);
    }
});
