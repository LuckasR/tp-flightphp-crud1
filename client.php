 <?php include('template_header.php'); ?>
<h1 class="text-center my-4">Gestion des clients</h1>

<div class="container mb-4">
  <form id="form-client" class="row g-3 align-items-end">
    <input type="hidden" id="id">

    <div class="col-md-3">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" id="nom" class="form-control" placeholder="Nom">
    </div>

    <div class="col-md-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" id="email" class="form-control" placeholder="Email">
    </div>

    <div class="col-md-3">
      <label for="date_naissance" class="form-label">Date de naissance</label>
      <input type="date" id="date_naissance" class="form-control" placeholder="Date de naissance">
    </div>

    <div class="col-md-3">
      <label for="id_type_client" class="form-label">Type de client</label>
      <select id="id_type_client" class="form-select" required>
        <option value="">Sélectionner un type de client</option>
        <!-- Options à remplir dynamiquement -->
      </select>
    </div>

    <div class="col-12 text-end">
      <button type="button" onclick="ajouterOuModifier()" class="btn btn-primary">Ajouter / Modifier</button>
    </div>

    <div class="col-12">
      <div id="error-message" class="text-danger"></div>
    </div>
  </form>
</div>
<a href="type_client.php"> Ajout Type Client</a>
<div class="container">
  <div class="table-responsive">
    <table id="table-clients" class="table table-bordered table-striped align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Email</th>
          <th>Date de naissance</th>
          <th>Type de client</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Données à remplir dynamiquement -->
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
        const select = document.getElementById("id_type_client");
        select.innerHTML = '<option value="">Sélectionner un type de client</option>';
        if (data && data.length > 0) {
          data.forEach(t => {
            const option = document.createElement("option");
            option.value = t.id;
            option.textContent = t.nom;
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun type de client disponible.";
        }
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function chargerClients() {
      ajax("GET", "/clients", null, (data) => {
        console.log("Réponse de /clients :", data);
        const tbody = document.querySelector("#table-clients tbody");
        tbody.innerHTML = "";
        data.forEach(c => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${c.id}</td>
            <td>${c.nom || ''}</td>
            <td>${c.email || ''}</td>
            <td>${c.date_naissance || ''}</td>
            <td>${c.type_client_nom}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(c)})'>✏️</button>
              <button onclick='supprimerClient(${c.id})'>🗑️</button>
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
      const email = document.getElementById("email").value;
      const date_naissance = document.getElementById("date_naissance").value;
      const id_type_client = document.getElementById("id_type_client").value;
      const errorMessage = document.getElementById("error-message");

      if (!id_type_client) {
        errorMessage.textContent = "Veuillez sélectionner un type de client.";
        return;
      }

      errorMessage.textContent = "";
      const data = `nom=${encodeURIComponent(nom)}&email=${encodeURIComponent(email)}&date_naissance=${encodeURIComponent(date_naissance)}&id_type_client=${encodeURIComponent(id_type_client)}`;

      if (id) {
        ajax("PUT", `/clients/${id}`, data, () => {
          resetForm();
          chargerClients();
        }, (error) => {
          errorMessage.textContent = error;
        });
      } else {
        ajax("POST", "/clients", data, () => {
          resetForm();
          chargerClients();
        }, (error) => {
          errorMessage.textContent = error;
        });
      }
    }

    function remplirFormulaire(c) {
      document.getElementById("id").value = c.id;
      document.getElementById("nom").value = c.nom || '';
      document.getElementById("email").value = c.email || '';
      document.getElementById("date_naissance").value = c.date_naissance || '';
      document.getElementById("id_type_client").value = c.id_type_client;
      document.getElementById("error-message").textContent = "";
    }

    function supprimerClient(id) {
      if (confirm("Supprimer ce client、北2client ?")) {
        ajax("DELETE", `/clients/${id}`, null, () => {
          chargerClients();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("email").value = "";
      document.getElementById("date_naissance").value = "";
      document.getElementById("id_type_client").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerTypesClients();
    chargerClients();
  </script>

 

<?php include('template_footer.php'); ?>
