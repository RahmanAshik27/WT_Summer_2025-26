document.addEventListener("DOMContentLoaded", function ()
{
    const currentTime = document.getElementById("currentTime");
    const menuButtons = document.querySelectorAll(".dashboard-menu-btn");
    const itemCards = document.querySelectorAll(".item-card");
    const itemsGrid = document.getElementById("itemsGrid");
    const originalCardOrder = Array.from(itemCards);
    const searchInput = document.getElementById("dashboardSearch");

    // Current Time er Jonno funtion
    function updateTime()
    {
        if (!currentTime)
        {
            return;
        }
        const now = new Date();
        currentTime.innerText = now.toLocaleTimeString();
    }

    updateTime();
    setInterval(updateTime, 1000);

    // Best Seller part er jonno
    function removeBestSellerBadges()
    {
        document.querySelectorAll(".best-seller-badge").forEach(function (badge) { badge.remove(); });
    }

    function removeBestSellerCategoryTitles()
    {
        document.querySelectorAll(".best-category-title").forEach(function (title) { title.remove(); });
    }

    function restoreNormalCardOrder()
    {
        if (!itemsGrid)
        {
            return;
        }

        originalCardOrder.forEach(function (card) { itemsGrid.appendChild(card); });
    }

    // CATEGORY wise filter korra jonno
    function filterItems(filter)
    {
        removeBestSellerBadges();
        removeBestSellerCategoryTitles();
        restoreNormalCardOrder();

        itemCards.forEach(function (card)
        {
            const itemType = (card.dataset.type || "").toLowerCase().trim();
            const category = (card.dataset.category || "").toLowerCase().trim();

            let shouldShow = false;

            if (filter === "all")
            {
                shouldShow = true;
            }
            else if (filter === "cat" && itemType === "pet" && category === "cat")
            {
                shouldShow = true;
            }
            else if (filter === "dog" && itemType === "pet" && category === "dog")
            {
                shouldShow = true;
            }
            else if (filter === "rabbit" && itemType === "pet" && category === "rabbit")
            {
                shouldShow = true;
            }
            else if (filter === "bird" && itemType === "pet" && category === "bird")
            {
                shouldShow = true;
            }
            else if (filter === "food" && itemType === "product" && category === "pet food")
            {
                shouldShow = true;
            }
            else if (filter === "accessories" && itemType === "product" && (category === "accessories" || category === "pet accessories"))
            {
                shouldShow = true;
            }
            else if (filter === "medicine" && itemType === "product" && (category === "medicine" || category === "pet medicine"))
            {
                shouldShow = true;
            }

            card.style.display = shouldShow ? "flex" : "none";
        });
    }

    function showBestSellers()
    {
        removeBestSellerBadges();
        removeBestSellerCategoryTitles();

        itemCards.forEach(function (card) { card.style.display = "none"; });

        if (typeof bestSellerItems === "undefined" || !Array.isArray(bestSellerItems) || bestSellerItems.length === 0)
        {
            console.log("No best seller data available.");
            return;
        }

        // ittem gulo er catagory wise group korar jonnop
        const categoryGroups = {};

        bestSellerItems.forEach(function (bestItem)
        {
            const bestType = String(bestItem.item_type);
            const bestId = String(bestItem.item_id);

            itemCards.forEach(function (card)
            {
                const cardType = String(card.dataset.type || "");
                const cardId = String(card.dataset.id || "");

                if (cardType === bestType && cardId === bestId)
                {
                    const category = (card.dataset.category || "Other").trim();

                    if (!categoryGroups[category])
                    {
                        categoryGroups[category] = [];
                    }

                    categoryGroups[category].push({ card: card, totalSold: parseInt(bestItem.total_sold, 10) || 0 });
                }
            });
        });

        // product sob gulo seel er sapekkhe catogry korar jonno
        Object.keys(categoryGroups).forEach(function (category)
        {
            categoryGroups[category].sort(function (a, b) { return (b.totalSold - a.totalSold); });
        });

        Object.keys(categoryGroups).forEach(function (category)
        {
            const categoryTitle = document.createElement("div");
            categoryTitle.className = "best-category-title";
            categoryTitle.innerText = getCategoryIcon(category) + " " + category.toUpperCase();

            itemsGrid.appendChild(categoryTitle);

            categoryGroups[category].forEach(function (item)
            {
                const card = item.card;
                card.style.display = "flex";
                let badge = card.querySelector(".best-seller-badge");

                if (!badge)
                {
                    badge = document.createElement("div");
                    badge.className = "best-seller-badge";
                    card.prepend(badge);
                }

                badge.innerText = "🔥 Sold: " + item.totalSold;
                itemsGrid.appendChild(card);
            });
        });
    }

    function getCategoryIcon(category)
    {
        const categoryName = category.toLowerCase();

        if (categoryName === "cat")
        {
            return "🐱";
        }

        if (categoryName === "dog")
        {
            return "🐶";
        }

        if (categoryName === "rabbit")
        {
            return "🐰";
        }

        if (categoryName === "bird")
        {
            return "🐦";
        }

        if (categoryName === "fish")
        {
            return "🐟";
        }

        if (categoryName === "pet food")
        {
            return "🍖";
        }

        if (categoryName === "accessories" || categoryName === "pet accessories")
        {
            return "🎾";
        }

        if (categoryName === "medicine" || categoryName === "pet medicine")
        {
            return "💊";
        }

        if (categoryName === "toys")
        {
            return "🧸";
        }

        if (categoryName === "grooming")
        {
            return "🧴";
        }

        return "🐾";
    }

    // SIDEBAR BUTTON EVENTS
    menuButtons.forEach(function (button)
    {
        button.addEventListener("click", function ()
        {
            menuButtons.forEach(function (item)
            {
                item.classList.remove("active");
            });

            button.classList.add("active");

            const selectedFilter = button.dataset.filter;

            if (searchInput)
            {
                searchInput.value = "";
            }

            if (selectedFilter === "best")
            {
                showBestSellers();
            }
            else
            {
                filterItems(selectedFilter);
            }
        });
    });

    // LIVE SEARCH er jonno funtion 
    if (searchInput)
    {
        searchInput.addEventListener("input", function ()
        {
            removeBestSellerBadges();
            removeBestSellerCategoryTitles();
            restoreNormalCardOrder();

            const searchText = searchInput.value.toLowerCase().trim();

            if (searchText === "")
            {
                filterItems("all");
                setDashboardActive();
                return;
            }


            menuButtons.forEach(function (button)
            {
                button.classList.remove("active");
            });

            itemCards.forEach(function (card)
            {
                const itemName = (card.dataset.name || "").toLowerCase();
                const category = (card.dataset.category || "").toLowerCase();
                const itemType = (card.dataset.type || "").toLowerCase();
                const extra = (card.dataset.extra || "").toLowerCase();

                const searchableText = itemName + " " + category + " " + itemType + " " + extra;

                card.style.display = searchableText.includes(searchText) ? "flex" : "none";
            });
        });
    }

    function setDashboardActive()
    {
        menuButtons.forEach(function (button)
        {
            button.classList.remove("active");

            if (button.dataset.filter === "all")
            {
                button.classList.add("active");
            }
        });
    }

    // CART TOAST
    const cartToast = document.getElementById("cartToast");
    const toastClose = document.getElementById("toastClose");

    function hideToast()
    {
        if (!cartToast)
        {
            return;
        }

        cartToast.classList.add("toast-hide");

        setTimeout(function ()
        {
            cartToast.remove();
        }, 300);
    }

    if (cartToast)
    {
        setTimeout(function ()
        {
            hideToast();
        }, 3000);

        if (toastClose)
        {
            toastClose.addEventListener("click", function ()
            {
                hideToast();
            });
        }
    }
});