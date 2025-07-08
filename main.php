<?php include('template_header.php'); ?>

 <h1 class="mb-4">Gestion des établissements financiers</h1>

<div class="mb-4">
  <form id="form-etablissement" onsubmit="event.preventDefault(); ajouterOuModifier();">
    <input type="hidden" id="id" name="id">

    <div class="mb-3">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
    </div>

    <div class="mb-3">
      <label for="adresse" class="form-label">Adresse</label>
      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse">
    </div>

    <div class="mb-3">
      <label for="telephone" class="form-label">Téléphone</label>
      <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Téléphone">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email">
    </div>

    <div class="mb-3">
      <label for="curr_montant" class="form-label">Montant actuel</label>
      <input type="number" class="form-control" id="curr_montant" name="curr_montant" placeholder="Montant actuel" readonly>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter / Modifier</button>
    <div id="error-message" class="text-danger mt-2"></div>
  </form>
</div>

<table id="table-etablissements" class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Adresse</th>
      <th>Téléphone</th>
      <th>Email</th>
      <th>Montant</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

  <script>
    const apiBase = "http://localhost/tp-flightphp-crud1/ws";

    function ajax(method, url, data, callback, errorCallback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              callback(JSON.parse(xhr.responseText));
            } catch (e) {
              console.error(`Erreur parsing JSON pour ${url}:`, e, xhr.responseText);
              if (errorCallback) errorCallback(`Erreur de format de réponse pour ${url}`);
            }
          } else {
            console.error(`Erreur ${xhr.status} pour ${url}:`, xhr.responseText);
            if (errorCallback) errorCallback(`Erreur serveur ${xhr.status} pour ${url}: ${xhr.responseText}`);
          }
        }
      };
      xhr.send(data);
    }

    function chargerEtablissements() {
      ajax("GET", "/etablissements", null, (data) => {
        const tbody = document.querySelector("#table-etablissements tbody");
        tbody.innerHTML = "";
        data.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.id}</td>
            <td>${e.nom}</td>
            <td>${e.adresse}</td>
            <td>${e.telephone}</td>
            <td>${e.email}</td>
            <td>${e.curr_montant}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(e)})'>✏️</button>
              <button onclick='supprimerEtablissement(${e.id})'>🗑️</button>
              <button onclick='updateCurrMontant(${e.id})'>🔄 Mettre à jour Montant</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const nom = document.getElementById("nom").value;
      const adresse = document.getElementById("adresse").value;
      const telephone = document.getElementById("telephone").value;
      const email = document.getElementById("email").value;
      // curr_montant is read-only, not included in update
      const data = `nom=${encodeURIComponent(nom)}&adresse=${encodeURIComponent(adresse)}&telephone=${encodeURIComponent(telephone)}&email=${encodeURIComponent(email)}`;

      if (!nom || !email) {
        document.getElementById("error-message").textContent = "Nom et email sont obligatoires.";
        return;
      }

      if (id) {
        ajax("PUT", `/etablissements/${id}`, data, () => {
          resetForm();
          chargerEtablissements();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      } else {
        ajax("POST", "/etablissements", data, () => {
          resetForm();
          chargerEtablissements();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function updateCurrMontant(id) {
      if (confirm("Mettre à jour le montant actuel de cet établissement ?")) {
        ajax("PUT", `/etablissements/${id}/curr_montant`, null, (response) => {
          alert(response.message);
          chargerEtablissements();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function remplirFormulaire(e) {
      document.getElementById("id").value = e.id;
      document.getElementById("nom").value = e.nom;
      document.getElementById("adresse").value = e.adresse;
      document.getElementById("telephone").value = e.telephone;
      document.getElementById("email").value = e.email;
      document.getElementById("curr_montant").value = e.curr_montant;
    }

    function supprimerEtablissement(id) {
      if (confirm("Supprimer cet établissement ?")) {
        ajax("DELETE", `/etablissements/${id}`, null, () => {
          chargerEtablissements();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("adresse").value = "";
      document.getElementById("telephone").value = "";
      document.getElementById("email").value = "";
      document.getElementById("curr_montant").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerEtablissements();
  </script>



<?php include('template_footer.php'); ?>
