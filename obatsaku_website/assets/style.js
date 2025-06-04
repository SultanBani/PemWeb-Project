document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.postBerita');

    galleryItems.forEach(item => {
        const moreLink = item.querySelector('.more-link');
        
        moreLink.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah default action dari link
            const imageUrl = item.querySelector('img').src; // Ambil URL gambar
            
            // Menampilkan gambar dalam mode full-screen atau popup
            const fullScreenImage = document.createElement('div');
            fullScreenImage.classList.add('full-screen-image');
            fullScreenImage.innerHTML = `<img src="${imageUrl}" alt="Full Image" />`;
            document.body.appendChild(fullScreenImage);

            // Menambahkan tombol close pada gambar fullscreen
            const closeButton = document.createElement('button');
            closeButton.innerText = 'Close';
            closeButton.classList.add('close-button');
            fullScreenImage.appendChild(closeButton);
            
            // Fungsi untuk menutup gambar fullscreen
            closeButton.addEventListener('click', function() {
                document.body.removeChild(fullScreenImage);
            });

            // Fitur geser menggunakan drag (mouse atau touch)
            let isDragging = false;
            let startX, scrollLeft;

            const img = fullScreenImage.querySelector('img');

            img.addEventListener('mousedown', (e) => {
                isDragging = true;
                startX = e.pageX - img.offsetLeft;
                scrollLeft = img.scrollLeft;
                img.style.cursor = 'grabbing';
            });

            img.addEventListener('mouseleave', () => {
                isDragging = false;
                img.style.cursor = 'grab';
            });

            img.addEventListener('mouseup', () => {
                isDragging = false;
                img.style.cursor = 'grab';
            });

            img.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - img.offsetLeft;
                const walk = (x - startX) * 2; // Sesuaikan kecepatan slide
                img.scrollLeft = scrollLeft - walk;
            });
        });
    });
});
