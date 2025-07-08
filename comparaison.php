 <?php include('template_header.php'); ?>

<div class="container mt-5">
  <h1 class="mb-4">Gestion des simulations de prêt</h1>

  <!-- Formulaire -->
  <form id="form-simulation" class="row g-3 mb-4">
    <input type="hidden" id="id">
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
    <div class="col-12">
      <button type="button" class="btn btn-primary" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
      <button type="reset" class="btn btn-secondary" onclick="resetForm()">Réinitialiser</button>
    </div>
  </form>

  <!-- Bouton comparer -->
  <div class="mb-3">
    <button class="btn btn-outline-primary" onclick="comparerSimulations()">Comparer les simulations sélectionnées</button>
  </div>

  <!-- Table des simulations -->
  <div class="table-responsive">
    <table class="table table-bordered" id="table-simulations">
      <thead class="table-light">
        <tr>
          <th></th>
          <th>ID</th>
          <th>Client</th>
          <th>Type Prêt</th>
          <th>Montant</th>
          <th>Durée</th>
          <th>Date expiration</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- Comparaison -->
  <div id="comparaison-simulations" class="mt-4"></div>
</div>

<script>
const apiBase = "http://localhost/tp-flightphp-crud1/ws";

function ajax(method, url, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, apiBase + url, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status >= 200 && xhr.status < 300) {
        callback(JSON.parse(xhr.responseText));
      } else {
        alert("Erreur API : " + xhr.status);
      }
    }
  };
  xhr.send(data);
}

function chargerSimulations() {
  ajax("GET", "/simulations", null, (data) => {
    const tbody = document.querySelector("#table-simulations tbody");
    tbody.innerHTML = "";
    data.forEach(e => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td><input type="checkbox" class="compare-checkbox" data-id="${e.id}"></td>
        <td>${e.id}</td>
        <td>${e.id_client}</td>
        <td>${e.id_type_pret}</td>
        <td>${e.montant_demande}</td>
        <td>${e.duree_demandee}</td>
        <td>${e.date_expiration ? e.date_expiration.split('T')[0] : ''}</td>
        <td>
          <button onclick='remplirFormulaire(${JSON.stringify(e)})'>✏️</button>
          <button onclick='supprimerSimulation(${e.id})'>🗑️</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  });
}

function ajouterOuModifier() {
  const id = document.getElementById("id").value;
  const champs = ["id_client", "id_type_pret", "montant_demande", "duree_demandee", "date_expiration"];
  const data = champs.map(f => `${encodeURIComponent(f)}=${encodeURIComponent(document.getElementById(f).value)}`).join("&");
  const url = id ? `/simulations/${id}` : "/simulations";
  const method = id ? "PUT" : "POST";

  ajax(method, url, data, () => {
    resetForm();
    chargerSimulations();
  });
}

function remplirFormulaire(e) {
  document.getElementById("id").value = e.id;
  document.getElementById("id_client").value = e.id_client;
  document.getElementById("id_type_pret").value = e.id_type_pret;
  document.getElementById("montant_demande").value = e.montant_demande;
  document.getElementById("duree_demandee").value = e.duree_demandee;
  document.getElementById("date_expiration").value = e.date_expiration?.split('T')[0] || "";
}

function supprimerSimulation(id) {
  if (confirm("Supprimer cette simulation ?")) {
    ajax("DELETE", `/simulations/${id}`, null, () => {
      chargerSimulations();
    });
  }
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

function comparerSimulations() {
  const checkboxes = document.querySelectorAll(".compare-checkbox:checked");
  if (checkboxes.length < 2) {
    alert("Sélectionnez au moins 2 simulations à comparer.");
    return;
  }

  const ids = Array.from(checkboxes).map(cb => cb.dataset.id);
  const promises = ids.map(id => new Promise(resolve => {
    ajax("GET", `/simulations/${id}`, null, resolve);
  }));

  Promise.all(promises).then(simulations => {
    afficherComparaison(simulations);
  });
}

function afficherComparaison(simulations) {
  const container = document.getElementById("comparaison-simulations");
  let html = '<h4>Comparaison des simulations</h4>';
  html += '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Champ</th>';

  simulations.forEach((_, i) => {
    html += `<th>Simulation ${i + 1}</th>`;
  });

  html += '</tr></thead><tbody>';

  const champs = {
    id: "ID",
    numero_simulation: "N° Simulation",
    id_client: "Client",
    id_type_pret: "Type Prêt",
    montant_demande: "Montant demandé",
    duree_demandee: "Durée (mois)",
    taux_applique: "Taux appliqué",
    taux_assurance: "Taux assurance",
    mensualite_capital: "Mensualité capital",
    mensualite_assurance: "Mensualité assurance",
    mensualite_totale: "Mensualité totale",
    montant_total_assurance: "Total assurance",
    montant_total_pret: "Montant total prêt",
    frais_dossier: "Frais dossier",
    date_expiration: "Date expiration",
    statut: "Statut",
    notes: "Notes"
  };

  for (let key in champs) {
    html += `<tr><td><strong>${champs[key]}</strong></td>`;
    simulations.forEach(sim => {
      html += `<td>${sim[key] ?? ""}</td>`;
    });
    html += '</tr>';
  }

  html += '</tbody></table></div>';
  container.innerHTML = html;
}

function resetForm() {
  ["id", "id_client", "id_type_pret", "montant_demande", "duree_demandee", "date_expiration"].forEach(id => {
    document.getElementById(id).value = "";
  });
}

// Chargement initial
chargerSelectClients();
chargerSelectTypesPret();
chargerSimulations();
</script>


<?php include('template_footer.php'); ?>
