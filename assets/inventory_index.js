var filters = document.querySelectorAll('input[type="text"][id^="filter-"]');
var rows = document.querySelectorAll("#inventory-locations-table tbody tr");

filters.forEach(function (filter) {
  filter.addEventListener("input", function () {
    let columnIndex = parseInt(this.getAttribute("data-column"), 10);
    let filterValue = this.value.trim().toLowerCase();

    rows.forEach(function (row) {
      let isVisible = true;

      filters.forEach(function (f) {
        let colIndex = parseInt(f.getAttribute("data-column"), 10);
        let val = f.value.trim().toLowerCase();
        let cellValue = row
          .querySelectorAll("td")
          [colIndex].textContent.trim()
          .toLowerCase();

        if (val && !cellValue.includes(val)) {
          isVisible = false;
        }
      });

      if (isVisible) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
});
// document.addEventListener("DOMContentLoaded", function () {
//   function filterTable() {
//     // Récupérer les valeurs sélectionnées
//     var checkboxes = document.querySelectorAll(".filter-warehouse");
//     var selectedWarehouses = Array.from(checkboxes)
//       .filter((checkbox) => checkbox.checked)
//       .map((checkbox) => checkbox.value);

//     // Afficher ou masquer les lignes du tableau
//     var rows = document.querySelectorAll("#inventory-locations-table tbody tr");
//     rows.forEach(function (row) {
//       var warehouse = row.cells[1].textContent.trim();
//       if (selectedWarehouses.includes(warehouse)) {
//         row.style.display = "";
//       } else {
//         row.style.display = "none";
//       }
//     });
//   }

//   // Ajouter un écouteur d'événement sur les cases à cocher
//   var checkboxes = document.querySelectorAll(".filter-warehouse");
//   checkboxes.forEach(function (checkbox) {
//     checkbox.addEventListener("change", filterTable);
//   });

//   // Filtrer le tableau au chargement de la page
//   filterTable();
// });
