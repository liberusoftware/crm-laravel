import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import { viteStaticCopy } from "vite-plugin-static-copy";

import fs from 'node:fs'
import path from 'node:path'

const themeInputs = fs.existsSync('themes')
    ? fs.readdirSync('themes', { withFileTypes: true })
        .filter((entry) => entry.isDirectory())
        .flatMap((entry) => {
            const manifestPath = path.join('themes', entry.name, 'theme.json')
            if (!fs.existsSync(manifestPath)) return []
            const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'))
            return [...(manifest.assets?.css ?? []), ...(manifest.assets?.js ?? [])]
                .map((asset) => path.join('themes', entry.name, asset))
                .filter((asset) => fs.existsSync(asset))
        })
    : []

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
		    'resources/css/filament/admin/theme.css', ...themeInputs],
            refresh: [
                ...refreshPaths,
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
                    src: "resources/images/*",
                    dest: "images",
                },
            ],
        }),
    ],
})
