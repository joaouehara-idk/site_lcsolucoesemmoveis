document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        img.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });
        
        img.addEventListener('dragstart', (e) => {
            e.preventDefault();
            return false;
        });
        
        img.addEventListener('touchstart', function(e) {
            if(e.touches.length > 1) {
                e.preventDefault();
            }
        }, false);
    });

    document.addEventListener('keydown', function(e) {
        if(e.ctrlKey && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            alert('⚠️ Download de conteúdo protegido não é permitido.');
            return false;
        }
    });

    document.addEventListener('selectstart', function(e) {
        if(e.target.tagName === 'IMG') {
            e.preventDefault();
        }
    });
});
