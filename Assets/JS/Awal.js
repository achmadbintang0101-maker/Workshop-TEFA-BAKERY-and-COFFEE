        function pindahHalaman(url) {
            const container = document.getElementById('main-container');
            container.classList.add('fade-out');
            setTimeout(() => {
                window.location.href = url;
            }, 300);
        }