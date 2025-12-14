    (function() {
        const ID = 'chat-body';

        function el() {
            return document.getElementById(ID);
        }

        function keyOf(t) {
            return `chat_draft_trade_${t.dataset.tradeId}_user_${t.dataset.userId}`;
        }

        function restore(force = false) {
            const t = el();
            if (!t) return;
            const saved = localStorage.getItem(keyOf(t));
            if (!saved) return;

            if (force || t.value.trim() === '') {
                t.value = saved;
            }
        }

        function save() {
            const t = el();
            if (!t) return;
            const v = t.value;
            if (v.trim() === '') return;
            localStorage.setItem(keyOf(t), v);
        }

        window.addEventListener('pageshow', function() {
            restore(true);
            setTimeout(() => restore(true), 0);
            requestAnimationFrame(() => restore(true));
        });

        window.addEventListener('pagehide', save);

        let last = null;
        setInterval(() => {
            const t = el();
            if (!t) return;
            if (t.value !== last) {
                last = t.value;
                save();
            }
        }, 500);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => restore(false));
        } else {
            restore(false);
        }

        document.addEventListener('submit', function(e) {
            const t = el();
            if (!t) return;
            if (e.target && e.target.closest('form')) {
                localStorage.removeItem(keyOf(t));
            }
        }, true);
    })();


    document.addEventListener('DOMContentLoaded', () => {
        const dialog = document.getElementById('reviewModal');
        if (!dialog) return;

        const stars = Array.from(dialog.querySelectorAll('.review-star'));
        const input = dialog.querySelector('#reviewScore');

        function paint(score) {
            stars.forEach(btn => {
                const v = Number(btn.dataset.value);
                const on = v <= score;
                btn.classList.toggle('is-on', on);
                btn.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
        }

        const init=Number(input?.value || 0);
        if (init) paint(init);

        stars.forEach(btn=> {
            btn.addEventListener('click', () => {
                const score = Number(btn.dataset.value);
                input.value = score;
                paint(score);
            });

            btn.addEventListener('mouseenter', () => paint(Number(btn.dataset.value)));
        });

        dialog.addEventListener('mouseleave', () => {
            const score = Number(input?.value || 0);
            paint(score);
        });
    });
