// var filters = document.querySelectorAll('input[type="text"][id^="filter-"]');
// var rows = document.querySelectorAll("#inventory-locations-table tbody tr");

// filters.forEach(function (filter) {
//   filter.addEventListener("input", function () {
//     let columnIndex = parseInt(this.getAttribute("data-column"), 10);
//     let filterValue = this.value.trim().toLowerCase();

//     rows.forEach(function (row) {
//       let isVisible = true;

//       filters.forEach(function (f) {
//         let colIndex = parseInt(f.getAttribute("data-column"), 10);
//         let val = f.value.trim().toLowerCase();
//         let cellValue = row
//           .querySelectorAll("td")
//           [colIndex].textContent.trim()
//           .toLowerCase();

//         if (val && !cellValue.includes(val)) {
//           isVisible = false;
//         }
//       });

//       if (isVisible) {
//         row.style.display = "";
//       } else {
//         row.style.display = "none";
//       }
//     });
//   });
// });

// // var filters = document.querySelectorAll('input[type="text"][id^="filter-"]');
// // var rows = document.querySelectorAll("#inventory-locations-table tbody tr");

// // // Charger les valeurs de filtres depuis le localStorage si elles existent
// // filters.forEach(function (filter) {
// //   let savedValue = localStorage.getItem(filter.id);
// //   if (savedValue) {
// //     filter.value = savedValue;
// //   }
// // });

// // function applyFilters() {
// //   rows.forEach(function (row) {
// //     let isVisible = true;

// //     filters.forEach(function (filter) {
// //       let columnIndex = parseInt(filter.getAttribute("data-column"), 10);
// //       let filterValue = filter.value.trim().toLowerCase();
// //       let cellValue = row
// //         .querySelectorAll("td")
// //         [columnIndex].textContent.trim()
// //         .toLowerCase();

// //       if (filterValue && !cellValue.includes(filterValue)) {
// //         isVisible = false;
// //       }
// //     });

// //     row.style.display = isVisible ? "" : "none";
// //   });
// // }

// // // Appliquer les filtres au chargement de la page
// // applyFilters();

// // filters.forEach(function (filter) {
// //   filter.addEventListener("input", function () {
// //     // Sauvegarder la valeur du filtre dans le localStorage
// //     localStorage.setItem(this.id, this.value);

// //     applyFilters();
// //   });
// // });

// // // Rafraîchir la page toutes les 10 secondes tout en conservant les filtres
// // setInterval(function () {
// //   window.location.reload();
// // }, 10000);

// // // Appliquer les filtres à nouveau après le rechargement
// // window.addEventListener("load", applyFilters);

// document.addEventListener("DOMContentLoaded", function () {
//   function refreshTable() {
//     fetch("/arba/inventaire/locations") // Appel à la nouvelle route
//       .then((response) => response.text())
//       .then((html) => {
//         // Remplacer le contenu du tableau
//         document.querySelector("#inventory-locations-table tbody").innerHTML =
//           html;
//       })
//       .catch((error) =>
//         console.error(
//           "Erreur lors du rafraîchissement des emplacements:",
//           error
//         )
//       );
//   }

//   // Rafraîchir toutes les 3 secondes
//   setInterval(refreshTable, 3000);
// });

console.log("Script chargé et exécuté");

// Déclaration des filtres
let filters = {
  inventoryNumber: "",
  warehouse: "",
  location: "",
  referent: "",
  status: "",
  action: "",
};

function saveFilters() {
  filters.inventoryNumber = document.querySelector("#filter-inventory-number").value;
  filters.warehouse = document.querySelector("#filter-warehouse").value;
  filters.location = document.querySelector("#filter-location").value;
  filters.referent = document.querySelector("#filter-referent").value;
  filters.status = document.querySelector("#filter-status").value;
  filters.action = document.querySelector("#filter-action").value;
}

function applyFilters() {
  document.querySelector("#filter-inventory-number").value = filters.inventoryNumber;
  document.querySelector("#filter-warehouse").value = filters.warehouse;
  document.querySelector("#filter-location").value = filters.location;
  document.querySelector("#filter-referent").value = filters.referent;
  document.querySelector("#filter-status").value = filters.status;
  document.querySelector("#filter-action").value = filters.action;

  filterTable();
}

function filterTable() {
  let rows = document.querySelectorAll("#inventory-locations-table tbody tr");

  rows.forEach((row) => {
    let showRow = true;

    if (filters.inventoryNumber && !row.cells[0].textContent.includes(filters.inventoryNumber)) {
      showRow = false;
    }
    if (filters.warehouse && !row.cells[1].textContent.includes(filters.warehouse)) {
      showRow = false;
    }
    if (filters.location && !row.cells[2].textContent.includes(filters.location)) {
      showRow = false;
    }
    if (filters.referent && !row.cells[3].textContent.includes(filters.referent)) {
      showRow = false;
    }
    if (filters.status && !row.cells[4].textContent.includes(filters.status)) {
      showRow = false;
    }
    if (filters.action && !row.cells[5].textContent.includes(filters.action)) {
      showRow = false;
    }

    row.style.display = showRow ? "" : "none";
  });
}

function refreshTable() {
  saveFilters(); // Sauvegarde des filtres avant le refresh

  fetch("/arba/inventaire/locations", {
    credentials: "same-origin", // S'assurer que les cookies de session sont envoyés
  })
    .then((response) => {
      if (response.status === 401 || response.redirected) {
        window.location.href = response.url || "/"; // Redirection vers une page de login
        return;
      }

      return response.text();
    })
    .then((html) => {
      if (html) {
        const tbody = document.querySelector("#inventory-locations-table tbody");

        tbody.innerHTML = ""; // Vider l'ancien contenu
        tbody.innerHTML = html; // Injecter le nouveau contenu

        applyFilters(); // Réappliquer les filtres après mise à jour
      }
    })
    .catch((error) => console.error("Erreur lors du rafraîchissement des emplacements:", error));
}

// Rafraîchir toutes les 3 secondes
setInterval(refreshTable, 3000);
