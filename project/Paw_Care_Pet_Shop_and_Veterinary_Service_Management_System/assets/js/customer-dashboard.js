document.addEventListener("DOMContentLoaded", function () {

    // =========================================
    // ELEMENTS
    // =========================================

    const currentTime =
        document.getElementById("currentTime");

    const menuButtons =
        document.querySelectorAll(".dashboard-menu-btn");

    const itemCards =
        document.querySelectorAll(".item-card");

    const searchInput =
        document.getElementById("dashboardSearch");


    // =========================================
    // CURRENT TIME
    // =========================================

    function updateTime() {

        const now = new Date();

        if (currentTime) {
            currentTime.innerText =
                now.toLocaleTimeString();
        }
    }

    updateTime();

    setInterval(updateTime, 1000);


    // =========================================
    // SIDEBAR CATEGORY FILTER
    // =========================================

    menuButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            // Remove active class from all buttons
            menuButtons.forEach(function (item) {
                item.classList.remove("active");
            });

            // Make clicked button active
            button.classList.add("active");


            const selectedFilter =
                button.dataset.filter;


            // Clear search box when category is clicked
            if (searchInput) {
                searchInput.value = "";
            }


            // Best Sellers will be implemented later
            if (selectedFilter === "best") {
                return;
            }


            filterItems(selectedFilter);
        });

    });


    // =========================================
    // CATEGORY FILTER FUNCTION
    // =========================================

    function filterItems(filter) {

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


            // Dashboard → Show everything
            if (filter === "all") {

                shouldShow = true;
            }

            // -------------------------
            // PET FILTERS
            // -------------------------

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

            // -------------------------
            // PRODUCT FILTERS
            // -------------------------

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


            if (shouldShow) {

                card.style.display = "flex";

            } else {

                card.style.display = "none";
            }

        });

    }


    // =========================================
    // LIVE SEARCH
    // =========================================

    if (searchInput) {

        searchInput.addEventListener("input", function () {

            const searchText =
                searchInput.value
                    .toLowerCase()
                    .trim();


            // If search is empty
            if (searchText === "") {

                filterItems("all");

                setDashboardActive();

                return;
            }


            // Remove active sidebar selection while searching
            menuButtons.forEach(function (button) {
                button.classList.remove("active");
            });


            // Search every card
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


                if (searchableText.includes(searchText)) {

                    card.style.display = "flex";

                } else {

                    card.style.display = "none";
                }

            });

        });

    }


    // =========================================
    // SET DASHBOARD ACTIVE
    // =========================================

    function setDashboardActive() {

        menuButtons.forEach(function (button) {

            button.classList.remove("active");

            if (button.dataset.filter === "all") {
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


if (cartToast) {

    // Automatically hide after 3 seconds
    setTimeout(function () {

        hideToast();

    }, 3000);


    // Manual close
    if (toastClose) {

        toastClose.addEventListener(
            "click",
            function () {

                hideToast();

            }
        );
    }
}


function hideToast() {

    if (!cartToast) {
        return;
    }

    cartToast.classList.add("toast-hide");

    setTimeout(function () {

        cartToast.remove();

    }, 300);
}

});