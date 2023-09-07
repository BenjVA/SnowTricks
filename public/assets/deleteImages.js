let links = document.querySelectorAll("[data-delete]");

for (let link of links) {
    link.addEventListener("click", function (e) {
        e.preventDefault();

        if (confirm("Êtes-vous sur de vouloir supprimer cette image ? ")) {
            fetch(this.getAttribute("href"), {
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({"_token": this.dataset.token})
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.parentElement.remove();
                        alert("L'image a été correctement supprimée.")
                    } else {
                        alert(data.error);
                    }
                })
        }
    });
}
