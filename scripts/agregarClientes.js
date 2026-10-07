function actualizarPreview() {
    const tipo = document.getElementById('cliente_membresia').value;
    const preview = document.getElementById('preview_fecha');
    
    if (!tipo) {
        preview.textContent = '';
        return;
    }
    
    const hoy = new Date();
    let fechaVencimiento = new Date();
    
    switch(tipo) {
        case 'DIA_PRUEBA':
            fechaVencimiento.setDate(hoy.getDate() + 1);
            preview.textContent = 'Fecha de vencimiento calculada: ' + fechaVencimiento.toISOString().split('T')[0];
            break;
        case 'SEMANAL':
            fechaVencimiento.setDate(hoy.getDate() + 7);
            preview.textContent = 'Fecha de vencimiento calculada: ' + fechaVencimiento.toISOString().split('T')[0];
            break;
        case 'MENSUALIDAD':
            fechaVencimiento.setMonth(hoy.getMonth() + 1);
            preview.textContent = 'Fecha de vencimiento calculada: ' + fechaVencimiento.toISOString().split('T')[0];
            break;
        case 'ANUALIDAD':
            fechaVencimiento.setFullYear(hoy.getFullYear() + 1);
            preview.textContent = 'Fecha de vencimiento calculada: ' + fechaVencimiento.toISOString().split('T')[0];
            break;
        case 'PROVISIONAL':
            preview.textContent = 'Sin fecha de vencimiento (Provisional)';
            break;
        default:
            preview.textContent = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const fechaNacInput = document.getElementById('cliente_fechaNac');
    
    if (fechaNacInput) {
        function convertirFechaDDMMYYYY(texto) {
            texto = texto.trim();
            
            if (/^\d{4}-\d{2}-\d{2}$/.test(texto)) {
                return texto;
            }
            
            let match = texto.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
            if (match) {
                const dia = match[1].padStart(2, '0');
                const mes = match[2].padStart(2, '0');
                const año = match[3];
                return `${año}-${mes}-${dia}`;
            }
            
            match = texto.match(/^(\d{2})(\d{2})(\d{4})$/);
            if (match) {
                const dia = match[1];
                const mes = match[2];
                const año = match[3];
                return `${año}-${mes}-${dia}`;
            }
            
            return null;
        }
        
        fechaNacInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const fechaConvertida = convertirFechaDDMMYYYY(pastedText);
            if (fechaConvertida) {
                this.value = fechaConvertida;
            }
        });
        
        fechaNacInput.addEventListener('blur', function() {
            const valor = this.value.trim();
            if (valor && !/^\d{4}-\d{2}-\d{2}$/.test(valor)) {
                const fechaConvertida = convertirFechaDDMMYYYY(valor);
                if (fechaConvertida) {
                    this.value = fechaConvertida;
                }
            }
        });
    }
});

