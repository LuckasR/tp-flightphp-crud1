<?php include('template_header.php'); ?>
<h1 class="text-center my-5 text-primary">📋 Prêts en cours</h1>
<a href="prets_non_valides.php"> Prets non valider </a>

<div class="container">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center">
          <thead class="table-primary">
            <tr class="fw-bold text-uppercase">
              <th>#</th>
              <th>Numéro</th>
              <th>Client</th>
              <th>Montant (Ar) </th>
              <th>Durée (mois) </th>
              <th>Motif</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="liste-prets">
            <tr>
              <td colspan="7" class="text-muted">Chargement en cours ou aucun prêt à valider.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
const api = "http://localhost/tp-flightphp-crud1/ws";

// Requête AJAX
function ajax(method, url, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, api + url, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        callback(JSON.parse(xhr.responseText));
      } else {
        alert("Erreur : " + xhr.responseText);
      }
    }
  };
  xhr.send(data);
}

// Charger les prêts non validés
function chargerPretsNonValides() {
  ajax("GET", "/prets/valides", null, data => {
    const tbody = document.getElementById("liste-prets");
    tbody.innerHTML = "";

    if (!Array.isArray(data)) {
      alert("Format de données invalide !");
      return;
    }

    data.forEach(p => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.id}</td>
        <td>${p.numero_pret}</td>
        <td>${p.id_client}</td>
        <td>${p.montant_demande}</td>
        <td>${p.duree_demandee} mois</td>
        <td>${p.motif_demande || ""}</td>
        <td>
        <a href="/tp-flightphp-crud1/ws/pret/${p.id}/pdf">✅ Generer pdf</a>
        </td>

      `;
      tbody.appendChild(tr);
    });
  });
}

// Action de validation
function valider(id) {
  if (confirm("Valider ce prêt ?")) {
    ajax("PUT", `/prets/valider/${id}`, "", () => {
      alert("Prêt validé !");
      chargerPretsNonValides();
    });
  }
}

// Action de rejet
function rejeter(id) {
  if (confirm("Rejeter ce prêt ?")) {
    const raison = prompt("Raison du rejet :");
    if (raison) {
      ajax("PUT", `/prets/rejeter/${id}`, `raison_rejet=${encodeURIComponent(raison)}`, () => {
        alert("Prêt rejeté !");
        chargerPretsNonValides();
      });
    }
  }
}

// Lancer au chargement
chargerPretsNonValides();
</script>


<?php include('template_footer.php'); ?>
