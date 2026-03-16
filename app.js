const timerElement = document.getElementById('timer');

if (timerElement) {
    let timeLeft = Number(timerElement.dataset.timeLeft);

    const formatTime = (seconds) => {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
    };

    const updateTimer = () => {
        timerElement.textContent = formatTime(Math.max(timeLeft, 0));

        if (timeLeft <= 0) {
            window.location.href = '../finish.php?status=lost';
            return;
        }

        timeLeft -= 1;
    };

    updateTimer();
    setInterval(updateTimer, 1000);
}
