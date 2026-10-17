/**
 * v-reveal — fades/slides elements in when they enter the viewport.
 *
 * Usage:  <div v-reveal>…</div>
 *         <div v-reveal="{ delay: 200, from: 'left' }">…</div>
 */
let observer;

function getObserver() {
    if (observer || typeof window === 'undefined') return observer;

    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
    );

    return observer;
}

export default {
    mounted(el, binding) {
        const opts = binding.value || {};
        el.classList.add('reveal', `reveal--${opts.from || 'up'}`);
        if (opts.delay) el.style.setProperty('--reveal-delay', `${opts.delay}ms`);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            el.classList.add('is-revealed');
            return;
        }

        getObserver().observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
