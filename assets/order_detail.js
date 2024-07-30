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

// Appeler la fonction pour charger le fichier CSV
loadCSVFile(csvFilePath, function (csvContent) {
    // Afficher le contenu du fichier CSV pour vérification (facultatif)
    console.log('Contenu du fichier CSV :');
    console.log(csvContent);

    // Traitement du contenu CSV
    let lines = csvContent.split('\n');
    let deliveryDates = {};

    // Parcourir les lignes du CSV et remplir deliveryDates
    for (let i = 1; i < lines.length; i++) {
        let line = lines[i].trim();
        if (line) {
            let parts = line.split(';');
            if (parts.length === 3) {
                let tourCode = parts[0].trim();
                let deliveryDate = parts[1].trim();
                let limitDate = parts[2].trim();
                deliveryDates[deliveryDate] = { tourCode: tourCode, limitDate: limitDate };
            }
        }
    }

    // Afficher le tableau deliveryDates pour vérification (facultatif)
    console.log('Contenu de deliveryDates :');
    console.log(deliveryDates);

    // Appliquer les couleurs aux jours dans flatpickr en fonction de deliveryDates
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

            if (deliveryDates.hasOwnProperty(formattedDate)) {
                if (deliveryDates[formattedDate].tourCode === 'S') {
                    dayElem.classList.add('workday-1');
                } else if (deliveryDates[formattedDate].tourCode === 'N') {
                    dayElem.classList.add('workday-2');
                }
            }
        },
        disable: [
            function (date) {
                const day = date.getDay();
                const formattedDate = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                const currentDateTime = new Date();
                // Disable weekends
                if (day === 0 || day === 6) {
                    return true;
                }

                // Disable dates that do not exist in deliveryDates
                if (!deliveryDates.hasOwnProperty(formattedDate)) {
                    return true;
                }

                // Disable 'N' days if tc is 'S'
                if (tc === 'S' && deliveryDates[formattedDate].tourCode === 'N') {
                    return true;
                }

                // Disable 'S' days if tc is 'N'
                if (tc === 'N' && deliveryDates[formattedDate].tourCode === 'S') {
                    return true;
                }

                //Désactive la livraison J+1 si le cut off est dépassé  
                if (currentDateTime > new Date(deliveryDates[formattedDate].limitDate)) {
                    return true;
                }

                return false;
            }
        ]
    });
});