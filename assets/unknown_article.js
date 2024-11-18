document.addEventListener("DOMContentLoaded", function () {
  function attachDeleteEvents() {
    const deleteButtons = document.querySelectorAll(".delete-article");

    deleteButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const articleId = this.dataset.id;

        if (!articleId) {
          alert("Impossible de supprimer cet article : données manquantes.");
          return;
        }

        if (confirm("Êtes-vous sûr de vouloir supprimer cet article ?")) {
          // URL de l'action Symfony
          const url = `/admin/liste-article-non-reference/supprimer/${articleId}`;

          // Requête GET avec fetch
          fetch(url, {
            method: "GET",
          })
            .then((response) => {
              if (response.ok) {
                // Supprimer la ligne de l'article
                const row = button.closest("tr");
                row.parentNode.removeChild(row);

                alert("Article supprimé avec succès.");
              } else {
                alert("Une erreur s'est produite lors de la suppression.");
              }
            })
            .catch((error) => {
              console.error("Erreur:", error);
              alert("Une erreur inattendue s'est produite.");
            });
        }
      });
    });
  }

  // Assurer l'attachement des événements après le chargement initial
  attachDeleteEvents();
});
