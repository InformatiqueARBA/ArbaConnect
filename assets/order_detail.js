// Fonction pour charger et lire le fichier CSV
function loadCSVFile(filePath, callback) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                callback(xhr.responseText);
            } else {
                console.error('Erreur de chargement du fichier CSV : ' + xhr.status);
            }
        }
    };
    xhr.open('GET', filePath, true);
    xhr.send();
}

// Chemin local vers le fichier CSV
var csvFilePath = '/csv/tour_code/tour_code.csv';
// let csvFilePath = '/csv/resultats.csv';

// Appeler la fonction pour charger le fichier CSV
loadCSVFile(csvFilePath, function (csvContent) {
    // Traitement du contenu CSV
    let lines = csvContent.split('\n');
    let deliveryDates = {};

    // Parcourir les lignes du CSV et remplir deliveryDates
    for (let i = 1; i < lines.length; i++) {
        let line = lines[i].trim();
        if (line) {
            let parts = line.split(';');
            if (parts.length === 2) {
                let tourCode = parts[0].trim();
                let deliveryDate = parts[1].trim();
                deliveryDates[deliveryDate] = tourCode;
            }
        }
    }

    // Initialisation de Flatpickr avec les options nécessaires
    flatpickr(".flatpickr-input", {
        dateFormat: "d/m/Y",
        minDate: "today",
        maxDate: new Date().fp_incr(365),
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
                longhand: [
                    "Dimanche",
                    "Lundi",
                    "Mardi",
                    "Mercredi",
                    "Jeudi",
                    "Vendredi",
                    "Samedi",
                ],
            },
            months: {
                shorthand: [
                    "Jan",
                    "Fév",
                    "Mar",
                    "Avr",
                    "Mai",
                    "Juin",
                    "Juil",
                    "Aoû",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Déc",
                ],
                longhand: [
                    "Janvier",
                    "Février",
                    "Mars",
                    "Avril",
                    "Mai",
                    "Juin",
                    "Juillet",
                    "Août",
                    "Septembre",
                    "Octobre",
                    "Novembre",
                    "Décembre",
                ],
            },
        },
        onDayCreate: function (dObj, dStr, fp, dayElem) {
            const date = new Date(dayElem.dateObj);
            const formattedDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

            // Désactiver tous les jours par défaut
            dayElem.classList.add('disabled-day');
            dayElem.classList.add('disabled');

            // Activer les jours selon le tourCode et les dates de livraison
            // if (tourCode === 'S' && deliveryDates[formattedDate] === 'S') {
            //     dayElem.classList.remove('disabled-day');
            //     dayElem.classList.remove('disabled');
            //     dayElem.classList.add('workday-1');
            // } else if (tourCode === 'N' && deliveryDates[formattedDate] === 'N') {
            //     dayElem.classList.remove('disabled-day');
            //     dayElem.classList.remove('disabled');
            //     dayElem.classList.add('workday-2');
            // }
        }
    });
});
