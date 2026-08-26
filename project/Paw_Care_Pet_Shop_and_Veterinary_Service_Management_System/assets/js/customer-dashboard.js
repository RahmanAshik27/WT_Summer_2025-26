document.addEventListener("DOMContentLoaded", function () {

    
    // ELEMENTS
    

    const currentTime =
        document.getElementById("currentTime");

    const menuButtons =
        document.querySelectorAll(".dashboard-menu-btn");

    const itemCards =
        document.querySelectorAll(".item-card");
    const itemsGrid =
    document.getElementById("itemsGrid");

    const originalCardOrder =
    Array.from(itemCards);

    const searchInput =
        document.getElementById("dashboardSearch");


  
    // CURRENT TIME
  

    function updateTime() {

        if (!currentTime) {
            return;
        }

        const now = new Date();

        currentTime.innerText =
            now.toLocaleTimeString();
    }

    updateTime();

    setInterval(updateTime, 1000);


    // =========================================
    // REMOVE BEST SELLER BADGES
    // =========================================

    function removeBestSellerBadges() {

        document
            .querySelectorAll(".best-seller-badge")
            .forEach(function (badge) {

                badge.remove();

            });
    }

    function removeBestSellerCategoryTitles() {

    document
        .querySelectorAll(".best-category-title")
        .forEach(function (title) {

            title.remove();

        });

}

function restoreNormalCardOrder() {

    if (!itemsGrid) {
        return;
    }

    originalCardOrder.forEach(function (card) {

        itemsGrid.appendChild(card);

    });

}


    // =========================================
    // CATEGORY FILTER
    // =========================================

    function filterItems(filter) {

    removeBestSellerBadges();

    removeBestSellerCategoryTitles();

    restoreNormalCardOrder();

        itemCards.forEach(function (card) {

            const itemType =
                (card.dataset.type || "")
                    .toLowerCase()
                    .trim();

            const category =
                (card.dataset.category || "")
                    .toLowerCase()
                    .trim();


            let shouldShow = false;


            // Dashboard
            if (filter === "all") {

                shouldShow = true;

            }

            // Pets
            else if (
                filter === "cat" &&
                itemType === "pet" &&
                category === "cat"
            ) {

                shouldShow = true;

            }

            else if (
                filter === "dog" &&
                itemType === "pet" &&
                category === "dog"
            ) {

                shouldShow = true;

            }

            else if (
                filter === "rabbit" &&
                itemType === "pet" &&
                category === "rabbit"
            ) {

                shouldShow = true;

            }

            else if (
                filter === "bird" &&
                itemType === "pet" &&
                category === "bird"
            ) {

                shouldShow = true;

            }

            // Products
            else if (
                filter === "food" &&
                itemType === "product" &&
                category === "pet food"
            ) {

                shouldShow = true;

            }

            else if (
                filter === "accessories" &&
                itemType === "product" &&
                (
                    category === "accessories" ||
                    category === "pet accessories"
                )
            ) {

                shouldShow = true;

            }

            else if (
                filter === "medicine" &&
                itemType === "product" &&
                (
                    category === "medicine" ||
                    category === "pet medicine"
                )
            ) {

                shouldShow = true;

            }


            card.style.display =
                shouldShow ? "flex" : "none";

        });
    }


    function showBestSellers() {

    // -----------------------------------------
    // Clean old Best Seller UI
    // -----------------------------------------

    removeBestSellerBadges();

    removeBestSellerCategoryTitles();


    // -----------------------------------------
    // Hide all normal cards first
    // -----------------------------------------

    itemCards.forEach(function (card) {

        card.style.display = "none";

    });


    // -----------------------------------------
    // Safety Check
    // -----------------------------------------

    if (
        typeof bestSellerItems === "undefined" ||
        !Array.isArray(bestSellerItems) ||
        bestSellerItems.length === 0
    ) {

        console.log("No best seller data available.");

        return;
    }


    // -----------------------------------------
    // Create category groups
    // -----------------------------------------

    const categoryGroups = {};


    bestSellerItems.forEach(function (bestItem) {

        const bestType =
            String(bestItem.item_type);

        const bestId =
            String(bestItem.item_id);


        itemCards.forEach(function (card) {

            const cardType =
                String(card.dataset.type || "");

            const cardId =
                String(card.dataset.id || "");


            if (
                cardType === bestType &&
                cardId === bestId
            ) {

                const category =
                    (card.dataset.category || "Other")
                        .trim();


                // Category group না থাকলে create
                if (!categoryGroups[category]) {

                    categoryGroups[category] = [];

                }


                categoryGroups[category].push({

                    card: card,

                    totalSold:
                        parseInt(
                            bestItem.total_sold,
                            10
                        ) || 0

                });

            }

        });

    });


    // -----------------------------------------
    // Sort each category by sales
    // -----------------------------------------

    Object.keys(categoryGroups)
        .forEach(function (category) {

            categoryGroups[category].sort(
                function (a, b) {

                    return (
                        b.totalSold -
                        a.totalSold
                    );

                }
            );

        });


    // -----------------------------------------
    // Display Category-wise Best Sellers
    // -----------------------------------------

    Object.keys(categoryGroups)
        .forEach(function (category) {

            // Category heading
            const categoryTitle =
                document.createElement("div");

            categoryTitle.className =
                "best-category-title";

            categoryTitle.innerText =
                getCategoryIcon(category) +
                " " +
                category.toUpperCase();


            itemsGrid.appendChild(
                categoryTitle
            );


            // Cards under this category
            categoryGroups[category]
                .forEach(function (item) {

                    const card =
                        item.card;


                    card.style.display =
                        "flex";


                    // Sold badge
                    let badge =
                        card.querySelector(
                            ".best-seller-badge"
                        );


                    if (!badge) {

                        badge =
                            document.createElement(
                                "div"
                            );

                        badge.className =
                            "best-seller-badge";

                        card.prepend(badge);

                    }


                    badge.innerText =
                        "🔥 Sold: " +
                        item.totalSold;


                    // Move card below category heading
                    itemsGrid.appendChild(card);

                });

        });

}

function getCategoryIcon(category) {

    const categoryName =
        category.toLowerCase();


    if (categoryName === "cat") {
        return "🐱";
    }

    if (categoryName === "dog") {
        return "🐶";
    }

    if (categoryName === "rabbit") {
        return "🐰";
    }

    if (categoryName === "bird") {
        return "🐦";
    }

    if (categoryName === "fish") {
        return "🐟";
    }

    if (categoryName === "pet food") {
        return "🍖";
    }

    if (
        categoryName === "accessories" ||
        categoryName === "pet accessories"
    ) {
        return "🎾";
    }

    if (
        categoryName === "medicine" ||
        categoryName === "pet medicine"
    ) {
        return "💊";
    }

    if (categoryName === "toys") {
        return "🧸";
    }

    if (categoryName === "grooming") {
        return "🧴";
    }


    return "🐾";
}


    // =========================================
    // SIDEBAR BUTTON EVENTS
    // =========================================

    menuButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                // Clear active state
                menuButtons.forEach(function (item) {

                    item.classList.remove("active");

                });


                // Selected button active
                button.classList.add("active");


                const selectedFilter =
                    button.dataset.filter;


                // Clear search
                if (searchInput) {

                    searchInput.value = "";

                }


                if (selectedFilter === "best") {

                    showBestSellers();

                } else {

                    filterItems(selectedFilter);

                }

            }
        );

    });


    // =========================================
    // LIVE SEARCH
    // =========================================

    if (searchInput) {

        searchInput.addEventListener(
            "input",
            function () {

                removeBestSellerBadges();

                removeBestSellerCategoryTitles();

                restoreNormalCardOrder();


                const searchText =
                    searchInput.value
                        .toLowerCase()
                        .trim();


                // Empty search → Dashboard
                if (searchText === "") {

                    filterItems("all");

                    setDashboardActive();

                    return;
                }


                // Remove sidebar active state
                menuButtons.forEach(function (button) {

                    button.classList.remove("active");

                });


                itemCards.forEach(function (card) {

                    const itemName =
                        (card.dataset.name || "")
                            .toLowerCase();

                    const category =
                        (card.dataset.category || "")
                            .toLowerCase();

                    const itemType =
                        (card.dataset.type || "")
                            .toLowerCase();

                    const extra =
                        (card.dataset.extra || "")
                            .toLowerCase();


                    const searchableText =
                        itemName + " " +
                        category + " " +
                        itemType + " " +
                        extra;


                    card.style.display =
                        searchableText.includes(searchText)
                            ? "flex"
                            : "none";

                });

            }
        );

    }


    // =========================================
    // DASHBOARD ACTIVE
    // =========================================

    function setDashboardActive() {

        menuButtons.forEach(function (button) {

            button.classList.remove("active");


            if (
                button.dataset.filter === "all"
            ) {

                button.classList.add("active");

            }

        });
    }


    // =========================================
    // CART TOAST
    // =========================================

    const cartToast =
        document.getElementById("cartToast");

    const toastClose =
        document.getElementById("toastClose");


    function hideToast() {

        if (!cartToast) {
            return;
        }

        cartToast.classList.add("toast-hide");


        setTimeout(function () {

            cartToast.remove();

        }, 300);
    }


    if (cartToast) {

        setTimeout(function () {

            hideToast();

        }, 3000);


        if (toastClose) {

            toastClose.addEventListener(
                "click",
                function () {

                    hideToast();

                }
            );

        }

    }

});