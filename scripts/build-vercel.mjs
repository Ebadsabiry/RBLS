import { cpSync, existsSync, mkdirSync, readdirSync, rmSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const source = path.join(root, 'public');
const output = path.join(root, 'vercel-public');

// Only reset the generated output directory directly inside this project.
if (path.dirname(output) !== root || path.basename(output) !== 'vercel-public') {
    throw new Error('Unexpected Vercel output directory.');
}
if (existsSync(output)) rmSync(output, { recursive: true, force: true });
mkdirSync(output, { recursive: true });

// Vercel serves index.php as a static directory index before a catch-all route.
// Keep PHP entrypoints and dotfiles out of the static deployment entirely.
const isPublicAsset = (file) => {
    const relative = path.relative(source, file);
    return !relative.split(path.sep).some(part => part.startsWith('.'))
        && !/\.(?:php\d*|phtml|phar)$/i.test(relative);
};
for (const entry of readdirSync(source)) {
    cpSync(path.join(source, entry), path.join(output, entry), {
        recursive: true,
        filter: isPublicAsset,
    });
}

console.log('Prepared static assets for Vercel; Laravel requests use api/index.php.');
