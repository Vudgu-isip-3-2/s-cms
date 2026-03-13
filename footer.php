<footer>
    <p id="datetime">
        Текущая дата и время: <?php echo date('d.m.Y H:i'); ?>
    </p>
</footer>

<script>
    function updateDateTime() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const datetimeString = `${day}.${month}.${year} ${hours}:${minutes}`;
        document.getElementById('datetime').innerText = 'Текущая дата и время: ' + datetimeString;
    }
    setInterval(updateDateTime, 1000);
</script>
