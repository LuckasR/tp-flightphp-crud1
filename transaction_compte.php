 <?php include('template_header.php'); ?>

<h1 class="text-center text-primary my-4">Gestion des transactions</h1>

<div class="container mb-4">
  <form class="row g-3 align-items-end">
    
    <input type="hidden" id="id">

    <div class="col-md-4">
      <label for="compte_id" class="form-label">Compte</label>
      <select id="compte_id" class="form-select" required>
        <option value="">Sélectionner un compte</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="id_type" class="form-label">Type de transaction</label>
      <select id="id_type" class="form-select" required onchange="toggleCompteCible()">
        <option value="">Sélectionner un type</option>
      </select>
    </div>

    <div class="col-md-4" id="compte_cible_id_container" style="display: none;">
      <label for="compte_cible_id" class="form-label">Compte cible (pour transfert)</label>
      <select id="compte_cible_id" class="form-select">
        <option value="">Sélectionner le compte cible</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="montant" class="form-label">Montant</label>
      <input type="number" id="montant" class="form-control" placeholder="Montant" step="0.01" required>
    </div>

    <div class="col-md-8">
      <label for="description" class="form-label">Description</label>
      <input type="text" id="description" class="form-control" placeholder="Description">
    </div>

    <div class="col-12 text-end">
      <button type="button" class="btn btn-success" onclick="ajouterTransaction()">Ajouter Transaction</button>
    </div>

    <div class="col-12">
      <div id="error-message" class="text-danger fw-bold"></div>
    </div>

  </form>
</div>

<div class="container">
  <h4 class="mb-3">Liste des transactions</h4>
  <div class="table-responsive">
    <table id="table-transactions" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Compte</th>
          <th>Type</th>
          <th>Montant</th>
          <th>Date</th>
          <th>Description</th>
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
          console.log(`Requête ${method} ${url} - Statut: ${xhr.status}, Réponse: ${xhr.responseText}`);
          if (xhr.status === 200) {
            try {
              const response = xhr.responseText ? JSON.parse(xhr.responseText) : {};
              callback(response);
            } catch (e) {
              errorCallback(`Erreur de parsing JSON : ${e.message} (Réponse brute : ${xhr.responseText})`);
            }
          } else {
            errorCallback(`Erreur serveur (${xhr.status}) : ${xhr.responseText}`);
          }
        }
      };
      xhr.send(data);
    }

    function chargerComptes() {
      ajax("GET", "/comptes_bancaires", null, (data) => {
        console.log("Réponse de /comptes_bancaires :", data);
        const select = document.getElementById("compte_id");
        const selectCible = document.getElementById("compte_cible_id");
        select.innerHTML = '<option value="">Sélectionner un compte</option>';
        selectCible.innerHTML = '<option value="">Sélectionner le compte cible (pour transfert)</option>';
        if (data && data.length > 0) {
          data.forEach(cb => {
            const option = document.createElement("option");
            option.value = cb.id;
            option.textContent = cb.numero_compte + ' (' + cb.client_nom + ')';
            select.appendChild(option);
            const optionCible = document.createElement("option");
            optionCible.value = cb.id;
            optionCible.textContent = cb.numero_compte + ' (' + cb.client_nom + ')';
            selectCible.appendChild(optionCible);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun compte disponible.";
        }
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function chargerTypes() {
      ajax("GET", "/categories", null, (data) => {
        console.log("Réponse de /categories :", data);
        const select = document.getElementById("id_type");
        select.innerHTML = '<option value="">Sélectionner un type</option>';
        if (data && data.length > 0) {
          data.forEach(t => {
            const option = document.createElement("option");
            option.value = t.id;
            option.textContent = t.type_name;
            select.appendChild(option);
          });
        } else {
          document.getElementById("error-message").textContent = "Aucun type de transaction disponible.";
        }
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function chargerTransactions() {
      ajax("GET", "/transactions_compte", null, (data) => {
        console.log("Réponse de /transactions_compte :", data);
        const tbody = document.querySelector("#table-transactions tbody");
        tbody.innerHTML = "";
        data.forEach(t => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${t.id}</td>
            <td>${t.numero_compte}</td>
            <td>${t.type_name}</td>
            <td>${t.montant}</td>
            <td>${t.date_transaction}</td>
            <td>${t.description || ''}</td>
            <td>
              <button disabled>✏️</button>
              <button disabled>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function toggleCompteCible() {
      const idType = document.getElementById("id_type").value;
      if (!idType) {
        document.getElementById("compte_cible_id_container").style.display = "none";
        return;
      }
      ajax("GET", `/categories/${idType}`, null, (data) => {
        const container = document.getElementById("compte_cible_id_container");
        container.style.display = data.type_name === 'transfert' ? 'block' : 'none';
      }, (error) => {
        document.getElementById("error-message").textContent = error;
      });
    }

    function ajouterTransaction() {
      const compte_id = document.getElementById("compte_id").value;
      const id_type = document.getElementById("id_type").value;
      const montant = document.getElementById("montant").value;
      const description = document.getElementById("description").value;
      const compte_cible_id = document.getElementById("compte_cible_id").value;
      const errorMessage = document.getElementById("error-message");

      if (!compte_id || !id_type || !montant) {
        errorMessage.textContent = "Veuillez remplir tous les champs obligatoires.";
        return;
      }

      ajax("GET", `/categories/${id_type}`, null, (typeData) => {
        let data = `compte_id=${encodeURIComponent(compte_id)}&id_type=${encodeURIComponent(id_type)}&montant=${encodeURIComponent(montant)}&description=${encodeURIComponent(description)}`;
        if (typeData.type_name === 'transfert' && compte_cible_id) {
          data += `&compte_cible_id=${encodeURIComponent(compte_cible_id)}`;
        } else if (typeData.type_name === 'transfert') {
          errorMessage.textContent = "Compte cible requis pour un transfert.";
          return;
        }

        errorMessage.textContent = "";
        ajax("POST", "/transactions_compte", data, (response) => {
          console.log("Transaction créée :", response);
          resetForm();
          chargerTransactions();
        }, (error) => {
          errorMessage.textContent = error;
        });
      }, (error) => {
        errorMessage.textContent = error;
      });
    }

    function resetForm() {
      document.getElementById("compte_id").value = "";
      document.getElementById("id_type").value = "";
      document.getElementById("compte_cible_id").value = "";
      document.getElementById("montant").value = "";
      document.getElementById("description").value = "";
      document.getElementById("compte_cible_id_container").style.display = "none";
      document.getElementById("error-message").textContent = "";
    }

    chargerComptes();
    chargerTypes();
    chargerTransactions();
  </script>

<?php include('template_footer.php'); ?>
