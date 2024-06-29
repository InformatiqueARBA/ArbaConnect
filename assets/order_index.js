
var filters = document.querySelectorAll('input[type="text"][id^="filter-"]');
var rows = document.querySelectorAll('#orders-table tbody tr');

filters.forEach(function (filter) {
    filter.addEventListener('input', function () {
        let columnIndex = parseInt(this.getAttribute('data-column'), 10);
        let filterValue = this.value.trim().toLowerCase();

        rows.forEach(function (row) {
            let isVisible = true;

            filters.forEach(function (f) {
                let colIndex = parseInt(f.getAttribute('data-column'), 10);
                let val = f.value.trim().toLowerCase();
                let cellValue = row.querySelectorAll('td')[colIndex].textContent.trim().toLowerCase();

                if (val && !cellValue.includes(val)) {
                    isVisible = false;
                }
            });

            if (isVisible) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

