import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import { viteStaticCopy } from 'vite-plugin-static-copy'

import fs from 'node:fs'
import { fileURLToPath } from 'node:url'
import path from 'node:path'

const projectRoot = fileURLToPath(new URL('.', import.meta.url))
const themesDirectory = path.join(projectRoot, 'themes')

const themeInputs = fs.existsSync(themesDirectory)
    ? fs.readdirSync(themesDirectory, { withFileTypes: true })
        .filter((entry) => entry.isDirectory())
        .flatMap((entry) => {
            const manifestPath = path.join(themesDirectory, entry.name, 'theme.json')
            if (!fs.existsSync(manifestPath)) return []
            const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'))
            return [...(manifest.assets?.css ?? []), ...(manifest.assets?.js ?? [])]
                .map((asset) => path.join(themesDirectory, entry.name, asset))
                .filter((asset) => fs.existsSync(asset))
                .map((asset) => path.relative(projectRoot, asset).split(path.sep).join('/'))
        })
    : []

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
                'resources/css/filament/admin/theme.css', ...themeInputs],
            refresh: [
                ...refreshPaths,
                'themes/**',
                'app/Filament/**',
                'app/Forms/Components/**',
                'app/Livewire/**',
                'app/Infolists/Components/**',
                'app/Providers/Filament/**',
                'app/Tables/Columns/**',
            ],
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/images/*',
                    dest: 'images',
                },
            ],
        }),
    ],
})
