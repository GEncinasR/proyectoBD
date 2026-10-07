document.addEventListener('DOMContentLoaded', function() {
    const fechaInicioInput = document.getElementById('coach_fechaInicio');
    
    if (fechaInicioInput) {
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
        
        fechaInicioInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const fechaConvertida = convertirFechaDDMMYYYY(pastedText);
            if (fechaConvertida) {
                this.value = fechaConvertida;
            }
        });
        
        fechaInicioInput.addEventListener('blur', function() {
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

