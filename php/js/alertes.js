document.addEventListener('DOMContentLoaded', function() {
    
    if (window.phpMessage !== "") {
        const missatgeNet = decodeURIComponent(window.phpMessage);
        alert(missatgeNet);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});