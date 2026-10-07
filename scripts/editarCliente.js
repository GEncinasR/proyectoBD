function actualizarFechaVencimiento() {
    const tipoMembresia = document.getElementById('cliente_membresia').value;
    const fechaInput = document.getElementById('cliente_fechaVencimiento');
    
    if (tipoMembresia === 'PROVISIONAL' || tipoMembresia === 'VENCIDA') {
        fechaInput.value = '';
        fechaInput.disabled = true;
    } else {
        fechaInput.disabled = false;
        if (!fechaInput.value) {
            const hoy = new Date();
            let fechaVencimiento = new Date();
            
            switch(tipoMembresia) {
                case 'DIA_PRUEBA':
                    fechaVencimiento.setDate(hoy.getDate() + 1);
                    break;
                case 'SEMANAL':
                    fechaVencimiento.setDate(hoy.getDate() + 7);
                    break;
                case 'MENSUALIDAD':
                    fechaVencimiento.setMonth(hoy.getMonth() + 1);
                    break;
                case 'ANUALIDAD':
                    fechaVencimiento.setFullYear(hoy.getFullYear() + 1);
                    break;
            }
            
            if (tipoMembresia !== 'PROVISIONAL' && tipoMembresia !== 'VENCIDA') {
                fechaInput.value = fechaVencimiento.toISOString().split('T')[0];
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarFechaVencimiento();
});

