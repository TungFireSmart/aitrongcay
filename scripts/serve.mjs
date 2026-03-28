import http from 'node:http';
import { createReadStream, existsSync, statSync } from 'node:fs';
import { extname, join, normalize } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = normalize(join(fileURLToPath(new URL('.', import.meta.url)), '..'));
const host = process.env.HOST || '127.0.0.1';
const port = Number(process.env.PORT || 4173);

const contentTypes = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.ico': 'image/x-icon',
};

function safePath(urlPath) {
  const cleaned = decodeURIComponent((urlPath || '/').split('?')[0]);
  const requested = cleaned === '/' ? '/index.html' : cleaned;
  const candidate = normalize(join(root, requested));
  if (!candidate.startsWith(root)) return null;
  return candidate;
}

const server = http.createServer((req, res) => {
  const candidate = safePath(req.url);
  if (!candidate) {
    res.writeHead(403, { 'content-type': 'text/plain; charset=utf-8' });
    res.end('Forbidden');
    return;
  }

  let filePath = candidate;
  if (existsSync(filePath) && statSync(filePath).isDirectory()) {
    filePath = join(filePath, 'index.html');
  }

  if (!existsSync(filePath)) {
    res.writeHead(404, { 'content-type': 'text/plain; charset=utf-8' });
    res.end('Not found');
    return;
  }

  const ext = extname(filePath).toLowerCase();
  res.writeHead(200, { 'content-type': contentTypes[ext] || 'application/octet-stream' });
  createReadStream(filePath).pipe(res);
});

server.listen(port, host, () => {
  console.log(`aitrongcay running at http://${host}:${port}`);
  console.log('Open this first: /index.html');
  console.log('Then review website flow: /index.html');
  console.log('Khu vườn demo: /portal/dashboard.html');
});
