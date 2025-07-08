 <?php include('template_header.php'); ?>

<h1 class="my-4 text-center text-primary">Gestion des comptes bancaires</h1>

<div class="container mb-4">
  <form id="form-compte" class="row g-3">
    <input type="hidden" id="id">

    <div class="col-md-6">
      <label for="id_client" class="form-label">Client</label>
      <select id="id_client" class="form-select" required>
        <option value="">Sélectionner un client</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="solde_compte" class="form-label">Solde initial (€)</label>
      <input type="number" id="solde_compte" class="form-control" placeholder="Ex: 500.00" step="0.01" required>
    </div>

    <div class="col-12">
      <button type="button" onclick="ajouterOuModifier()" class="btn btn-primary w-100">
        Ajouter / Modifier
      </button>
    </div>

    <div id="error-message" class="text-danger text-center"></div>
  </form>
</div>

<div class="container">
  <table id="table-comptes-bancaires" class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Numéro de compte</th>
        <th>Client</th>
        <th>Solde (€)</th>
        <th>Dernière modification</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
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

    function chargerClients() {
      ajax("GET", "/clients", null, (data) => {
        console.log("Réponse de /clients :", data);
        const select = document.getElementById("id_client");
        select.innerHTML = '<option value="">Sélectionner un client</option>';
        if (data && data.length > 0) {
          data.forEach(c => {
            const option = document.createElement("option");
            option.value = c.id;
            option.textContent = c.nom || 'Client sans nom';
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun client disponible.";
        }
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function chargerComptesBancaires() {
      ajax("GET", "/comptes_bancaires", null, (data) => {
        console.log("Réponse de /comptes_bancaires :", data);
        const tbody = document.querySelector("#table-comptes-bancaires tbody");
        tbody.innerHTML = "";
        data.forEach(cb => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${cb.id}</td>
            <td>${cb.numero_compte}</td>
            <td>${cb.client_nom}</td>
            <td>${cb.solde_compte}</td>
            <td>${cb.last_change || ''}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(cb)})'>✏️</button>
              <button onclick='supprimerCompteBancaire(${cb.id})'>🗑️</button>
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
      const id_client = document.getElementById("id_client").value;
      const solde_compte = document.getElementById("solde_compte").value;
      const errorMessage = document.getElementById("error-message");

      if (!id_client || !solde_compte) {
        errorMessage.textContent = "Veuillez remplir tous les champs obligatoires.";
        return;
      }

      errorMessage.textContent = "";
      const data = `id_client=${encodeURIComponent(id_client)}&solde_compte=${encodeURIComponent(solde_compte)}`;

      if (id) {
        ajax("PUT", `/comptes_bancaires/${id}`, data, () => {
          resetForm();
          chargerComptesBancaires();
        }, (error) => {
          errorMessage.textContent = error;
        });
      } else {
        ajax("POST", "/comptes_bancaires", data, () => {
          resetForm();
          chargerComptesBancaires();
        }, (error) => {
          errorMessage.textContent = error;
        });
      }
    }

    function remplirFormulaire(cb) {
      document.getElementById("id").value = cb.id;
      document.getElementById("id_client").value = cb.id_client;
      document.getElementById("solde_compte").value = cb.solde_compte;
      document.getElementById("error-message").textContent = "";
    }

    function supprimerCompteBancaire(id) {
      if (confirm("Supprimer ce compte bancaire ?")) {
        ajax("DELETE", `/comptes_bancaires/${id}`, null, () => {
          chargerComptesBancaires();
        }, (error) => {
          document.getElementById("error-message").textContent = error;
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("id_client").value = "";
      document.getElementById("solde_compte").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerClients();
    chargerComptesBancaires();
  </script>
 <?php include('template_footer.php'); ?>
