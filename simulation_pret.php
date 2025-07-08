 <?php include('template_header.php'); ?>

  <div class="container">
    <h1 class="mb-4">Gestion des simulations de prêt</h1>

    <div class="row g-3">
      <input type="hidden" id="id">
  <a  href="comparaison.php">Simulation Comparaison</a>

      <div class="col-md-4"><input style=" display: none; " class="form-control" type="text" id="numero_simulation" placeholder="Numéro simulation" value="0"></div>
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
      <div class="col-md-4"><input class="form-control" type="number" step="0.01" id="montant_demande" placeholder="Montant demandé"></div>
      <div class="col-md-4"><input class="form-control" type="number" id="duree_demandee" placeholder="Durée (mois)"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="taux_applique" placeholder="Taux appliqué (%)" value="0"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="taux_assurance" placeholder="Taux assurance (%)" value="0"></div>

      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="mensualite_capital" placeholder="Mensualité capital" value="0"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="mensualite_assurance" placeholder="Mensualité assurance" value="0"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="mensualite_totale" placeholder="Mensualité totale" value="0"></div>

      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="montant_total_assurance" placeholder="Montant total assurance" value="0"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="montant_total_pret" placeholder="Montant total prêt" value="0"></div>
      <div  style=" display: none; " class="col-md-4"><input class="form-control" type="number" step="0.01" id="frais_dossier" placeholder="Frais dossier" value="0"></div>

      <div class="col-md-4"><input class="form-control" type="date" id="date_expiration" placeholder="Date expiration"></div>
      <div class="col-md-4" >
        <select class="form-select" id="statut">
          <option value="active">Active</option>
          <option value="convertie">Convertie</option>
          <option value="expiree">Expirée</option>
        </select>
      </div>
      <div class="col-md-8"><input class="form-control" type="text" id="notes" placeholder="Notes"></div>

      <div class="col-12">
        <button class="btn btn-primary" onclick="ajouterOuModifier()">Ajouter / Modifier</button>
        <button class="btn btn-secondary" onclick="resetForm()">Réinitialiser</button>
      </div>
    </div>

    <hr class="my-4">

    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="table-simulations">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Numéro</th>
            <th>ID Client</th>
            <th>ID Type</th>
            <th>Montant</th>
            <th>Durée</th>
            <th>Taux</th>
            <th>Assurance</th>
            <th>Mensualité</th>
            <th>Ass. Mens.</th>
            <th>Totale</th>
            <th>Total Ass.</th>
            <th>Total Prêt</th>
            <th>Frais</th>
            <th>Expiration</th>
            <th>Statut</th>
            <th>Notes</th>
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
            <td>${e.id}</td>
            <td>${e.numero_simulation}</td>
            <td>${e.id_client}</td>
            <td>${e.id_type_pret}</td>
            <td>${e.montant_demande}</td>
            <td>${e.duree_demandee}</td>
            <td>${e.taux_applique}</td>
            <td>${e.taux_assurance}</td>
            <td>${e.mensualite_capital}</td>
            <td>${e.mensualite_assurance}</td>
            <td>${e.mensualite_totale}</td>
            <td>${e.montant_total_assurance}</td>
            <td>${e.montant_total_pret}</td>
            <td>${e.frais_dossier}</td>
            <td>${e.date_expiration ? e.date_expiration.split('T')[0] : ''}</td>
            <td>${e.statut}</td>
            <td>${e.notes || ''}</td>
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
      const fields = [
        "numero_simulation", "id_client", "id_type_pret",
        "montant_demande", "duree_demandee", "taux_applique", "taux_assurance",
        "mensualite_capital", "mensualite_assurance", "mensualite_totale",
        "montant_total_assurance", "montant_total_pret", "frais_dossier",
        "date_expiration", "statut", "notes"
      ];

      const data = fields.map(f => {
        const val = document.getElementById(f).value;
        return encodeURIComponent(f) + "=" + encodeURIComponent(val);
      }).join("&");

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


    function remplirFormulaire(e) {
      const fields = [
        "id", "numero_simulation", "id_client", "id_type_pret",
        "montant_demande", "duree_demandee", "taux_applique", "taux_assurance",
        "mensualite_capital", "mensualite_assurance", "mensualite_totale",
        "montant_total_assurance", "montant_total_pret", "frais_dossier",
        "date_expiration", "statut", "notes"
      ];
      fields.forEach(f => {
        document.getElementById(f).value = e[f] || "";
      });
    }

    function supprimerSimulation(id) {
      if (confirm("Supprimer cette simulation ?")) {
        ajax("DELETE", `/simulations/${id}`, null, () => {
          chargerSimulations();
        });
      }
    }

    function resetForm() {
      const fields = [
        "id", "numero_simulation", "id_client", "id_type_pret",
        "montant_demande", "duree_demandee", "taux_applique", "taux_assurance",
        "mensualite_capital", "mensualite_assurance", "mensualite_totale",
        "montant_total_assurance", "montant_total_pret", "frais_dossier",
        "date_expiration", "statut", "notes"
      ];
      fields.forEach(f => {
        document.getElementById(f).value = "";
      });
      document.getElementById("statut").value = "active";
    }

    // Chargement initial des données
    
  chargerSelectClients();
  chargerSelectTypesPret();
    chargerSimulations();
  </script>

 

<?php include('template_footer.php'); ?>
