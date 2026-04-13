document.addEventListener('DOMContentLoaded', () => {
    const timerElement = document.getElementById('timer');
    const countdownElement = document.getElementById('countdown');

    if (timerElement) {
        let elapsed = Number(timerElement.dataset.elapsed || 0);
        const limit = Number(timerElement.dataset.limit || 900);
        const expireUrl = timerElement.dataset.expireUrl || '';
        let hasExpired = false;

        const formatTime = (seconds) => {
            const safeSeconds = Math.max(0, seconds);
            const minutes = Math.floor(safeSeconds / 60);
            const remainingSeconds = safeSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
        };

        const updateTimer = () => {
            timerElement.textContent = formatTime(elapsed);

            if (countdownElement) {
                const remaining = Math.max(0, limit - elapsed);
                countdownElement.textContent = formatTime(remaining);
            }

            if (elapsed >= limit && !hasExpired) {
                hasExpired = true;

                if (expireUrl !== '') {
                    window.location.href = expireUrl;
                    return;
                }
            }

            elapsed += 1;
        };

        updateTimer();
        setInterval(updateTimer, 1000);
    }

    const playerCountSelect = document.getElementById('player-count');
    const playerFields = document.querySelectorAll('[data-player-field]');

    if (playerCountSelect && playerFields.length) {
        const syncPlayerFields = () => {
            const count = Number(playerCountSelect.value || 1);

            playerFields.forEach((field) => {
                const fieldIndex = Number(field.dataset.playerField);
                const input = field.querySelector('input');
                const isVisible = fieldIndex <= count;

                field.classList.toggle('is-hidden', !isVisible);
                field.style.display = isVisible ? 'grid' : 'none';

                if (input) {
                    input.required = isVisible;

                    if (!isVisible) {
                        input.value = '';
                    }
                }
            });
        };

        syncPlayerFields();
        playerCountSelect.addEventListener('change', syncPlayerFields);
        playerCountSelect.addEventListener('input', syncPlayerFields);
    }
});
