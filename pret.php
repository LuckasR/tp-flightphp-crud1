 
<?php include('template_header.php'); ?>
<h1 class="text-center my-4">Gestion des Prêts</h1>

<section class="container mb-5">
  <form id="form-pret" class="row g-3">
    <input type="hidden" id="id">
    <input type="hidden" id="numero_pret">

    <div class="col-md-6">
      <label for="id_client" class="form-label">Client</label>
      <select id="id_client" class="form-select" required></select>
    </div>

    <div class="col-md-6">
      <label for="id_type_pret" class="form-label">Type de prêt</label>
      <select id="id_type_pret" class="form-select" required></select>
    </div>

    <div class="col-md-6">
      <label for="id_admin_createur" class="form-label">Administrateur créateur</label>
      <select id="id_admin_createur" class="form-select" required></select>
    </div>

    <div class="col-md-6">
      <label for="montant_demande" class="form-label">Montant demandé</label>
      <input type="number" id="montant_demande" class="form-control" min="0" step="0.01" required>
    </div>

    <div class="col-md-6">
      <label for="duree_demandee" class="form-label">Durée demandée (mois)</label>
      <input type="number" id="duree_demandee" class="form-control" min="1" step="1" required>
    </div>

    <div class="col-12">
      <label for="motif_demande" class="form-label">Motif de la demande</label>
      <textarea id="motif_demande" class="form-control" rows="3" placeholder="Motif de la demande"></textarea>
    </div>

    <div class="col-12 text-end">
      <button type="button" onclick="ajouterOuModifier()" class="btn btn-primary">Ajouter / Modifier</button>
    </div>
  </form>
</section>
<a href="type_pret.php"> Ajout Type Pret </a>

<section class="container">
  <h3 class="mb-3">Liste des prêts </h3>
  <div class="table-responsive">
    <table id="table-prets" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Client</th>
          <th>Type</th>
          <th>Montant demandé</th>
          <th>Durée</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Les données seront ajoutées ici dynamiquement -->
      </tbody>
    </table>
  </div>
</section>


<script>
const api = "http://localhost/tp-flightphp-crud1/ws";

function ajax(method, url, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, api + url, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          callback(JSON.parse(xhr.responseText));
        } catch(e) {
          alert("Réponse JSON invalide");
          console.error(e);
        }
      } else {
        alert("Erreur : " + xhr.responseText);
      }
    }
  };
  xhr.send(data);
}

function remplirSelect(id, endpoint, labelKey = 'nom') {
  ajax("GET", `/${endpoint}`, null, data => {
    const select = document.getElementById(id);
    select.innerHTML = `<option value="">-- Sélectionner --</option>`;
    data.forEach(e => {
      const opt = document.createElement("option");
      opt.value = e.id;
      opt.textContent = e[labelKey] || e.nom || e.email || e.id; // fallback si labelKey absent
      select.appendChild(opt);
    });
  });
}

function getFormData() {
  const numero_pret = document.getElementById("numero_pret").value;
  const id_client = document.getElementById("id_client").value;
  const id_type_pret = document.getElementById("id_type_pret").value;
  const id_admin_createur = document.getElementById("id_admin_createur").value;
  const montant_demande = document.getElementById("montant_demande").value;
  const duree_demandee = document.getElementById("duree_demandee").value;
  const motif_demande = document.getElementById("motif_demande").value;

  return `numero_pret=${encodeURIComponent(numero_pret)}&id_client=${encodeURIComponent(id_client)}&id_type_pret=${encodeURIComponent(id_type_pret)}&id_admin_createur=${encodeURIComponent(id_admin_createur)}&montant_demande=${encodeURIComponent(montant_demande)}&duree_demandee=${encodeURIComponent(duree_demandee)}&motif_demande=${encodeURIComponent(motif_demande)}`;
}

function ajouterOuModifier() {
  const id = document.getElementById("id").value;
  const id_client = document.getElementById("id_client").value;
  const id_type_pret = document.getElementById("id_type_pret").value;
  const montant_demande = document.getElementById("montant_demande").value;

  if (!id_client || !id_type_pret || !montant_demande) {
    alert("Veuillez remplir tous les champs obligatoires (Client, Type de prêt, Montant).");
    return;
  }

  const data = getFormData();

  if (id) {
    ajax("PUT", `/prets/${id}`, data, () => {
      resetForm();
      chargerPrets();
    });
  } else {
    ajax("POST", "/prets", data, () => {
      resetForm();
      chargerPrets();
    });
  }
}

function chargerPrets() {
  ajax("GET", "/prets", null, data => {
    const tbody = document.querySelector("#table-prets tbody");
    tbody.innerHTML = "";
    data.forEach(p => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.id}</td>
        <td>${p.nom_client || p.id_client}</td>
        <td>${p.nom_type_pret || p.id_type_pret}</td>
        <td>${p.montant_demande}</td>
        <td>${p.duree_demandee} mois</td>
        <td>
          <button onclick='remplir(${JSON.stringify(p)})'>✏️</button>
          <button onclick='supprimer(${p.id})'>🗑️</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  });
}

function remplir(p) {
  document.getElementById("id").value = p.id;
  document.getElementById("numero_pret").value = p.numero_pret || "";
  document.getElementById("id_client").value = p.id_client || "";
  document.getElementById("id_type_pret").value = p.id_type_pret || "";
  document.getElementById("id_admin_createur").value = p.id_admin_createur || "";
  document.getElementById("montant_demande").value = p.montant_demande || "";
  document.getElementById("duree_demandee").value = p.duree_demandee || "";
  document.getElementById("motif_demande").value = p.motif_demande || "";
}

function supprimer(id) {
  if (confirm("Supprimer ce prêt ?")) {
    ajax("DELETE", `/prets/${id}`, null, () => chargerPrets());
  }
}

function resetForm() {
  document.getElementById("id").value = "";
  document.getElementById("numero_pret").value = "";
  document.getElementById("id_client").value = "";
  document.getElementById("id_type_pret").value = "";
  document.getElementById("id_admin_createur").value = "";
  document.getElementById("montant_demande").value = "";
  document.getElementById("duree_demandee").value = "";
  document.getElementById("motif_demande").value = "";
}

// Chargement initial des selects et table
remplirSelect("id_client", "clients", "nom");
remplirSelect("id_type_pret", "typeprets", "nom");
remplirSelect("id_admin_createur", "admins", "nom");
chargerPrets();

</script>
 
<?php include('template_footer.php'); ?>
