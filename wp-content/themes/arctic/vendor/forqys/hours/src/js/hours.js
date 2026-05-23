/**
 * Status
 */

document.addEventListener("DOMContentLoaded", function () {
    fetch(hours_data.ajax_url, {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: new URLSearchParams({
            action: "hours_is_open",
            hours: JSON.stringify(hours_data.hours),
        })
    })
        .then(res => res.json())
        .then(data => {
            const statuses = document.querySelectorAll(".js-hours__status");
            if (statuses) {
                statuses.forEach(status => {
                    // status.textContent = data.open ? "We are open now" : "Currently closed";
                    status.classList.toggle("open", data.open);
                    status.classList.toggle("closed", !data.open);
                });
            }
        });
});