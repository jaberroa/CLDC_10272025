/**
 * Sitemap Generator Utility
 * Generates XML sitemap for search engines
 */

export interface SitemapUrl {
  loc: string;
  lastmod?: string;
  changefreq?: 'always' | 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly' | 'never';
  priority?: number;
}

export function generateSitemap(urls: SitemapUrl[]): string {
  const urlset = urls
    .map(
      (url) => `
  <url>
    <loc>${url.loc}</loc>
    ${url.lastmod ? `<lastmod>${url.lastmod}</lastmod>` : ''}
    ${url.changefreq ? `<changefreq>${url.changefreq}</changefreq>` : ''}
    ${url.priority !== undefined ? `<priority>${url.priority}</priority>` : ''}
  </url>`
    )
    .join('');

  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  ${urlset}
</urlset>`;
}

/**
 * Default sitemap URLs for static pages
 */
export const defaultSitemapUrls: SitemapUrl[] = [
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

/**
 * Generate sitemap and save to public directory
 * Note: This should be run during build process
 */
export function buildSitemap(): string {
  return generateSitemap(defaultSitemapUrls);
}
