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
