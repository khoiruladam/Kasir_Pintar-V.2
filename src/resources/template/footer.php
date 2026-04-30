</div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Efek haptic sederhana (opsional) untuk navigasi mobile
        document.querySelectorAll('.nav-item-custom').forEach(item => {
            item.addEventListener('click', () => {
                if (window.navigator.vibrate) {
                    window.navigator.vibrate(50);
                }
            });
        });
    </script>
</body>
</html>