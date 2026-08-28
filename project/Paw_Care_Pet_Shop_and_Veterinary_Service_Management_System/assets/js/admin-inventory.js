const tabs = document.querySelectorAll(".inventory-tab");
const panels = document.querySelectorAll(".inventory-panel");
const searchInput = document.getElementById("inventorySearch");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        tabs.forEach(item => item.classList.remove("active"));
        panels.forEach(panel => panel.classList.remove("active-panel"));

        tab.classList.add("active");
        document.getElementById(tab.dataset.target).classList.add("active-panel");
        searchInput.value = "";
        filterInventory("");
    });
});

function filterInventory(value) {
    const activePanel = document.querySelector(".inventory-panel.active-panel");
    const rows = activePanel.querySelectorAll(".inventory-row");
    const search = value.toLowerCase().trim();

    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(search) ? "" : "none";
    });
}

searchInput.addEventListener("input", () => filterInventory(searchInput.value));