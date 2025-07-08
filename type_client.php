 <?php include('template_header.php'); ?>


<h1 class="text-center my-4">Gestion des types de clients</h1>

<div class="container mb-4">
  <form id="form-type-client" class="row g-3 align-items-end">
    <input type="hidden" id="id">

    <div class="col-md-5">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" id="nom" class="form-control" placeholder="Nom" required>
    </div>

    <div class="col-md-5">
      <label for="description" class="form-label">Description</label>
      <input type="text" id="description" class="form-control" placeholder="Description">
    </div>

    <div class="col-md-2 text-end">
      <button type="button" onclick="ajouterOuModifier()" class="btn btn-primary">Ajouter / Modifier</button>
    </div>

    <div class="col-12">
      <div id="error-message" class="text-danger"></div>
    </div>
  </form>
</div>

<div class="container">
  <div class="table-responsive">
    <table id="table-types-clients" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Description</th>
          <th>Date Création</th>
          <th>Date Modification</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Les données seront insérées dynamiquement ici -->
      </tbody>
    </table>
  </div>
</div>


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
              errorCallback("Réponse non-JSON : " + xhr.responseText);
            }
          } else {
            errorCallback(`Erreur serveur (${xhr.status}) : ${xhr.responseText}`);
          }
        }
      };
      xhr.send(data);
    }

    function chargerTypesClients() {
      ajax("GET", "/type_clients", null, (data) => {
        console.log("Réponse de /type_clients :", data);
        const tbody = document.querySelector("#table-types-clients tbody");
        tbody.innerHTML = "";
        data.forEach(t => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${t.id}</td>
            <td>${t.nom}</td>
            <td>${t.description || ''}</td>
            <td>${t.date_creation}</td>
            <td>${t.date_modification || ''}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
              <button onclick='supprimerTypeClient(${t.id})'>🗑️</button>
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
      const description = document.getElementById("description").value;
      const errorMessage = document.getElementById("error-message");

      if (!nom) {
        errorMessage.textContent = "Veuillez remplir le champ nom.";
        return;
      }

      errorMessage.textContent = "";
      const data = `nom=${encodeURIComponent(nom)}&description=${encodeURIComponent(description)}`;

      if (id) {
        ajax("PUT", `/type_clients/${id}`, data, () => {
          resetForm();
          chargerTypesClients();
        }, (error) => {
          errorMessage.textContent = error;
        });
      } else {
        ajax("POST", "/type_clients", data, () => {
          resetForm();
          chargerTypesClients();
        }, (error) => {
          errorMessage.textContent = error;
        });
      }
    }

    function remplirFormulaire(t) {
      document.getElementById("id").value = t.id;
      document.getElementById("nom").value = t.nom;
      document.getElementById("description").value = t.description || '';
      document.getElementById("error-message").textContent = "";
    }

    function supprimerTypeClient(id) {
      if (confirm("Supprimer ce type de client ?")) {
        ajax("DELETE", `/type_clients/${id}`, null, () => {
          chargerTypesClients();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("description").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerTypesClients();
  </script>


<?php include('template_footer.php'); ?>
