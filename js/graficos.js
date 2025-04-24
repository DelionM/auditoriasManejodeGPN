// Function to update select element colors
function updateSelectColors() {
  const selects = document.querySelectorAll('.resultado');
  
  selects.forEach(select => {
      select.addEventListener('change', function() {
          updateColor(this);
      });
      // Apply initial color
      updateColor(select);
  });
}

function updateColor(select) {
  switch(select.value) {
      case 'OK':
          select.style.backgroundColor = '#28a745'; // Green
          select.style.color = 'white';
          break;
      case 'Pendiente':
          select.style.backgroundColor = '#ffc107'; // Yellow
          select.style.color = 'black';
          break;
      case 'NOK':
          select.style.backgroundColor = '#dc3545'; // Red
          select.style.color = 'white';
          break;
      default:
          select.style.backgroundColor = 'white';
          select.style.color = 'black';
  }
}

// Function to lock all input fields
function lockFields() {
  const inputs = document.querySelectorAll('input, select, textarea');
  inputs.forEach(input => {
      input.disabled = true;
  });
}

// Function to count statuses for a specific section
function countStatuses(sectionPrefix) {
  const statuses = {
      OK: 0,
      Pendiente: 0,
      NOK: 0
  };
  
  const selects = document.querySelectorAll(`[id^="${sectionPrefix}"]`);
  selects.forEach(select => {
      if (select.value === 'OK') statuses.OK++;
      else if (select.value === 'Pendiente') statuses.Pendiente++;
      else if (select.value === 'NOK') statuses.NOK++;
  });
  
  return statuses;
}

// Function to create or update a chart
function createOrUpdateChart(chartId, sectionPrefix) {
  const ctx = document.getElementById(chartId).getContext('2d');
  const statuses = countStatuses(sectionPrefix);
  
  // Check if chart exists and destroy it
  if (window[chartId] && typeof window[chartId].destroy === 'function') {
      window[chartId].destroy();
  }
  
  window[chartId] = new Chart(ctx, {
      type: 'doughnut',
      data: {
          labels: ['OK', 'Pendiente', 'NOK'],
          datasets: [{
              label: 'Estado de Auditoría',
              data: [statuses.OK, statuses.Pendiente, statuses.NOK],
              backgroundColor: [
                  '#28a745', // Green
                  '#ffc107', // Yellow
                  '#dc3545'  // Red
              ],
              borderColor: [
                  '#1d7d38',
                  '#cca107',
                  '#b52938'
              ],
              borderWidth: 1
          }]
      },
      options: {
          scales: {
              y: {
                  beginAtZero: true,
                  ticks: {
                      stepSize: 1
                  }
              }
          },
          plugins: {
              legend: {
                  display: false
              }
          }
      }
  });

  // Actualiza la leyenda debajo del gráfico
  const legendContainer = document.getElementById(`legend-${chartId}`);
  legendContainer.innerHTML = `
      <ul style="list-style:none; padding-left: 0;">
          <li style="color: #28a745;">OK: ${statuses.OK}</li>
          <li style="color: #ffc107;">Pendiente: ${statuses.Pendiente}</li>
          <li style="color: #dc3545;">NOK: ${statuses.NOK}</li>
      </ul>
  `;
}


// Initialize everything when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Update select colors initially
  updateSelectColors();
  
  // Lock all fields
  lockFields();
  
  // Create initial charts
  try {
      createOrUpdateChart('evaluationChart1', 'idResultado1');
      createOrUpdateChart('evaluationChart2', 'idResultado2');
      createOrUpdateChart('evaluationChart3', 'idResultado3');
      createOrUpdateChart('evaluationChart4', 'idResultado4');
      createOrUpdateChart('evaluationChart5', 'idResultado5');
      createOrUpdateChart('evaluationChart6', 'idResultado6');
  } catch (error) {
      console.error('Error creating charts:', error);
  }
  
  // Add event listeners to update buttons
  document.getElementById('update-chart1').addEventListener('click', () => createOrUpdateChart('evaluationChart1', 'idResultado1'));
  document.getElementById('update-chart2').addEventListener('click', () => createOrUpdateChart('evaluationChart2', 'idResultado2'));
  document.getElementById('update-chart3').addEventListener('click', () => createOrUpdateChart('evaluationChart3', 'idResultado3'));
  document.getElementById('update-chart4').addEventListener('click', () => createOrUpdateChart('evaluationChart4', 'idResultado4'));
  document.getElementById('update-chart5').addEventListener('click', () => createOrUpdateChart('evaluationChart5', 'idResultado5'));
  document.getElementById('update-chart6').addEventListener('click', () => createOrUpdateChart('evaluationChart6', 'idResultado6'));
});