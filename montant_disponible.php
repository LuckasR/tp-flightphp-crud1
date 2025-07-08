<?php include('template_header.php'); ?>

<h1 class="text-center text-primary my-4">Montant Disponible par Mois</h1>

<div class="container mb-4">
  <form class="row g-3 align-items-end" id="filter-form">
    <div class="col-md-3">
      <label for="mois_debut" class="form-label">Mois Début</label>
      <select id="mois_debut" class="form-select" required>
        <option value="">Sélectionner</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="annee_debut" class="form-label">Année Début</label>
      <input type="number" id="annee_debut" class="form-control" placeholder="YYYY" required>
    </div>
    <div class="col-md-3">
      <label for="mois_fin" class="form-label">Mois Fin</label>
      <select id="mois_fin" class="form-select" required>
        <option value="">Sélectionner</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="annee_fin" class="form-label">Année Fin</label>
      <input type="number" id="annee_fin" class="form-control" placeholder="YYYY" required>
    </div>
    <div class="col-12 text-end">
      <button type="button" class="btn btn-primary" onclick="chargerMontants()">Filtrer</button>
    </div>
    <div class="col-12">
      <div id="error-message" class="text-danger fw-bold"></div>
    </div>
  </form>
</div>

<div class="container mb-5">
  <h4 class="mb-3">Montants Disponibles</h4>
  <div class="table-responsive">
    <table id="table-montants" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Année</th>
          <th>Mois</th>
          <th>Montant Non Emprunté</th>
          <th>Remboursements Clients</th>
          <th>Montant Total Disponible</th>
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
      console.log("Statut:", xhr.status); // Debug
      console.log("Réponse:", xhr.responseText); // Debug
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

function chargerMontants() {
  const moisDebut = document.getElementById("mois_debut").value;
  const anneeDebut = document.getElementById("annee_debut").value;
  const moisFin = document.getElementById("mois_fin").value;
  const anneeFin = document.getElementById("annee_fin").value;
  const errorMessage = document.getElementById("error-message");

  if (!moisDebut || !anneeDebut || !moisFin || !anneeFin) {
    errorMessage.textContent = "Veuillez remplir tous les champs de filtre.";
    return;
  }

  const dateDebut = `${anneeDebut}-${moisDebut}-01`;
  const dateFin = `${anneeFin}-${moisFin}-31`;
  if (new Date(dateDebut) > new Date(dateFin)) {
    errorMessage.textContent = "La date de début doit être antérieure à la date de fin.";
    return;
  }

  errorMessage.textContent = "";
  ajax("GET", `/montantsDisponibles?mois_debut=${moisDebut}&annee_debut=${anneeDebut}&mois_fin=${moisFin}&annee_fin=${anneeFin}`, null, (data) => {
    console.log("Données reçues:", data); // Debug
    const tbody = document.querySelector("#table-montants tbody");
    tbody.innerHTML = "";
    data.forEach(m => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${m.annee}</td>
        <td>${m.mois}</td>
        <td>${m.montant_non_emprunte}</td>
        <td>${m.remboursements}</td>
        <td>${m.montant_total}</td>
      `;
      tbody.appendChild(tr);
    });
  }, (error) => {
    console.error("Erreur AJAX:", error); // Debug
    errorMessage.textContent = error;
  });
}
</script>

<?php include('template_footer.php'); ?>