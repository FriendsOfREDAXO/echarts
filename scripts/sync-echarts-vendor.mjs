import { copyFileSync, existsSync, mkdirSync, writeFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';

const require = createRequire(import.meta.url);

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const source = resolve(projectRoot, 'node_modules/echarts/dist/echarts.min.js');
const target = resolve(projectRoot, 'assets/echarts.min.js');
const versionFile = resolve(projectRoot, 'assets/echarts.vendor-version.txt');

if (!existsSync(source)) {
  throw new Error(
    'Vendor file not found. Run "npm install" or "pnpm install" in the addon directory first.'
  );
}

mkdirSync(dirname(target), { recursive: true });
copyFileSync(source, target);

const echartsPkg = require(resolve(projectRoot, 'node_modules/echarts/package.json'));
const version = typeof echartsPkg.version === 'string' ? echartsPkg.version : 'unknown';
writeFileSync(versionFile, `echarts@${version}\n`, 'utf8');

console.log(`Synced ${source} -> ${target}`);
console.log(`Recorded vendor version in ${versionFile}`);
