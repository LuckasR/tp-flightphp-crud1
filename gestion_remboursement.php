<?php include('template_header.php'); ?>
<h1 class="text-center text-primary my-4">Gestion des Remboursements (Annuité Constante)</h1>

<section id="form-section" class="container mb-5">
  <div class="row g-3">
    <div class="col-md-4">
      <label for="id_pret" class="form-label">Prêt</label>
      <select id="id_pret" class="form-select" onchange="chargerDetailsPret()">
        <option value="">-- Sélectionner un prêt --</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="id_admin" class="form-label">Administrateur</label>
      <select id="id_admin" class="form-select"></select>
    </div>

    <div class="col-md-4">
      <label for="date_valeur" class="form-label">Date de valeur</label>
      <input type="date" id="date_valeur" class="form-control">
    </div>

    <div class="col-md-4">
      <label for="delai_premier_remboursement" class="form-label">Délai avant premier remboursement (mois)</label>
      <input type="number" id="delai_premier_remboursement" class="form-control" min="0" value="0">
    </div>

    <div class="col-12 text-end mt-3">
      <button onclick="genererPlanRemboursement()" class="btn btn-success">Générer Plan de Remboursement</button>
    </div>
  </div>

  <div id="details-pret" class="mt-4 p-3 border border-secondary rounded bg-light"></div>
</section>

<section class="container">
  <h3 class="mb-3">Plan de Remboursement</h3>
  <div class="table-responsive">
    <table id="table-remboursements" class="table table-bordered table-hover table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Période</th>
          <th class="text-end">Annuité (Ar)</th>
          <th class="text-end">Intérêt (Ar)</th>
          <th class="text-end">Principal (Ar)</th>
          <th class="text-end">Capital Restant (Ar)</th>
          <th>Date Échéance</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="7" class="text-muted">Sélectionnez un prêt et générez le plan de remboursement.</td>
        </tr>
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
      console.log("Réponse brute de l'API:", xhr.responseText);
      if (xhr.status >= 200 && xhr.status < 300) {
        try {
          callback(JSON.parse(xhr.responseText));
        } catch (e) {
          alert("Réponse JSON invalide");
          console.error("Erreur JSON:", e, "Réponse:", xhr.responseText);
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
    console.log("Données reçues pour", endpoint, ":", data);
    const select = document.getElementById(id);
    select.innerHTML = `<option value="">-- Sélectionner --</option>`;
    if (!data || data.length === 0) {
      alert(`Aucune donnée disponible pour ${endpoint}`);
      return;
    }
    data.forEach(e => {
      const opt = document.createElement("option");
      opt.value = e.id;
      opt.textContent = endpoint === "prets" ? `${e.numero_pret} - ${Number(e.montant_accorde).toFixed(2)} Ar` : (e[labelKey] || e.nom || e.id);
      select.appendChild(opt);
    });
  });
}

function chargerDetailsPret() {
  const id_pret = document.getElementById("id_pret").value;
  const detailsDiv = document.getElementById("details-pret");
  if (!id_pret) {
    detailsDiv.innerHTML = "";
    return;
  }
  ajax("GET", `/prets/${id_pret}`, null, pret => {
    const montant_restant = pret.montant_restant != null ? Number(pret.montant_restant) : 
                           (pret.montant_total != null ? Number(pret.montant_total) : 
                           Number(pret.montant_accorde) + (Number(pret.montant_accorde) * (Number(pret.taux_applique) || 0) / 100) + (Number(pret.frais_dossier) || 0));
    detailsDiv.innerHTML = `
      <p><strong>Numéro du prêt :</strong> ${pret.numero_pret}</p>
      <p><strong>Montant accordé :</strong> ${Number(pret.montant_accorde).toFixed(2)} Ar</p>
      <p><strong>Taux d'intérêt annuel :</strong> ${pret.taux_applique || 0} %</p>
      <p><strong>Durée (mois) :</strong> ${pret.duree_accordee}</p>
      <p><strong>Montant remboursé :</strong> ${Number(pret.montant_rembourse || 0).toFixed(2)} Ar</p>
      <p><strong>Montant restant :</strong> ${montant_restant.toFixed(2)} Ar</p>
      <p><strong>Date première échéance :</strong> ${pret.date_premiere_echeance || 'N/A'}</p>
    `;
    document.getElementById("form-section").style.display = "block";
  });
}

function calculerAnnuité(montant, tauxAnnuel, dureeMois) {
  const tauxMensuel = tauxAnnuel / 100 / 12;
  const annuite = montant * (tauxMensuel / (1 - Math.pow(1 + tauxMensuel, -dureeMois)));
  return isFinite(annuite) ? annuite : 0;
}

function genererPlanRemboursement() {
  const id_pret = document.getElementById("id_pret").value;
  const id_admin = document.getElementById("id_admin").value;
  const date_valeur = document.getElementById("date_valeur").value;
  const delai_premier_remboursement = parseInt(document.getElementById("delai_premier_remboursement").value) || 0;

  if (!id_pret || !id_admin || !date_valeur) {
    alert("Veuillez remplir tous les champs obligatoires (Prêt, Administrateur, Date de valeur).");
    return;
  }
  ajax("GET", `/prets/${id_pret}`, null, pret => {
    const montant_restant = pret.montant_restant != null ? Number(pret.montant_restant) : 
                           (pret.montant_total != null ? Number(pret.montant_total) : 
                           Number(pret.montant_accorde) + (Number(pret.montant_accorde) * (Number(pret.taux_applique) || 0) / 100) + (Number(pret.frais_dossier) || 0));
    const tauxAnnuel = Number(pret.taux_applique || 0);
    let dureeMois = Number(pret.duree_accordee);
    const montantRembourse = Number(pret.montant_rembourse || 0);

    ajax("GET", `/paiements`, null, paiements => {
      const paiementsPret = paiements.filter(p => Number(p.id_pret) === Number(id_pret));
      const moisPayes = paiementsPret.length;
      dureeMois = Math.max(0, dureeMois - moisPayes);

      if (dureeMois === 0) {
        alert("Ce prêt est entièrement remboursé.");
        document.querySelector("#table-remboursements tbody").innerHTML = `<tr><td colspan="7">Aucun remboursement restant.</td></tr>`;
        return;
      }

      const annuite = calculerAnnuité(montant_restant, tauxAnnuel, dureeMois);
      let capitalRestant = montant_restant;
      const tbody = document.querySelector("#table-remboursements tbody");
      tbody.innerHTML = "";

      let dateEcheance = new Date(pret.date_premiere_echeance || date_valeur);
      // Appliquer le délai avant le premier remboursement
      dateEcheance.setMonth(dateEcheance.getMonth() + moisPayes + delai_premier_remboursement);

      for (let i = 1; i <= dureeMois; i++) {
        const interet = capitalRestant * (tauxAnnuel / 100 / 12);
        const principal = annuite - interet;
        capitalRestant -= principal;

        if (capitalRestant < 0) capitalRestant = 0;

        const dateEcheanceStr = dateEcheance.toISOString().split('T')[0];
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${i + moisPayes}</td>
          <td>${annuite.toFixed(2).replace('.', ',')}</td>
          <td>${interet.toFixed(2).replace('.', ',')}</td>
          <td>${principal.toFixed(2).replace('.', ',')}</td>
          <td>${capitalRestant.toFixed(2).replace('.', ',')}</td>
          <td>${dateEcheanceStr}</td>
          <td><button onclick="enregistrerPaiement(${id_pret}, ${id_admin}, ${annuite.toFixed(2)}, '${dateEcheanceStr}')">Enregistrer</button></td>
        `;
        tbody.appendChild(tr);

        dateEcheance.setMonth(dateEcheance.getMonth() + 1);
      }
    });
  });
}

function enregistrerPaiement(id_pret, id_admin, montant_paye, date_valeur) {
  console.log(`Enregistrer paiement: id_pret=${id_pret}, id_admin=${id_admin}, montant_paye=${montant_paye}, date_valeur=${date_valeur}`);
  const data = `id_pret=${id_pret}&id_admin=${id_admin}&montant_paye=${montant_paye}&date_valeur=${date_valeur}&commentaire=Annuité constante période`;
  ajax("POST", "/paiements", data, response => {
    if (response.success) {
      alert("Paiement enregistré avec succès !");
      chargerDetailsPret();
      genererPlanRemboursement();
    } else {
      alert("Erreur lors de l'enregistrement : " + (response.error || "Unknown error"));
    }
  });
}

// Chargement initial des selects
document.addEventListener("DOMContentLoaded", () => {
  remplirSelect("id_pret", "prets");
  remplirSelect("id_admin", "admins");
});
</script>

<?php include('template_footer.php'); ?>
