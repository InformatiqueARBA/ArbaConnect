// Sélectionne tous les boutons avec la classe "delete-article"
const deleteButtons = document.querySelectorAll(".delete-article");

// Ajoute un gestionnaire d'événements sur chaque bouton
deleteButtons.forEach((button) => {
  if (!button.classList.contains("event-attached")) {
    button.classList.add("event-attached");

    button.addEventListener("click", async (event) => {
      event.preventDefault();

      const articleId = button.getAttribute("data-article-id");

      if (confirm("Voulez-vous vraiment supprimer cet article ?")) {
        const deleteUrl = `/admin/liste-article-non-reference/supprimer/${articleId}`;

        try {
          const response = await fetch(deleteUrl, { method: "GET" });

          if (response.ok) {
            const row = button.closest("tr");
            if (row) {
              row.remove();
            }
            alert("Article supprimé avec succès !");
          } else {
            alert("Une erreur est survenue lors de la suppression.");
          }
        } catch (error) {
          alert("Une erreur réseau est survenue. Veuillez réessayer.");
        }
      }
    });
  }
});