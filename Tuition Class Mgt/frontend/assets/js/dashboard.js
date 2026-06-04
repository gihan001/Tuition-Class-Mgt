function toggleSidebar(event) {
    event.stopPropagation();

    document.querySelector(".sidebar").classList.toggle("active");
}

document.addEventListener("click", function(event) {
    let sidebar = document.querySelector(".sidebar");

    if (sidebar.classList.contains("active") && !sidebar.contains(event.target)) {
        sidebar.classList.remove("active");
    }
});

function openModal() {
    document.getElementById("addModal").classList.add("active");
}

function closeModal() {
    document.getElementById("addModal").classList.remove("active");
}

// Table Search Function
function searchTable() {
    // 1. පරිශීලකයා ටයිප් කරන දේ ලබාගෙන එය simple අකුරු (lowercase) බවට පත් කිරීම
    let input = document.getElementById("searchInput").value.toLowerCase();
    
    // 2. වගුවේ ඇති සියලුම දත්ත පේළි (table rows) තෝරා ගැනීම
    let tableRows = document.querySelectorAll(".data-table tbody tr");

    // 3. සෑම පේළියක්ම එකින් එක පරීක්ෂා කිරීම (Looping)
    tableRows.forEach(function(row) {
        
        // පේළිය ඇතුළත ඇති සම්පූර්ණ ලියවිල්ල ලබා ගැනීම
        let rowData = row.innerText.toLowerCase();

        // 4. ටයිප් කළ අකුරු එම පේළියේ තිබේදැයි බැලීම
        if (rowData.includes(input)) {
            row.style.display = ""; // තිබේ නම් පේළිය පෙන්වන්න
        } else {
            row.style.display = "none"; // නැති නම් පේළිය සඟවන්න (hide)
        }
        
    });
}