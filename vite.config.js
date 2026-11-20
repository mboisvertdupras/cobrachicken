import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig, lazyPlugins } from 'vite-plus';

const ignoredPaths = [
    '**/.agents/**',
    '**/.claude/**',
    '**/.grok/**',
    '**/.junie/**',
    '**/.opencode/**',
    '**/.pi/**',
];

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ]),
    server: {
        cors: true,
        watch: {
            ignored: [
                ...ignoredPaths,
                '**/storage/framework/views/**',
                '**/vendor/**',
            ],
        },
    },
    lint: {
        ignorePatterns: [
            ...ignoredPaths,
            'vendor/**',
            'node_modules/**',
            'public/**',
            'storage/**',
            'bootstrap/**',
        ],
        options: {
            denyWarnings: true,
            typeAware: false,
        },
    },
    fmt: {
        printWidth: 80,
        tabWidth: 4,
        singleQuote: true,
        semi: true,
        singleAttributePerLine: false,
        htmlWhitespaceSensitivity: 'css',
        ignorePatterns: [
            ...ignoredPaths,
            'vendor/**',
            'node_modules/**',
            'public/**',
            'storage/**',
            'bootstrap/**',
            'composer.json',
            'composer.lock',
            'package-lock.json',
            '**/*.md',
            '.mcp.json',
            'opencode.json',
            'boost.json',
        ],
        sortTailwindcss: {
            stylesheet: 'resources/css/app.css',
        },
    },
});
