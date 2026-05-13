document.addEventListener("DOMContentLoaded", function () {
    const header = document.getElementById("header");
    const cartCount = document.getElementById("cart-count");
    const authLinks = document.getElementById("authLinks");
    const cartItems = document.getElementById("cart-items");

    function startsGood(value) {
        return /^[\p{Script=Arabic}a-zA-Z0-9]/u.test(value.trim());
    }

    function clean(text) {
        const div = document.createElement("div");
        div.textContent = text;
        return div.innerHTML;
    }

    function getCart() {
        return JSON.parse(localStorage.getItem("cart")) || [];
    }

    function saveCart(cart) {
        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();
    }

    function updateCartCount() {
        if (!cartCount) return;

        const cart = getCart();
        let total = 0;

        cart.forEach(function (item) {
            total += Number(item.quantity) || 0;
        });

        cartCount.textContent = total;
    }

    function priceByQuantity(quantity) {
        const prices = { 1: 250, 2: 480, 3: 700, 4: 900, 5: 1050, 6: 1200 };
        return prices[quantity] || quantity * 200;
    }

    function addToCart(name, image) {
        const cart = getCart();
        const found = cart.find(function (item) {
            return item.name === name;
        });

        if (found) {
            found.quantity++;
        } else {
            cart.push({ name: name, image: image, price: 250, quantity: 1 });
        }

        saveCart(cart);
        alert("تمت إضافة المنتج إلى السلة");
    }

    function renderCart() {
        if (!cartItems) return;

        const cart = getCart();
        const emptyCart = document.getElementById("empty-cart");
        const cartContent = document.getElementById("cart-content");

        if (cart.length === 0) {
            emptyCart.style.display = "flex";
            cartContent.style.display = "none";
            updateCartCount();
            return;
        }

        emptyCart.style.display = "none";
        cartContent.style.display = "grid";

        let html = "";
        let subtotal = 0;
        let total = 0;

        cart.forEach(function (item, index) {
            const quantity = Number(item.quantity) || 1;
            const itemSubtotal = 250 * quantity;
            const itemTotal = priceByQuantity(quantity);

            subtotal += itemSubtotal;
            total += itemTotal;

            html += `
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="${clean(item.image)}" alt="${clean(item.name)}">
                    </div>

                    <div class="cart-item-info">
                        <h3>${clean(item.name)}</h3>
                        <p class="price">${itemTotal} ر.س</p>
                    </div>

                    <div class="cart-item-quantity">
                        <button class="qty-btn" data-index="${index}" data-change="-1">-</button>
                        <span class="qty">${quantity}</span>
                        <button class="qty-btn" data-index="${index}" data-change="1">+</button>
                    </div>

                    <button class="remove-btn" data-index="${index}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
        });

        cartItems.innerHTML = html;

        document.getElementById("total-items").textContent = cart.reduce(function (sum, item) {
            return sum + (Number(item.quantity) || 0);
        }, 0);

        document.getElementById("subtotal").textContent = subtotal + " ر.س";
        document.getElementById("total").textContent = total + " ر.س";

        const discount = subtotal - total;
        const discountRow = document.getElementById("discount-row");
        document.getElementById("total-discount").textContent = "- " + discount + " ر.س";
        discountRow.style.display = discount > 0 ? "flex" : "none";

        const progress = Math.min((total / 200) * 100, 100);
        document.getElementById("progress-fill").style.width = progress + "%";
        document.getElementById("shipping-status").textContent = total >= 200 ? "التوصيل مجاني!" : "يتبقى " + (200 - total) + " ر.س";

        updateCartCount();
    }

    if (header) {
        window.addEventListener("scroll", function () {
            header.classList.toggle("header-small", window.scrollY > 50);
        });
    }

    if (authLinks) {
        fetch("auth_status.php")
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (result.logged_in) {
                    authLinks.innerHTML = `<a href="Account.php"><i class="fa-solid fa-user"></i> ${clean(result.name)}</a><a href="Logout.php">Logout</a>`;
                }
            })
            .catch(function () { });
    }

    document.addEventListener("submit", function (e) {
        const fields = e.target.querySelectorAll("input:not([type='password']):not([type='hidden']), textarea");

        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];

            if (field.value.trim() !== "" && !startsGood(field.value)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                alert("لا يمكن أن تبدأ البيانات برمز");
                field.focus();
                return;
            }
        }
    }, true);

    document.querySelectorAll(".item-buy").forEach(function (button) {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const card = button.closest(".item-card");
            addToCart(card.querySelector("h3").textContent.trim(), card.querySelector("img").getAttribute("src"));
        });
    });

    const modal = document.getElementById("itemModal");
    const modalBuy = document.querySelector(".modal-buy-btn");

    document.querySelectorAll(".item-details").forEach(function (button) {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            document.getElementById("modalImage").src = button.dataset.image;
            document.getElementById("modalName").textContent = button.dataset.name;
            document.getElementById("modalPrice").textContent = button.dataset.price;
            document.getElementById("modalDescription").textContent = button.dataset.description;
            document.getElementById("modalDetails").textContent = button.dataset.details;

            modalBuy.dataset.name = button.dataset.name;
            modalBuy.dataset.image = button.dataset.image;
            modal.classList.add("show");
        });
    });

    if (modalBuy) {
        modalBuy.addEventListener("click", function (e) {
            e.preventDefault();
            addToCart(modalBuy.dataset.name, modalBuy.dataset.image);
        });
    }

    const closeModal = document.getElementById("closeModal");
    if (closeModal) closeModal.onclick = function () { modal.classList.remove("show"); };
    if (modal) modal.onclick = function (e) { if (e.target === modal) modal.classList.remove("show"); };

    document.querySelectorAll(".rec-card").forEach(function (card) {
        card.addEventListener("click", function () {
            addToCart(card.dataset.name, card.dataset.image);
            renderCart();
        });
    });

    if (cartItems) {
        renderCart();

        cartItems.addEventListener("click", function (e) {
            const qtyBtn = e.target.closest(".qty-btn");
            const removeBtn = e.target.closest(".remove-btn");
            const cart = getCart();

            if (qtyBtn) {
                const index = Number(qtyBtn.dataset.index);
                const change = Number(qtyBtn.dataset.change);
                cart[index].quantity += change;
                if (cart[index].quantity < 1) cart[index].quantity = 1;
                saveCart(cart);
                renderCart();
            }

            if (removeBtn) {
                cart.splice(Number(removeBtn.dataset.index), 1);
                saveCart(cart);
                renderCart();
            }
        });
    }

    const clearCartBtn = document.getElementById("clearCartBtn");
    if (clearCartBtn) {
        clearCartBtn.onclick = function () {
            if (confirm("هل تريد تفريغ السلة؟")) {
                saveCart([]);
                renderCart();
            }
        };
    }

    const checkoutModal = document.getElementById("checkoutModal");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const closeCheckoutBtn = document.getElementById("closeCheckoutBtn");

    if (checkoutBtn) {
        checkoutBtn.onclick = function () {
            if (getCart().length === 0) return;

            fetch("auth_status.php")
                .then(function (response) { return response.json(); })
                .then(function (result) {
                    if (result.logged_in) {
                        checkoutModal.style.display = "flex";
                    } else {
                        alert("لازم تسجل الدخول قبل إتمام الطلب");
                        location.href = "Account.php#login";
                    }
                });
        };
    }

    if (closeCheckoutBtn) {
        closeCheckoutBtn.onclick = function () {
            checkoutModal.style.display = "none";
        };
    }

    const checkoutForm = document.getElementById("checkoutForm");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const cart = getCart();
            const items = cart.map(function (item) {
                const total = priceByQuantity(item.quantity);
                return {
                    name: item.name,
                    quantity: item.quantity,
                    price: Math.round(total / item.quantity),
                    total: total
                };
            });

            fetch("submit_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    name: document.getElementById("customerName").value,
                    phone: document.getElementById("customerPhone").value,
                    city: document.getElementById("customerCity").value,
                    address: document.getElementById("customerAddress").value,
                    notes: document.getElementById("customerNotes").value,
                    items: items
                })
            })
                .then(function (response) { return response.json(); })
                .then(function (result) {
                    alert(result.success ? "تم إرسال الطلب بنجاح، رقم الطلب: " + result.order_id : result.message);

                    if (result.success) {
                        saveCart([]);
                        renderCart();
                        checkoutForm.reset();
                        checkoutModal.style.display = "none";
                    }
                });
        });
    }

    const contactForm = document.querySelector(".contact-form");
    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            e.preventDefault();
            alert("تم إرسال الرسالة بنجاح");
            contactForm.reset();
        });
    }

    updateCartCount();
});
