/**
 * Sitemap Generation Script
 * Run this script to generate sitemap.xml in the public directory
 * Usage: node scripts/generate-sitemap.js
 */

import { writeFileSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Sitemap URLs configuration
const urls = [
  {
    loc: 'https://cldci.com/',
    changefreq: 'daily',
    priority: 1.0,
  },
  {
    loc: 'https://cldci.com/dashboard',
    changefreq: 'daily',
    priority: 0.9,
  },
  {
    loc: 'https://cldci.com/miembros',
    changefreq: 'weekly',
    priority: 0.8,
  },
  {
    loc: 'https://cldci.com/directiva',
    changefreq: 'monthly',
    priority: 0.8,
  },
  {
    loc: 'https://cldci.com/elecciones',
    changefreq: 'monthly',
    priority: 0.7,
  },
  {
    loc: 'https://cldci.com/formacion-profesional',
    changefreq: 'weekly',
    priority: 0.8,
  },
  {
    loc: 'https://cldci.com/transparencia',
    changefreq: 'monthly',
    priority: 0.7,
  },
  {
    loc: 'https://cldci.com/documentos-legales',
    changefreq: 'monthly',
    priority: 0.6,
  },
  {
    loc: 'https://cldci.com/premios',
    changefreq: 'yearly',
    priority: 0.6,
  },
];

function generateSitemap(urls) {
  const lastmod = new Date().toISOString().split('T')[0];
  
  const urlset = urls
    .map(
      (url) => `
  <url>
    <loc>${url.loc}</loc>
    <lastmod>${lastmod}</lastmod>
    <changefreq>${url.changefreq}</changefreq>
    <priority>${url.priority}</priority>
  </url>`
    )
    .join('');

  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urlset}
</urlset>`;
}

try {
  const sitemap = generateSitemap(urls);
  const publicPath = join(__dirname, '..', 'public', 'sitemap.xml');
  
  writeFileSync(publicPath, sitemap, 'utf-8');
  console.log('✅ Sitemap generated successfully at:', publicPath);
} catch (error) {
  console.error('❌ Error generating sitemap:', error);
  process.exit(1);
}
