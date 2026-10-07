document.addEventListener('DOMContentLoaded', function() {
    var headers = document.querySelectorAll('th[data-columna]');
    
    headers.forEach(function(header) {
        header.style.cursor = 'pointer';
        
        header.addEventListener('click', function() {
            var columna = this.getAttribute('data-columna');
            
            var urlParams = new URLSearchParams(window.location.search);
            
            urlParams.set('ordenar', columna);
            
            var nuevaURL = window.location.pathname + '?' + urlParams.toString();
            
            window.location.href = nuevaURL;
        });
    });
});

