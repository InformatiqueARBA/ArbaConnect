document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll(".delete-article").forEach(button => {
      button.addEventListener("click", function() {
          const articleId = this.getAttribute("data-article-id");
          const row = this.closest("tr");

          if (confirm("Voulez-vous vraiment supprimer cet article ?")) {
              fetch(`/admin/suppression-article-inconnu/${articleId}`, {
                  method: 'DELETE',
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest',
                  },
              })
              .then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      row.remove(); // Supprime la ligne du tableau si la suppression est réussie
                      alert(data.message);
                  } else {
                      alert("Erreur lors de la suppression : " + data.message);
                  }
              })
              .catch(error => {
                  alert("Erreur réseau ou serveur.");
                  console.error("Error:", error);
              });
          }
      });
  });
});
