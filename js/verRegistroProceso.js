// verRegistroProceso.js

document.addEventListener('DOMContentLoaded', function() {
    // Get all status dropdowns with class 'resultado'
    const statusDropdowns = document.querySelectorAll('.resultado');

    // Loop through each dropdown
    statusDropdowns.forEach(dropdown => {
        // Get the current value
        const value = dropdown.value;

        // Apply styles based on the value
        switch(value) {
            case 'OK':
                dropdown.style.backgroundColor = '#28a745'; // Green
                dropdown.style.color = 'white';
                break;
            case 'NOK':
                dropdown.style.backgroundColor = '#dc3545'; // Red
                dropdown.style.color = 'white';
                break;
            case '': // Empty or No Aplica
            case 'Pendiente':
                dropdown.style.backgroundColor = ''; // Default (white)
                dropdown.style.color = 'black';
                break;
            default:
                dropdown.style.backgroundColor = ''; // Default (white)
                dropdown.style.color = 'black';
        }
    });
});