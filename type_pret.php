<?php include('template_header.php'); ?>

<h1 class="text-center my-4">Gestion des Types de Prêt</h1>

<form id="form-pret" class="container mb-5">
  <input type="hidden" id="id">

  <!-- Identification -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Identification</h2>
    <div class="mb-3">
      <label for="nom" class="form-label">Nom du prêt</label>
      <input type="text" id="nom" class="form-control" placeholder="Nom du type de prêt" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" class="form-control" placeholder="Description" rows="3"></textarea>
    </div>
  </section>

  <!-- Critères d’éligibilité -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Critères d’éligibilité</h2>
    <div class="row g-3">
      <div class="col-md-4">
        <label for="revenu_minimum" class="form-label">Revenu minimum</label>
        <input type="number" id="revenu_minimum" class="form-control" placeholder="Revenu minimum">
      </div>
      <div class="col-md-4">
        <label for="age_minimum" class="form-label">Âge minimum</label>
        <input type="number" id="age_minimum" class="form-control" placeholder="Âge minimum">
      </div>
      <div class="col-md-4">
        <label for="age_maximum" class="form-label">Âge maximum</label>
        <input type="number" id="age_maximum" class="form-control" placeholder="Âge maximum">
      </div>
    </div>
  </section>

  <!-- Montants -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Montants</h2>
    <div class="row g-3">
      <div class="col-md-6">
        <label for="montant_min" class="form-label">Montant minimum</label>
        <input type="number" id="montant_min" class="form-control" placeholder="Montant minimum">
      </div>
      <div class="col-md-6">
        <label for="montant_max" class="form-label">Montant maximum</label>
        <input type="number" id="montant_max" class="form-control" placeholder="Montant maximum">
      </div>
    </div>
  </section>

  <!-- Durées -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Durées</h2>
    <div class="row g-3">
      <div class="col-md-6">
        <label for="duree_min" class="form-label">Durée minimum (mois)</label>
        <input type="number" id="duree_min" class="form-control" placeholder="Durée minimum">
      </div>
      <div class="col-md-6">
        <label for="duree_max" class="form-label">Durée maximum (mois)</label>
        <input type="number" id="duree_max" class="form-control" placeholder="Durée maximum">
      </div>
    </div>
  </section>

  <!-- Taux & Frais -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Taux & Frais</h2>
    <div class="row g-3">
      <div class="col-md-4">
        <label for="taux_interet" class="form-label">Taux d’intérêt (%)</label>
        <input type="number" id="taux_interet" class="form-control" placeholder="Taux intérêt" step="0.01">
      </div>
      <div class="col-md-4">
        <label for="taux_interet_retard" class="form-label">Taux de retard (%)</label>
        <input type="number" id="taux_interet_retard" class="form-control" placeholder="Taux intérêt de retard" step="0.01">
      </div>
      <div class="col-md-4">
        <label for="frais_dossier_fixe" class="form-label">Frais de dossier</label>
        <input type="number" id="frais_dossier_fixe" class="form-control" placeholder="Frais dossier fixe" step="0.01">
      </div>
    </div>
  </section>

  <!-- Documents requis -->
  <section class="mb-4">
    <h2 class="h5 mb-3">Documents requis</h2>
    <div class="mb-3">
      <label for="documents_requis" class="form-label">Liste des documents</label>
      <textarea id="documents_requis" class="form-control" placeholder="Exemple: CIN, fiche de paie, justificatif de domicile" rows="3"></textarea>
    </div>
    <div class="form-check">
      <input type="checkbox" id="actif" class="form-check-input" checked>
      <label for="actif" class="form-check-label">Actif</label>
    </div>
  </section>

  <!-- Bouton -->
  <section class="mb-5 text-end">
    <button type="button" onclick="ajouterOuModifier()" class="btn btn-primary">Ajouter / Modifier</button>
  </section>
</form>

<section class="container">
  <h2 class="mb-3">Liste des types de prêt</h2>
  <div class="table-responsive">
    <table id="table-typeprets" class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Nom</th>
          <th>Montant</th>
          <th>Durée</th>
          <th>Taux</th>
          <th>Actif</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Les données seront insérées ici dynamiquement -->
      </tbody>
    </table>
  </div>
</section>


  <!-- SCRIPT JS -->
  <script>
    const apiBase = "http://localhost/tp-flightphp-crud1/ws";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) callback(JSON.parse(xhr.responseText));
          else alert("Erreur serveur : " + xhr.responseText);
        }
      };
      xhr.send(data);
    }

    function chargerTypePrets() {
      ajax("GET", "/typeprets", null, (data) => {
        const tbody = document.querySelector("#table-typeprets tbody");
        tbody.innerHTML = "";
        data.forEach(tp => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${tp.nom}</td>
            <td>${tp.montant_min} - ${tp.montant_max}</td>
            <td>${tp.duree_min} - ${tp.duree_max} mois</td>
            <td>${tp.taux_interet}%</td>
            <td>${tp.actif ? '✅' : '❌'}</td>
            <td class="actions">
              <button onclick='remplir(${JSON.stringify(tp)})'>✏️</button>
              <button onclick='supprimer(${tp.id})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function getDataFromForm() {
      return `nom=${encodeURIComponent(nom.value)}&description=${encodeURIComponent(description.value)}&revenu_minimum=${revenu_minimum.value}&age_minimum=${age_minimum.value}&age_maximum=${age_maximum.value}&montant_min=${montant_min.value}&montant_max=${montant_max.value}&duree_min=${duree_min.value}&duree_max=${duree_max.value}&taux_interet=${taux_interet.value}&taux_interet_retard=${taux_interet_retard.value}&frais_dossier_fixe=${frais_dossier_fixe.value}&documents_requis=${encodeURIComponent(documents_requis.value)}&actif=${actif.checked ? 1 : 0}`;
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const data = getDataFromForm();

      if (id) {
        ajax("PUT", `/typeprets/${id}`, data, () => {
          resetForm(); chargerTypePrets();
        });
      } else {
        ajax("POST", `/typeprets`, data, () => {
          resetForm(); chargerTypePrets();
        });
      }
    }

    function remplir(tp) {
      id.value = tp.id;
      nom.value = tp.nom;
      description.value = tp.description || "";
      revenu_minimum.value = tp.revenu_minimum;
      age_minimum.value = tp.age_minimum;
      age_maximum.value = tp.age_maximum;
      montant_min.value = tp.montant_min;
      montant_max.value = tp.montant_max;
      duree_min.value = tp.duree_min;
      duree_max.value = tp.duree_max;
      taux_interet.value = tp.taux_interet;
      taux_interet_retard.value = tp.taux_interet_retard;
      frais_dossier_fixe.value = tp.frais_dossier_fixe;
      documents_requis.value = tp.documents_requis || "";
      actif.checked = tp.actif;
    }

    function supprimer(id) {
      if (confirm("Supprimer ce type de prêt ?")) {
        ajax("DELETE", `/typeprets/${id}`, null, () => chargerTypePrets());
      }
    }   

    function resetForm() {
      document.querySelectorAll("input, textarea").forEach(el => {
        if (el.type === "checkbox") el.checked = true;
        else el.value = "";
      });
    }

    // Initialisation
    chargerTypePrets();
  </script>


<?php include('template_footer.php'); ?>
