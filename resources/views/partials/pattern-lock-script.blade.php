<script>
    document.querySelectorAll('.pattern-lock-widget').forEach((widget) => {
        const targetId = widget.dataset.patternTarget;
        const hiddenInput = document.getElementById(targetId);
        const preview = widget.querySelector('[data-pattern-preview]');
        const clearButton = widget.querySelector('[data-pattern-clear]');
        const dots = widget.querySelectorAll('.pattern-dot');
        let sequence = [];

        const render = () => {
            dots.forEach((dot) => {
                dot.classList.toggle('active', sequence.includes(dot.dataset.dot));
            });

            if (hiddenInput) {
                hiddenInput.value = sequence.join('');
            }

            if (preview) {
                preview.textContent = sequence.length ? sequence.join('-') : 'None';
            }
        };

        const addDot = (dotValue) => {
            if (!sequence.includes(dotValue)) {
                sequence.push(dotValue);
                render();
            }
        };

        dots.forEach((dot) => {
            dot.addEventListener('click', () => addDot(dot.dataset.dot));
            dot.addEventListener('mouseenter', (event) => {
                if (event.buttons === 1) {
                    addDot(dot.dataset.dot);
                }
            });
            dot.addEventListener('touchstart', () => addDot(dot.dataset.dot), { passive: true });
        });

        clearButton?.addEventListener('click', () => {
            sequence = [];
            render();
        });

        if (hiddenInput?.value) {
            sequence = hiddenInput.value.split('');
            render();
        }
    });
</script>
