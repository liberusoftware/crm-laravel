import 'preline';

const loadStripe = () => new Promise((resolve, reject) => {
    if (window.Stripe) {
        resolve(window.Stripe);
        return;
    }

    const existingScript = document.querySelector('script[data-stripe-js]');
    if (existingScript) {
        existingScript.addEventListener('load', () => resolve(window.Stripe), { once: true });
        existingScript.addEventListener('error', () => reject(new Error('Stripe.js failed to load.')), { once: true });
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://js.stripe.com/v3/';
    script.async = true;
    script.dataset.stripeJs = 'true';
    script.addEventListener('load', () => resolve(window.Stripe), { once: true });
    script.addEventListener('error', () => reject(new Error('Stripe.js failed to load.')), { once: true });
    document.head.appendChild(script);
});

const initializeStripeForms = async () => {
    const forms = [...document.querySelectorAll('[data-stripe-form]')];
    if (forms.length === 0) return;

    const key = document.querySelector('meta[name="stripe-key"]')?.content;
    if (!key) {
        forms.forEach((form) => {
            form.querySelector('[data-stripe-error]')?.replaceChildren('Stripe payments are not configured.');
        });
        return;
    }

    try {
        const stripeFactory = await loadStripe();
        if (typeof stripeFactory !== 'function') throw new Error('Stripe.js did not expose its client.');
        const stripe = stripeFactory(key);

        forms.forEach((form) => {
            const mountPoint = form.querySelector('[data-stripe-card]');
            if (!mountPoint || mountPoint.dataset.stripeMounted === 'true') return;

            const card = stripe.elements().create('card');
            card.mount(mountPoint);
            mountPoint.dataset.stripeMounted = 'true';
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const button = form.querySelector('button[type="submit"]');
                const error = form.querySelector('[data-stripe-error]');
                if (button) button.disabled = true;
                if (error) error.textContent = '';

                try {
                    const result = await stripe.createPaymentMethod({ type: 'card', card });
                    if (result.error) throw new Error(result.error.message);
                    form.querySelector('[data-stripe-payment-method]').value = result.paymentMethod.id;
                    form.submit();
                } catch (submissionError) {
                    if (error) error.textContent = submissionError instanceof Error ? submissionError.message : 'Unable to process the card.';
                    if (button) button.disabled = false;
                }
            });
        });
    } catch (loadingError) {
        forms.forEach((form) => {
            form.querySelector('[data-stripe-error]')?.replaceChildren(loadingError instanceof Error ? loadingError.message : 'Unable to load card payments.');
        });
    }
};

document.addEventListener('DOMContentLoaded', initializeStripeForms);
