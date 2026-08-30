const setTheme = (mode) => {
    document.documentElement.dataset.theme = mode
    localStorage.setItem('crm-theme', mode)
}

const savedTheme = localStorage.getItem('crm-theme')
if (savedTheme === 'dark' || savedTheme === 'light') setTheme(savedTheme)

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]')
    if (!toggle) return
    setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark')
})

document.addEventListener('DOMContentLoaded', () => {
    if (!window.Stripe) return
    const key = document.querySelector('meta[name="stripe-key"]')?.content
    if (!key) return
    const stripe = window.Stripe(key)
    document.querySelectorAll('[data-stripe-form]').forEach((form) => {
        const elements = stripe.elements()
        const card = elements.create('card')
        card.mount(form.querySelector('[data-stripe-card]'))
        form.addEventListener('submit', async (event) => {
            event.preventDefault()
            const result = await stripe.createPaymentMethod({ type: 'card', card })
            if (result.error) {
                form.querySelector('[data-stripe-error]').textContent = result.error.message
                return
            }
            form.querySelector('[data-stripe-payment-method]').value = result.paymentMethod.id
            form.submit()
        })
    })
})
