<?php include('template_header.php'); ?>

<h1 class="text-center text-primary my-4">Rapport des Intérêts Mensuels</h1>

<div class="container mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <label for="annee_debut" class="form-label">Année Début</label>
      <input type="number" id="annee_debut" class="form-control" value="2025" min="2000" max="2030">
    </div>

    <div class="col-md-3">
      <label for="mois_debut" class="form-label">Mois Début</label>
      <select id="mois_debut" class="form-select">
        <?php
        for ($i = 1; $i <= 12; $i++) {
          $mois = DateTime::createFromFormat('!m', $i)->format('F');
          echo "<option value='$i'>$mois</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-md-3">
      <label for="annee_fin" class="form-label">Année Fin</label>
      <input type="number" id="annee_fin" class="form-control" value="2025" min="2000" max="2030">
    </div>

    <div class="col-md-3">
      <label for="mois_fin" class="form-label">Mois Fin</label>
      <select id="mois_fin" class="form-select">
        <?php
        for ($i = 1; $i <= 12; $i++) {
          $mois = DateTime::createFromFormat('!m', $i)->format('F');
          echo "<option value='$i'>$mois</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-12 text-end mt-3">
      <button onclick="fetchInterestData()" class="btn btn-primary">Afficher</button>
    </div>
  </div>
</div>

<div class="container mb-5">
  <h4 class="mb-3">Tableau des Intérêts</h4>
  <div class="table-responsive">
    <table id="interestTable" class="table table-bordered table-striped text-center">
      <thead class="table-dark">
        <tr>
          <th>Année</th>
          <th>Mois</th>
          <th>Mois et Année</th>
          <th>Intérêts Mensuels (€)</th>
        </tr>
      </thead>
      <tbody id="interestTableBody">
        <tr><td colspan="4" class="text-muted">Aucun résultat pour l'instant.</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="container mb-5">
  <h4 class="mb-3">Graphique</h4>
  <canvas id="interestChart" height="100"></canvas>
</div>

<!-- 📌 Inclusion d’Axios et Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const apiBase = "http://localhost/tp-flightphp-crud1/ws";
  let chartInstance = null;

  async function fetchInterestData() {
    const annee_debut = document.getElementById('annee_debut').value;
    const mois_debut = document.getElementById('mois_debut').value;
    const annee_fin = document.getElementById('annee_fin').value;
    const mois_fin = document.getElementById('mois_fin').value;

    try {
      const response = await axios.post(`${apiBase}/etablissements/monthly_interest`, {
        annee_debut: parseInt(annee_debut),
        mois_debut: parseInt(mois_debut),
        annee_fin: parseInt(annee_fin),
        mois_fin: parseInt(mois_fin)
      }, {
        headers: {
          'Content-Type': 'application/json'
        }
      });

      const data = response.data;
      const tableBody = document.getElementById('interestTableBody');
      tableBody.innerHTML = '';

      if (data.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="4" class="text-muted">Aucun intérêt trouvé.</td></tr>`;
      } else {
        data.forEach(row => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${row.annee}</td>
            <td>${row.mois}</td>
            <td>${row.mois_annee}</td>
            <td>${row.interet_mensuel}</td>
          `;
          tableBody.appendChild(tr);
        });
      }

      // Graphique
      const ctx = document.getElementById('interestChart').getContext('2d');
      if (chartInstance) chartInstance.destroy();
      chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(r => r.mois_annee),
          datasets: [{
            label: 'Intérêts Mensuels (€)',
            data: data.map(r => r.interet_mensuel),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          scales: {
            x: { title: { display: true, text: 'Mois et Année' } },
            y: { beginAtZero: true, title: { display: true, text: 'Intérêts (€)' } }
          }
        }
      });
    } catch (error) {
      console.error("Erreur:", error);
      alert("Erreur lors de la récupération des données: " + (error.response?.data?.error || error.message));
    }
  }
</script>

<?php include('template_footer.php'); ?>
