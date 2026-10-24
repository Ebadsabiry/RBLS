import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { existsSync, readdirSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
execFileSync(process.execPath, ['scripts/build-vercel.mjs'], { cwd: root });
const config = JSON.parse(readFileSync(path.join(root, 'vercel.json'), 'utf8'));
const output = path.resolve(root, config.outputDirectory);
assert.notEqual(output, path.join(root, 'public'));
assert.ok(!existsSync(path.join(output, 'index.php')), 'Static directory index must never contain the Laravel source');
assert.ok(existsSync(path.join(root, 'public/index.php')), 'Laravel front controller must remain available to the function');

let assets = 0;
function checkAssets(directory) {
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        assert.ok(!entry.name.startsWith('.'), 'Static output excludes dotfiles');
        assert.ok(!/\.(?:php\d*|phtml|phar)$/i.test(entry.name), 'Static output excludes PHP source');
        if (entry.isDirectory()) checkAssets(path.join(directory, entry.name));
        else assets++;
    }
}
checkAssets(output);
for (const asset of ['css/site.css', 'js/site.js', 'images/rbls-hero.png', 'RBLS Data/RBLS Profile 2026-compressed.pdf', 'RBLS Data/RBLS past experience portfolio.xlsx']) {
    assert.ok(existsSync(path.join(output, asset)), asset+' remains available');
}

// Model static directory index resolution, which occurs before catch-all routes.
function resolveRequest(url) {
    for (const route of config.routes) {
        if (route.handle === 'filesystem') {
            for (const candidate of [url, url.replace(/\/$/, '')+'/index.php']) {
                const relative = decodeURIComponent(candidate).replace(/^\//, '');
                if (relative && existsSync(path.join(output, relative))) return 'static:'+relative;
            }
        } else if (new RegExp('^(?:'+route.src+')$').test(url)) {
            return route.status ?? route.dest;
        }
    }
    return undefined;
}
assert.equal(resolveRequest('/'), '/api/index.php');
assert.equal(resolveRequest('/about'), '/api/index.php');
assert.equal(resolveRequest('/contact'), '/api/index.php');
assert.equal(resolveRequest('/index.php'), 404);
assert.equal(resolveRequest('/.env'), 404);
assert.equal(resolveRequest('/css/site.css'), 'static:css/site.css');
assert.equal(resolveRequest('/RBLS%20Data/RBLS%20Profile%202026-compressed.pdf'), 'static:RBLS Data/RBLS Profile 2026-compressed.pdf');
console.log('PASS Vercel routing: homepage and pages execute Laravel, source is excluded, and '+assets+' static assets remain available.');
