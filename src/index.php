<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Woolly Wonderland - Premium Wool Crafts</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Woolly<span>Wonderland</span></a>
            <ul class="nav-links">
                <li><a href="#products">Products</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="admin.php">Admin</a></li>
                <li class="cart-icon">
                    <a href="cart.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span id="cart-count">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Handcrafted Warmth <br>for Your Soul</h1>
            <p>Discover our exclusive collection of premium wool creations, ethically sourced and lovingly made.</p>
            <a href="#products" class="btn btn-primary">Shop Collection</a>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&q=80&w=1000"
                alt="Wool Texture">
        </div>
    </header>
        <div class="container" style="margin-top: 30px; text-align: center;">
            <input type="text" id="search-input" placeholder="🔍 Tim kiem do len..." 
               style="padding: 12px 25px; width: 60%; border-radius: 30px; border: 2px solid #e0b0ff; outline: none; font-size: 16px; box-shadow: var(--shadow);">
        </div>
    <main class="container">
        <section id="products" class="products-section">
            <div class="section-header">
                <h2>Our Featured Products</h2>
                <div class="divider"></div>
            </div>
            <div id="product-list" class="product-grid">
                <!-- Products will be loaded here via Ajax -->
                <div class="loader">Loading...</div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Woolly Wonderland. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            // 1. Hàm hiển thị sản phẩm ra màn hình
            function renderProducts(products) {
                let html = '';
                if (products.length > 0) {
                    products.forEach(product => {
                        html += `
                            <div class="product-card">
                                <img src="${product.image}" alt="${product.name}">
                                <div class="product-info">
                                    <h3>${product.name}</h3>
                                    <p>${product.description}</p>
                                    <div class="product-footer">
                                        <span class="price">$${product.price}</span>
                                        <button class="btn btn-sm" onclick="addToCart(${product.id})">Add to Cart</button>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    html = '<p style="grid-column: 1/-1; text-align: center;">Không tìm thấy sản phẩm nào.</p>';
                }
                $('#product-list').html(html);
            }

            // 2. Tự động tải sản phẩm khi vừa mở trang
            $.get('api.php?action=get_products', function(data) {
                renderProducts(data);
            });

            // 3. Xử lý khi người dùng gõ vào ô tìm kiếm
            $('#search-input').on('keyup', function() {
                let keyword = $(this).val();
                $.get('api.php?action=search_products&keyword=' + keyword, function(data) {
                    renderProducts(data);
                });
            });
        });

        // 4. Hàm thêm vào giỏ hàng 
        function addToCart(id) {
            $.get('api.php?action=add_to_cart&id=' + id, function(data) {
                if(data.success) {
                    $('#cart-count').text(data.cart_count);
                    alert('Đã thêm vào giỏ hàng thành công!');
                }
            }, 'json');
        }
    </script>
</body>

</html>