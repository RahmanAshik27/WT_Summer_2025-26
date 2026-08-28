function updateDateTime() {
    const now = new Date();

    const date = now.toLocaleDateString("en-US", {
        weekday: "long",
        month: "short",
        day: "2-digit",
        year: "numeric"
    });

    const time = now.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });

    document.getElementById("currentDate").textContent = date;
    document.getElementById("currentTime").textContent = time;
}

updateDateTime();
setInterval(updateDateTime, 1000);