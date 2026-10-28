// ================================
// 1. ТАУАРЛАР ТІЗІМІ
// ================================

const products = [
    {
        id: 1,
        name: "Сымсыз құлаққап",
        category: "Электроника",
        price: 8990,
        image: "https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 2,
        name: "Смарт сағат",
        category: "Электроника",
        price: 18900,
        image: "https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 3,
        name: "Қалаға арналған сөмке",
        category: "Аксессуарлар",
        price: 12500,
        image: "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 4,
        name: "Күлгін құлаққап",
        category: "Электроника",
        price: 24990,
        image: "https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 5,
        name: "Футболка",
        category: "Киім",
        price: 6990,
        image: "https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 6,
        name: "Кроссовка",
        category: "Киім",
        price: 29900,
        image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 7,
        name: "Үстел шамы",
        category: "Үйге арналған",
        price: 6400,
        image: "https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=500&q=80"
    },
    {
        id: 8,
        name: "Су құтысы",
        category: "Аксессуарлар",
        price: 3200,
        image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=500&q=80"
    }
];


// ================================
// 2. БРАУЗЕРДЕГІ САҚТАУ
// ================================

function readStorage(key, fallback) {
    try {
        const value = localStorage.getItem(key);
        return value === null ? fallback : JSON.parse(value);
    } catch {
        return fallback;
    }
}

let cart = readStorage("minishop_cart", []);
let users = readStorage("minishop_users", []);
let currentUser = readStorage("minishop_current_user", null);

function saveCart() {
    localStorage.setItem("minishop_cart", JSON.stringify(cart));
}

function saveUsers() {
    localStorage.setItem("minishop_users", JSON.stringify(users));
}

function saveCurrentUser() {
    localStorage.setItem(
        "minishop_current_user",
        JSON.stringify(currentUser)
    );
}

function money(price) {
    return price.toLocaleString("ru-RU") + " ₸";
}


// ================================
// 3. ХАБАРЛАМА ШЫҒАРУ
// ================================

let toastTimer;

function showToast(message) {
    const toast = document.getElementById("toast");

    toast.textContent = message;
    toast.classList.add("show");

    clearTimeout(toastTimer);

    toastTimer = setTimeout(() => {
        toast.classList.remove("show");
    }, 2500);
}


// ================================
// 4. БЕТТЕР АРАСЫНДА АУЫСУ
// ================================

function showPage(pageName) {
    const page = document.getElementById(pageName);

    if (!page) return;

    document.querySelectorAll(".page").forEach(section => {
        section.classList.remove("active");
    });

    page.classList.add("active");

    document.querySelectorAll("[data-page]").forEach(link => {
        link.classList.toggle(
            "active",
            link.dataset.page === pageName
        );
    });

    document.getElementById("nav").classList.remove("open");

    if (pageName === "catalog") {
        renderProducts();
    }

    if (pageName === "cart") {
        renderCart();
    }

    if (pageName === "profile") {
        renderProfile();
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
}

document.querySelectorAll("[data-page]").forEach(element => {
    element.addEventListener("click", event => {
        event.preventDefault();
        showPage(element.dataset.page);
    });
});


// ================================
// 5. МОБИЛЬДІ МӘЗІР
// ================================

document.getElementById("menuBtn").addEventListener("click", () => {
    document.getElementById("nav").classList.toggle("open");
});


// ================================
// 6. ТАУАР КАРТОЧКАСЫ
// ================================

function productCard(product) {
    return `
        <article class="product-card">
            <img
                class="product-image"
                src="${product.image}"
                alt="${product.name}"
                loading="lazy"
                onerror="this.onerror=null;this.src='https://placehold.co/500x400/eee7ff/7656d8?text=MiniShop'"
            >

            <div class="product-info">
                <p class="product-category">
                    ${product.category}
                </p>

                <h3>${product.name}</h3>

                <div class="product-bottom">
                    <span class="price">
                        ${money(product.price)}
                    </span>

                    <button
                        class="btn"
                        data-add="${product.id}"
                    >
                        Себетке +
                    </button>
                </div>
            </div>
        </article>
    `;
}


// ================================
// 7. БАСТЫ БЕТТЕГІ ТАУАРЛАР
// ================================

function renderFeatured() {
    const area = document.getElementById("featuredProducts");

    area.innerHTML = products
        .slice(0, 4)
        .map(productCard)
        .join("");
}


// ================================
// 8. КАТАЛОГ, ІЗДЕУ ЖӘНЕ СҮЗГІ
// ================================

function renderProducts() {
    const search = document
        .getElementById("searchInput")
        .value
        .trim()
        .toLocaleLowerCase("kk");

    const category = document.getElementById("categoryFilter").value;

    const filtered = products.filter(product => {
        const matchesName = product.name
            .toLocaleLowerCase("kk")
            .includes(search);

        const matchesCategory =
            category === "all" || product.category === category;

        return matchesName && matchesCategory;
    });

    const area = document.getElementById("allProducts");

    if (filtered.length === 0) {
        area.innerHTML = `
            <div class="empty-cart">
                <h3>Тауар табылмады 😔</h3>
                <p>Іздеу сөзін немесе санатты өзгертіп көріңіз.</p>
            </div>
        `;
    } else {
        area.innerHTML = filtered.map(productCard).join("");
    }

    document.getElementById("productCount").textContent =
        "Тауар саны: " + filtered.length;
}

document.getElementById("searchInput")
    .addEventListener("input", renderProducts);

document.getElementById("categoryFilter")
    .addEventListener("change", renderProducts);


// ================================
// 9. СЕБЕТКЕ ТАУАР ҚОСУ
// ================================

function addToCart(id) {
    const product = products.find(item => item.id === id);

    if (!product) return;

    const item = cart.find(item => item.id === id);

    if (item) {
        item.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            category: product.category,
            quantity: 1
        });
    }

    saveCart();
    updateCartCount();

    showToast(product.name + " себетке қосылды!");
}

// Тауар карточкаларындағы батырмаларды өңдеу
document.addEventListener("click", event => {
    const button = event.target.closest("[data-add]");

    if (button) {
        addToCart(Number(button.dataset.add));
    }
});


// ================================
// 10. СЕБЕТТЕГІ ТАУАР САНЫ
// ================================

function updateCartCount() {
    const count = cart.reduce(
        (total, item) => total + item.quantity,
        0
    );

    document.getElementById("cartCount").textContent = count;
}


// ================================
// 11. СЕБЕТТІ КӨРСЕТУ
// ================================

function renderCart() {
    const area = document.getElementById("cartItems");
    const summary = document.getElementById("cartSummary");

    if (cart.length === 0) {
        area.innerHTML = `
            <div class="empty-cart">
                <h2>🛒 Себетіңіз бос</h2>
                <p>Ұнаған тауарларыңызды себетке қосыңыз.</p>
                <button class="btn" data-page="catalog">
                    Тауар таңдау
                </button>
            </div>
        `;

        summary.innerHTML = "";
        return;
    }

    area.innerHTML = cart.map(item => `
        <article class="cart-item">
            <img src="${item.image}" alt="${item.name}"
                 onerror="this.onerror=null;this.src='https://placehold.co/200x200/eee7ff/7656d8?text=Shop'">

            <div class="cart-item-info">
                <h3>${item.name}</h3>

                <p class="muted">${money(item.price)}</p>

                <div class="quantity">
                    <button data-minus="${item.id}">−</button>

                    <b>${item.quantity}</b>

                    <button data-plus="${item.id}">+</button>
                </div>
            </div>

            <div>
                <b>${money(item.price * item.quantity)}</b>
                <br><br>

                <button class="remove-btn" data-remove="${item.id}">
                    Өшіру
                </button>
            </div>
        </article>
    `).join("");

    const total = cart.reduce(
        (sum, item) => sum + item.price * item.quantity,
        0
    );

    summary.innerHTML = `
        <h2>Жалпы сома: ${money(total)}</h2>
        <p class="muted">Тауар саны: ${cart.reduce(
            (sum, item) => sum + item.quantity, 0
        )}</p>

        <button class="btn" id="checkoutBtn">
            Тапсырыс беру →
        </button>

        <button class="btn secondary" data-page="catalog">
            Сауданы жалғастыру
        </button>
    `;
}


// ================================
// 12. СЕБЕТТЕГІ САНДЫ ӨЗГЕРТУ
// ================================

document.addEventListener("click", event => {
    const plus = event.target.closest("[data-plus]");
    const minus = event.target.closest("[data-minus]");
    const remove = event.target.closest("[data-remove]");

    if (plus) {
        const item = cart.find(
            item => item.id === Number(plus.dataset.plus)
        );

        if (item) item.quantity++;
    }

    if (minus) {
        const item = cart.find(
            item => item.id === Number(minus.dataset.minus)
        );

        if (item) {
            item.quantity--;

            if (item.quantity <= 0) {
                cart = cart.filter(product => product.id !== item.id);
            }
        }
    }

    if (remove) {
        const id = Number(remove.dataset.remove);
        cart = cart.filter(item => item.id !== id);

        showToast("Тауар себеттен өшірілді.");
    }

    if (plus || minus || remove) {
        saveCart();
        updateCartCount();
        renderCart();
    }
});


// ================================
// 13. АККАУНТТАРДЫ БАСҚАРУ
// ================================

// Демо-нұсқада құпиясөз браузердегі Web Crypto арқылы хэштеледі.
// Бұл серверлік қауіпсіз аутентификацияның орнына жүрмейді.

async function hashPassword(password) {
    if (!crypto.subtle) {
        throw new Error(
            "Қауіпсіз құпиясөз өңдеу үшін сайтты localhost арқылы ашыңыз."
        );
    }

    const data = new TextEncoder().encode(password);
    const hash = await crypto.subtle.digest("SHA-256", data);

    return Array.from(new Uint8Array(hash))
        .map(byte => byte.toString(16).padStart(2, "0"))
        .join("");
}


// ================================
// 14. ТІРКЕЛУ
// ================================

document.getElementById("registerForm")
    .addEventListener("submit", async event => {
        event.preventDefault();

        const name = document.getElementById("regName").value.trim();
        const email = document.getElementById("regEmail")
            .value.trim().toLowerCase();

        const password = document.getElementById("regPassword").value;
        const password2 = document.getElementById("regPassword2").value;

        const message = document.getElementById("registerMessage");

        if (password !== password2) {
            message.textContent = "Құпиясөздер бірдей емес!";
            return;
        }

        if (users.some(user => user.email === email)) {
            message.textContent = "Бұл электрондық пошта тіркелген.";
            return;
        }

        try {
            const passwordHash = await hashPassword(password);

            users.push({
                name: name,
                email: email,
                passwordHash: passwordHash
            });

            saveUsers();

            currentUser = { name, email };
            saveCurrentUser();
            updateNavigation();

            event.target.reset();

            showToast("Тіркелу сәтті аяқталды!");

            showPage("profile");
        } catch (error) {
            message.textContent = error.message;
        }
    });


// ================================
// 15. ЖҮЙЕГЕ КІРУ
// ================================

document.getElementById("loginForm")
    .addEventListener("submit", async event => {
        event.preventDefault();

        const email = document.getElementById("loginEmail")
            .value.trim().toLowerCase();

        const password = document.getElementById("loginPassword").value;

        const message = document.getElementById("loginMessage");

        const user = users.find(user => user.email === email);

        if (!user) {
            message.textContent = "Мұндай аккаунт табылмады. Алдымен тіркеліңіз.";
            return;
        }

        try {
            const passwordHash = await hashPassword(password);

            if (passwordHash !== user.passwordHash) {
                message.textContent = "Құпиясөз қате!";
                return;
            }

            currentUser = {
                name: user.name,
                email: user.email
            };

            saveCurrentUser();
            updateNavigation();

            event.target.reset();

            showToast("Жүйеге сәтті кірдіңіз!");

            showPage("profile");
        } catch (error) {
            message.textContent = error.message;
        }
    });


// ================================
// 16. ПРОФИЛЬ
// ================================

function renderProfile() {
    if (!currentUser) {
        showPage("login");
        showToast("Алдымен жүйеге кіріңіз.");
        return;
    }

    document.getElementById("profileName").textContent =
        currentUser.name;

    document.getElementById("profileEmail").textContent =
        currentUser.email;
}


// ================================
// 17. МӘЗІРДІ ЖАҢАРТУ
// ================================

function updateNavigation() {
    const loggedIn = Boolean(currentUser);

    document.getElementById("loginLink").hidden = loggedIn;
    document.getElementById("registerLink").hidden = loggedIn;
    document.getElementById("profileLink").hidden = !loggedIn;
    document.getElementById("logoutBtn").hidden = !loggedIn;
}

document.getElementById("logoutBtn").addEventListener("click", () => {
    currentUser = null;

    localStorage.removeItem("minishop_current_user");

    updateNavigation();

    showToast("Аккаунттан шықтыңыз.");

    showPage("home");
});


// ================================
// 18. ТАПСЫРЫС БЕРУ
// ================================

document.addEventListener("click", event => {
    if (event.target.id !== "checkoutBtn") return;

    if (!currentUser) {
        showToast("Тапсырыс беру үшін алдымен жүйеге кіріңіз.");
        showPage("login");
        return;
    }

    if (cart.length === 0) {
        showToast("Себетіңіз бос.");
        return;
    }

    showToast(
        "Интерфейс дайын! Нақты тапсырысты сақтау үшін PHP/MySQL қажет."
    );
});


// ================================
// 19. САЙТТЫ ІСКЕ ҚОСУ
// ================================

renderFeatured();
renderProducts();
updateCartCount();
updateNavigation();