<?php include('template_header.php'); ?>
 
<div class="container mt-5">
  <h1 class="mb-4">Gestion des simulations de prêt</h1>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form id="form-simulation">
        <input type="hidden" id="id">

        <div class="row g-3">
          <div class="col-md-4">
            <select class="form-select" id="id_client" required>
              <option value="">-- Sélectionner un client --</option>
            </select>
          </div>

          <div class="col-md-4">
            <select class="form-select" id="id_type_pret" required>
              <option value="">-- Sélectionner un type de prêt --</option>
            </select>
          </div>

          <div class="col-md-4">
            <input class="form-control" type="number" step="0.01" id="montant_demande" placeholder="Montant demandé" required>
          </div>

          <div class="col-md-4">
            <input class="form-control" type="number" id="duree_demandee" placeholder="Durée (mois)" required>
          </div>

          <div class="col-md-4">
            <input class="form-control" type="date" id="date_expiration" placeholder="Date expiration">
          </div>

          <div class="col-12 d-flex justify-content-start gap-2 mt-3">
            <button type="button" class="btn btn-primary" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
            <button type="reset" class="btn btn-secondary" onclick="resetForm()">Réinitialiser</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle" id="table-simulations">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Type Prêt</th>
          <th>Montant demandé</th>
          <th>Durée (mois)</th>
          <th>Date expiration</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
  const apiBase = "http://localhost/tp-flightphp-crud1/ws";

  function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        callback(JSON.parse(xhr.responseText));
      }
    };
    xhr.send(data);
  }

  function chargerSimulations() {
    ajax("GET", "/simulations", null, (data) => {
      const tbody = document.querySelector("#table-simulations tbody");
      tbody.innerHTML = "";
      data.forEach(s => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${s.id}</td>
          <td>${s.id_client}</td>
          <td>${s.id_type_pret}</td>
          <td>${s.montant_demande}</td>
          <td>${s.duree_demandee}</td>
          <td>${s.date_expiration ?? ''}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary me-1" onclick='remplirFormulaire(${JSON.stringify(s)})'>✏️</button>
            <button class="btn btn-sm btn-outline-danger" onclick='supprimerSimulation(${s.id})'>🗑️</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    });
  }

  function chargerSelectClients() {
    ajax("GET", "/clients", null, (clients) => {
      const select = document.getElementById("id_client");
      select.innerHTML = '<option value="">-- Sélectionner un client --</option>';
      clients.forEach(c => {
        select.innerHTML += `<option value="${c.id}">${c.nom}</option>`;
      });
    });
  }

  function chargerSelectTypesPret() {
    ajax("GET", "/typeprets", null, (types) => {
      const select = document.getElementById("id_type_pret");
      select.innerHTML = '<option value="">-- Sélectionner un type de prêt --</option>';
      types.forEach(t => {
        select.innerHTML += `<option value="${t.id}">${t.nom}</option>`;
      });
    });
  }

  function ajouterOuModifier() {
    const id = document.getElementById("id").value;
    const data = new URLSearchParams({
      id_client: document.getElementById("id_client").value,
      id_type_pret: document.getElementById("id_type_pret").value,
      montant_demande: document.getElementById("montant_demande").value,
      duree_demandee: document.getElementById("duree_demandee").value,
      date_expiration: document.getElementById("date_expiration").value
    }).toString();

    if (id) {
      ajax("PUT", `/simulations/${id}`, data, () => {
        resetForm();
        chargerSimulations();
      });
    } else {
      ajax("POST", "/simulations", data, () => {
        resetForm();
        chargerSimulations();
      });
    }
  }

  function remplirFormulaire(s) {
    document.getElementById("id").value = s.id;
    document.getElementById("id_client").value = s.id_client;
    document.getElementById("id_type_pret").value = s.id_type_pret;
    document.getElementById("montant_demande").value = s.montant_demande;
    document.getElementById("duree_demandee").value = s.duree_demandee;
    document.getElementById("date_expiration").value = s.date_expiration;
  }

  function supprimerSimulation(id) {
    if (confirm("Supprimer cette simulation ?")) {
      ajax("DELETE", `/simulations/${id}`, null, () => {
        chargerSimulations();
      });
    }
  }

  function resetForm() {
    document.getElementById("form-simulation").reset();
    document.getElementById("id").value = "";
  }

  chargerSelectClients();
  chargerSelectTypesPret();
  chargerSimulations();
</script>

<?php include('template_footer.php'); ?>
