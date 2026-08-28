const catalogTabs = document.querySelectorAll(".catalog-tab");
const catalogCards = document.querySelectorAll(".catalog-card");
const inventoryType = document.getElementById("inventoryType");
const categorySelect = document.getElementById("categorySelect");
const itemSelect = document.getElementById("itemSelect");
const itemPrice = document.getElementById("itemPrice");
const stockQuantity = document.getElementById("stockQuantity");

let selectedCard = null;

function showCatalog(type) {
    document.getElementById("petCatalog").classList.toggle("hidden", type !== "pet");
    document.getElementById("productCatalog").classList.toggle("hidden", type !== "product");

    catalogTabs.forEach(tab => tab.classList.toggle("active", tab.dataset.type === type));

    inventoryType.value = type;
    loadCategories(type);
}

function loadCategories(type) {
    const categories = new Set();

    document.querySelectorAll(`.catalog-card[data-type="${type}"]`).forEach(card => {
        if (card.dataset.category) categories.add(card.dataset.category);
    });

    categorySelect.innerHTML = '<option value="">Select Category</option>';

    categories.forEach(category => {
        categorySelect.innerHTML += `<option value="${category}">${category}</option>`;
    });

    itemSelect.innerHTML = '<option value="">Select Item</option>';
}

function loadItems(type, category) {
    itemSelect.innerHTML = '<option value="">Select Item</option>';

    document.querySelectorAll(`.catalog-card[data-type="${type}"]`).forEach(card => {
        if (!category || card.dataset.category === category) {
            itemSelect.innerHTML += `<option value="${card.dataset.id}">${card.dataset.name}</option>`;
        }
    });
}

function selectCard(card) {
    catalogCards.forEach(item => item.classList.remove("selected"));
    card.classList.add("selected");
    selectedCard = card;

    const type = card.dataset.type;
    const image = card.querySelector("img").src;

    showCatalog(type);

    categorySelect.value = card.dataset.category;
    loadItems(type, card.dataset.category);
    itemSelect.value = card.dataset.id;
    itemPrice.value = card.dataset.price;
    stockQuantity.value = 1;

    document.getElementById("selectedName").textContent = card.dataset.name;
    document.getElementById("selectedStock").textContent = `Database Stock: ${card.dataset.stock} Units`;
    const stock = Number(card.dataset.stock);
    let stockStatus = "In Stock";

    if (stock === 0)
        { 
            stockStatus = "Out of Stock";

        }
    else if (stock <= 5)
        {
            stockStatus = "Low Stock";
        } 
    document.getElementById("selectedCategory").textContent = `Category: ${card.dataset.category}`;
    document.getElementById("selectedPrice").textContent = `Price: ৳${Number(card.dataset.price).toFixed(2)}`;

    document.getElementById("previewImage").innerHTML = `<img src="${image}" alt="${card.dataset.name}">`;

    document.getElementById("removeInventoryType").value = type;
    document.getElementById("removeItemId").value = card.dataset.id;
    document.getElementById("removeQuantity").value = 1;

    document.getElementById("monitorContent").innerHTML = `
        <table class="monitor-table">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Category</th>
                <th>Item</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>${card.dataset.id}</td>
                <td>${type === "pet" ? "Pet" : "Product"}</td>
                <td>${card.dataset.category}</td>
                <td>${card.dataset.name}</td>
                <td>৳${Number(card.dataset.price).toFixed(2)}</td>
                <td>${stock}</td>
                <td><span class="stock-status ${stock === 0 ? "out-stock" : stock <= 5 ? "low-stock" : "in-stock"}">${stockStatus}</span></td>
            </tr>
        </table>
    `;
}

catalogTabs.forEach(tab => {
    tab.addEventListener("click", () => showCatalog(tab.dataset.type));
});

catalogCards.forEach(card => {
    card.addEventListener("click", () => selectCard(card));
});

inventoryType.addEventListener("change", () => {
    selectedCard = null;
    showCatalog(inventoryType.value);
});

categorySelect.addEventListener("change", () => {
    loadItems(inventoryType.value, categorySelect.value);
});

itemSelect.addEventListener("change", () => {
    const card = document.querySelector(`.catalog-card[data-type="${inventoryType.value}"][data-id="${itemSelect.value}"]`);
    if (card) selectCard(card);
});

showCatalog("pet");