import { mkdir, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { buildSeoApiCandidates, loadSeoEnv } from './seo-env.mjs';

const { siteUrl, apiUrl, seoApiUrl } = await loadSeoEnv();
const seoApiCandidates = buildSeoApiCandidates({ apiUrl, seoApiUrl });
const publicDir = resolve(process.cwd(), 'public');

if (!siteUrl) {
  console.warn('[seo] VITE_SITE_URL is missing. Sitemap URLs need an absolute site URL for production.');
}

const staticRoutes = [
  { path: '/', changefreq: 'daily', priority: '1.0' },
  { path: '/product', changefreq: 'daily', priority: '0.9' },
  { path: '/contact', changefreq: 'monthly', priority: '0.6' },
  { path: '/ordering', changefreq: 'monthly', priority: '0.5' },
  { path: '/delivery-policy', changefreq: 'monthly', priority: '0.5' },
  { path: '/refund-policy', changefreq: 'monthly', priority: '0.5' },
  { path: '/terms', changefreq: 'monthly', priority: '0.4' },
];

const escapeXml = (value = '') =>
  String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');

const fetchProducts = async () => {
  const attemptedUrls = [];

  for (const baseUrl of [...new Set(seoApiCandidates)]) {
    const endpoint = `${baseUrl}/api/seo/products`;
    attemptedUrls.push(endpoint);

    try {
      const response = await fetch(endpoint);
      if (!response.ok) throw new Error(`API responded with ${response.status}`);

      console.log(`[seo] Product data loaded from ${endpoint}`);
      return await response.json();
    } catch (error) {
      console.warn(`[seo] Product API unavailable at ${endpoint}: ${error.message}`);
    }
  }

  console.warn(`[seo] Product sitemap generation skipped after trying: ${attemptedUrls.join(', ')}`);
  return [];
};

const buildUrl = ({ path, changefreq, priority, lastmod }) => {
  const normalizedPath = path === '/' ? '/' : path.replace(/\/$/, '');
  const lines = [
    '  <url>',
    `    <loc>${escapeXml(`${siteUrl}${normalizedPath}`)}</loc>`,
  ];

  if (lastmod) lines.push(`    <lastmod>${escapeXml(lastmod)}</lastmod>`);
  lines.push(`    <changefreq>${changefreq}</changefreq>`);
  lines.push(`    <priority>${priority}</priority>`);
  lines.push('  </url>');

  return lines.join('\n');
};

const writeSitemap = async (products) => {
  const productRoutes = products
    .filter((product) => product.slug)
    .map((product) => ({
      path: `/product/${product.slug}`,
      changefreq: 'weekly',
      priority: '0.8',
      lastmod: product.updated_at || product.created_at,
    }));

  const body = [...staticRoutes, ...productRoutes].map(buildUrl).join('\n');
  const sitemap = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${body}\n</urlset>\n`;

  await mkdir(publicDir, { recursive: true });
  await writeFile(resolve(publicDir, 'sitemap.xml'), sitemap, 'utf8');
};

const writeRobots = async () => {
  const robots = [
    'User-agent: *',
    'Allow: /',
    'Disallow: /admin',
    'Disallow: /login',
    'Disallow: /register',
    'Disallow: /forgot',
    'Disallow: /profile',
    'Disallow: /cart',
    'Disallow: /checkout',
    'Disallow: /order-success',
    '',
    `Sitemap: ${siteUrl}/sitemap.xml`,
    '',
  ].join('\n');

  await mkdir(publicDir, { recursive: true });
  await writeFile(resolve(publicDir, 'robots.txt'), robots, 'utf8');
};

const products = await fetchProducts();
await writeSitemap(products);
await writeRobots();

console.log(`[seo] Generated robots.txt and sitemap.xml with ${products.length} product URLs.`);
