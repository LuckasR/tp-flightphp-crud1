 <?php include('template_header.php'); ?>

  <h1 class="text-center text-primary my-4">Gestion des mouvements d'établissement</h1>

<div class="container mb-4">
  <form class="row g-3 align-items-end">

    <input type="hidden" id="id">

    <div class="col-md-4">
      <label for="id_admin" class="form-label">Administrateur</label>
      <select id="id_admin" class="form-select" required>
        <option value="">Sélectionner un administrateur</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="id_type" class="form-label">Type de mouvement</label>
      <select id="id_type" class="form-select" required>
        <option value="">Sélectionner un type</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="id_client" class="form-label">Client</label>
      <select id="id_client" class="form-select" required>
        <option value="">Sélectionner un client</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="montant" class="form-label">Montant</label>
      <input type="number" id="montant" class="form-control" placeholder="Montant" step="0.01" required>
    </div>

    <div class="col-md-4">
      <label for="description" class="form-label">Description</label>
      <input type="text" id="description" class="form-control" placeholder="Description">
    </div>

    <div class="col-md-4">
      <label for="reference_externe" class="form-label">Référence externe</label>
      <input type="text" id="reference_externe" class="form-control" placeholder="Référence externe">
    </div>

    <div class="col-12 text-end">
      <button type="button" class="btn btn-success" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
    </div>

    <div class="col-12">
      <div id="error-message" class="text-danger fw-bold"></div>
    </div>

  </form>
</div>

<div class="container mb-5">
  <h4 class="mb-3">Liste des mouvements</h4>
  <div class="table-responsive">
    <table id="table-mouvements" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Administrateur</th>
          <th>Type</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Description</th>
          <th>Référence externe</th>
          <th>Date mouvement</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
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
            callback(JSON.parse(xhr.responseText));
          } else {
            console.error("Erreur serveur :", xhr.responseText);
            if (errorCallback) errorCallback(xhr.responseText);
          }
        }
      };
      xhr.send(data);
    }

    function chargerAdmins() {
      ajax("GET", "/admins", null, (data) => {
        const select = document.getElementById("id_admin");
        select.innerHTML = '<option value="">Sélectionner un administrateur</option>';
        if (data && data.length > 0) {
          data.forEach(a => {
            const option = document.createElement("option");
            option.value = a.id;
            option.textContent = a.nom;
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun administrateur disponible.";
        }
      }, (error) => {
        console.error("Erreur AJAX :", error);
        document.getElementById("error-message").textContent = "Erreur lors du chargement des administrateurs.";
      });
    }

    function chargerTypesMouvement() {
      ajax("GET", "/mouvements", null, (data) => {
        const select = document.getElementById("id_type");
        select.innerHTML = '<option value="">Sélectionner un type de mouvement</option>';
        if (data && data.length > 0) {
          data.forEach(t => {
            const option = document.createElement("option");
            option.value = t.id;
            option.textContent = t.nom;
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun type de mouvement disponible.";
        }
      }, (error) => {
        console.error("Erreur AJAX :", error);
        document.getElementById("error-message").textContent = "Erreur lors du chargement des types de mouvement.";
      });
    }

   

    function chargerClients() {
      ajax("GET", "/clients", null, (data) => {
        const select = document.getElementById("id_client");
        select.innerHTML = '<option value="">Sélectionner un client</option>';
        if (data && data.length > 0) {
          data.forEach(c => {
            const option = document.createElement("option");
            option.value = c.id;
            option.textContent = c.nom;
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun client disponible.";
        }
      }, (error) => {
        console.error("Erreur AJAX :", error);
        document.getElementById("error-message").textContent = "Erreur lors du chargement des clients.";
      });
    }

    function chargerMouvements() {
      ajax("GET", "/mouvementsEtablissement", null, (data) => {
        const tbody = document.querySelector("#table-mouvements tbody");
        tbody.innerHTML = "";
        data.forEach(m => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${m.id}</td>
            <td>${m.admin_nom}</td>
            <td>${m.type_nom}</td>
            <td>${m.client_nom}</td>
            <td>${m.montant}</td>
            <td>${m.description || ''}</td>
            <td>${m.reference_externe || ''}</td>
            <td>${m.date_mouvement}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(m)})'>✏️</button>
              <button onclick='supprimerMouvement(${m.id})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const id_admin = document.getElementById("id_admin").value;
      const id_type = document.getElementById("id_type").value;
      const id_client = document.getElementById("id_client").value;
      const montant = document.getElementById("montant").value;
      const description = document.getElementById("description").value;
      const reference_externe = document.getElementById("reference_externe").value;
      const errorMessage = document.getElementById("error-message");

      if (!id_admin || !id_type || !id_client || !montant) {
        errorMessage.textContent = "Veuillez remplir tous les champs obligatoires.";
        return;
      }

      errorMessage.textContent = "";
      const data = `id_admin=${encodeURIComponent(id_admin)}&id_type=${encodeURIComponent(id_type)}&id_client=${encodeURIComponent(id_client)}&montant=${encodeURIComponent(montant)}&description=${encodeURIComponent(description)}&reference_externe=${encodeURIComponent(reference_externe)}`;

      if (id) {
        ajax("PUT", `/mouvementsEtablissement/${id}`, data, () => {
          resetForm();
          chargerMouvements();
        }, (error) => {
          errorMessage.textContent = "Erreur lors de la mise à jour.";
        });
      } else {
        ajax("POST", "/mouvementsETablissement", data, () => {
          resetForm();
          chargerMouvements();
        }, (error) => {
          errorMessage.textContent = "Erreur lors de la création.";
        });
      }
    }

    function remplirFormulaire(m) {
      document.getElementById("id").value = m.id;
      document.getElementById("id_admin").value = m.id_admin;
      document.getElementById("id_type").value = m.id_type;
      document.getElementById("id_client").value = m.id_client;
      document.getElementById("montant").value = m.montant;
      document.getElementById("description").value = m.description || '';
      document.getElementById("reference_externe").value = m.reference_externe || '';
      document.getElementById("error-message").textContent = "";
    }

    function supprimerMouvement(id) {
      if (confirm("Supprimer ce mouvement d'établissement ?")) {
        ajax("DELETE", `/mouvementsEtablissement/${id}`, null, () => {
          chargerMouvements();
        }, (error) => {
          document.getElementById("error-message").textContent = "Erreur lors de la suppression.";
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("id_admin").value = "";
      document.getElementById("id_type").value = "";
      document.getElementById("id_client").value = "";
      document.getElementById("montant").value = "";
      document.getElementById("description").value = "";
      document.getElementById("reference_externe").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerAdmins();
    chargerTypesMouvement();
    chargerClients();
    chargerMouvements();
  </script>


<?php include('template_footer.php'); ?>
