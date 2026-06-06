// Sidebar එක විවෘත සහ වැසීමේ ශ්‍රිතය
function toggleSidebar(event) {
    event.stopPropagation();
    document.querySelector(".sidebar").classList.toggle("active");
}

// Sidebar එකෙන් පිටත ක්ලික් කළ විට එය වැසීම
document.addEventListener("click", function(event) {
    let sidebar = document.querySelector(".sidebar");
    if (sidebar.classList.contains("active") && !sidebar.contains(event.target)) {
        sidebar.classList.remove("active");
    }
});

// Modal එක විවෘත කිරීම (CSS Flexbox සක්‍රීය වන පරිදි active class එක එකතු කරයි)
function openModal() {
    document.getElementById("addModal").classList.add("active");
}

// Modal එක වැසීම
function closeModal() {
    document.getElementById("addModal").classList.remove("active");
}

// Modal එකෙන් පිටත (අඳුරු පසුබිම මත) ක්ලික් කළ විට ස්වයංක්‍රීයව වැසීම
window.addEventListener("click", function(event) {
    let modal = document.getElementById('addModal');
    if (event.target === modal) {
        modal.classList.remove("active");
    }
});

// වගුවේ දත්ත සෙවීමේ ශ්‍රිතය (Table Search Function)
function searchTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let tableRows = document.querySelectorAll(".data-table tbody tr");

    tableRows.forEach(function(row) {
        let rowData = row.innerText.toLowerCase();

        if (rowData.includes(input)) {
            row.style.display = ""; // දත්ත තිබේ නම් පෙන්වන්න
        } else {
            row.style.display = "none"; // නැත්නම් සඟවන්න
        }
    });
}