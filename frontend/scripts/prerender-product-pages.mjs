import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { buildSeoApiCandidates, loadSeoEnv } from './seo-env.mjs';

const { siteUrl, apiUrl, seoApiUrl } = await loadSeoEnv();
const seoApiCandidates = buildSeoApiCandidates({ apiUrl, seoApiUrl });
const distDir = resolve(process.cwd(), 'dist');
const apiImageUrl = apiUrl ? `${apiUrl}/images` : '';

if (!siteUrl) {
  console.warn('[prerender] VITE_SITE_URL is missing. Canonical URLs need an absolute site URL for production.');
}

const staticPages = [
  {
    path: '/',
    title: 'TBS Flower Shop | Fresh flower designs',
    description: 'Fresh flower arrangements for gifts, openings, events, and same-day delivery.',
    heading: 'TBS Flower Shop',
  },
  {
    path: '/product',
    title: 'Fresh flower shop | TBS Flower Shop',
    description: 'Explore fresh flower collections by occasion, category, and price range.',
    heading: 'Fresh flower collection',
  },
  {
    path: '/contact',
    title: 'Contact TBS Flower Shop',
    description: 'Contact TBS Flower Shop for flower recommendations, custom orders, and delivery support.',
    heading: 'Contact TBS Flower Shop',
  },
  {
    path: '/ordering',
    title: 'How to order flowers | TBS Flower Shop',
    description: 'Learn how to choose flowers, add them to cart, enter delivery details, and pay securely.',
    heading: 'How to order flowers',
  },
  {
    path: '/delivery-policy',
    title: 'Flower delivery policy | TBS Flower Shop',
    description: 'Delivery timing, shipping fees, and fresh flower handling commitments from TBS Flower Shop.',
    heading: 'Flower delivery policy',
  },
  {
    path: '/refund-policy',
    title: 'Return and refund policy | TBS Flower Shop',
    description: 'Return and refund support for flower orders with delivery issues, wrong items, or damaged products.',
    heading: 'Return and refund policy',
  },
  {
    path: '/terms',
    title: 'Policies and terms | TBS Flower Shop',
    description: 'Privacy policy, service terms, and customer responsibilities when buying flowers from TBS Flower Shop.',
    heading: 'Policies and terms',
  },
];

const escapeHtml = (value = '') =>
  String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const normalizeText = (value = '') => String(value || '').replace(/\s+/g, ' ').trim();
const truncateText = (value = '', maxLength = 170) => {
  const text = normalizeText(value);
  return text.length <= maxLength ? text : `${text.slice(0, maxLength - 1).trim()}...`;
};

const stripExistingSeo = (html) =>
  html
    .replace(/<title>[\s\S]*?<\/title>\s*/i, '')
    .replace(/\s*<meta\s+name=["']description["'][^>]*>\s*/gi, '')
    .replace(/\s*<meta\s+name=["']robots["'][^>]*>\s*/gi, '')
    .replace(/\s*<link\s+rel=["']canonical["'][^>]*>\s*/gi, '')
    .replace(/\s*<meta\s+(?:property|name)=["'](?:og|twitter):[^"']+["'][^>]*>\s*/gi, '')
    .replace(/\s*<script\s+id=["']page-schema-jsonld["'][\s\S]*?<\/script>\s*/gi, '');

const escapeJsonLd = (data) => JSON.stringify(data).replace(/</g, '\\u003c');

const resolveImageUrl = (image = '') => {
  if (!image) return `${siteUrl}/favicon.ico`;
  if (/^https?:\/\//i.test(image)) return image;
  if (image.startsWith('/')) return `${siteUrl}${image}`;
  return apiImageUrl ? `${apiImageUrl}/${image}` : `${siteUrl}/images/${image}`;
};

const fetchProducts = async () => {
  const attemptedUrls = [];

  for (const baseUrl of [...new Set(seoApiCandidates)]) {
    const endpoint = `${baseUrl}/api/seo/products`;
    attemptedUrls.push(endpoint);

    try {
      const response = await fetch(endpoint);
      if (!response.ok) throw new Error(`API responded with ${response.status}`);

      console.log(`[prerender] Product data loaded from ${endpoint}`);
      return await response.json();
    } catch (error) {
      console.warn(`[prerender] Product API unavailable at ${endpoint}: ${error.message}`);
    }
  }

  console.warn(`[prerender] Product pages skipped after trying: ${attemptedUrls.join(', ')}`);
  return [];
};

const buildProductSchema = (product) => {
  const variants = product.variants || [];
  const prices = variants
    .map((variant) => Number(variant.sale_price || variant.price))
    .filter((price) => price > 0);
  const inStock = variants.some((variant) => Number(variant.stock) > 0);
  const images = [
    product.thumbnail,
    ...(product.images || []).map((image) => image.image_path),
  ]
    .filter(Boolean)
    .map(resolveImageUrl);

  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: product.name,
    description: truncateText(product.meta_description || product.description || product.name, 500),
    image: images.length ? images : [resolveImageUrl(product.thumbnail)],
    sku: variants.find((variant) => variant.sku)?.sku || `product-${product.id}`,
    brand: {
      '@type': 'Brand',
      name: 'TBS Flower Shop',
    },
    category: product.categoryItem?.name || product.category_item?.name || product.category?.name || 'Fresh flowers',
    url: `${siteUrl}/product/${product.slug}`,
  };

  if (prices.length) {
    schema.offers = {
      '@type': 'AggregateOffer',
      priceCurrency: 'VND',
      lowPrice: Math.min(...prices),
      highPrice: Math.max(...prices),
      offerCount: prices.length,
      availability: inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
      url: `${siteUrl}/product/${product.slug}`,
    };
  }

  return schema;
};

const buildWebPageSchema = (page) => {
  const path = page.path === '/' ? '/' : page.path;

  return {
    '@context': 'https://schema.org',
    '@type': page.path === '/' ? 'WebSite' : 'WebPage',
    name: page.title,
    description: page.description,
    url: `${siteUrl}${path}`,
    publisher: {
      '@type': 'Organization',
      name: 'TBS Flower Shop',
    },
  };
};

const injectSeo = (html, product) => {
  const path = `/product/${product.slug}`;
  const title = truncateText(product.seo_title || product.name || 'TBS Flower Shop', 70);
  const description = truncateText(product.meta_description || product.description || product.name, 170);
  const imageUrl = resolveImageUrl(product.thumbnail);
  const canonicalUrl = `${siteUrl}${path}`;
  const schema = escapeJsonLd(buildProductSchema(product));

  const metaBlock = [
    `<title>${escapeHtml(title)}</title>`,
    `<meta name="description" content="${escapeHtml(description)}">`,
    '<meta name="robots" content="index, follow">',
    `<link rel="canonical" href="${escapeHtml(canonicalUrl)}">`,
    '<meta property="og:site_name" content="TBS Flower Shop">',
    '<meta property="og:type" content="product">',
    `<meta property="og:title" content="${escapeHtml(title)}">`,
    `<meta property="og:description" content="${escapeHtml(description)}">`,
    `<meta property="og:url" content="${escapeHtml(canonicalUrl)}">`,
    `<meta property="og:image" content="${escapeHtml(imageUrl)}">`,
    '<meta name="twitter:card" content="summary_large_image">',
    `<meta name="twitter:title" content="${escapeHtml(title)}">`,
    `<meta name="twitter:description" content="${escapeHtml(description)}">`,
    `<meta name="twitter:image" content="${escapeHtml(imageUrl)}">`,
    `<script id="page-schema-jsonld" type="application/ld+json">${schema}</script>`,
  ].join('\n    ');

  const fallbackHtml = [
    '<main data-prerender="product">',
    `  <h1>${escapeHtml(product.name)}</h1>`,
    `  <p>${escapeHtml(description)}</p>`,
    product.thumbnail
      ? `  <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(product.image_alt || product.name)}">`
      : '',
    '</main>',
  ].join('\n');

  return stripExistingSeo(html)
    .replace('</head>', `    ${metaBlock}\n  </head>`)
    .replace('<div id="app"></div>', `<div id="app">${fallbackHtml}</div>`);
};

const injectStaticSeo = (html, page) => {
  const path = page.path === '/' ? '/' : page.path;
  const title = truncateText(page.title, 70);
  const description = truncateText(page.description, 170);
  const canonicalUrl = `${siteUrl}${path}`;
  const imageUrl = resolveImageUrl('/favicon.ico');
  const schema = escapeJsonLd(buildWebPageSchema(page));

  const metaBlock = [
    `<title>${escapeHtml(title)}</title>`,
    `<meta name="description" content="${escapeHtml(description)}">`,
    '<meta name="robots" content="index, follow">',
    `<link rel="canonical" href="${escapeHtml(canonicalUrl)}">`,
    '<meta property="og:site_name" content="TBS Flower Shop">',
    '<meta property="og:type" content="website">',
    `<meta property="og:title" content="${escapeHtml(title)}">`,
    `<meta property="og:description" content="${escapeHtml(description)}">`,
    `<meta property="og:url" content="${escapeHtml(canonicalUrl)}">`,
    `<meta property="og:image" content="${escapeHtml(imageUrl)}">`,
    '<meta name="twitter:card" content="summary_large_image">',
    `<meta name="twitter:title" content="${escapeHtml(title)}">`,
    `<meta name="twitter:description" content="${escapeHtml(description)}">`,
    `<meta name="twitter:image" content="${escapeHtml(imageUrl)}">`,
    `<script id="page-schema-jsonld" type="application/ld+json">${schema}</script>`,
  ].join('\n    ');

  const fallbackHtml = [
    '<main data-prerender="page">',
    `  <h1>${escapeHtml(page.heading)}</h1>`,
    `  <p>${escapeHtml(description)}</p>`,
    '</main>',
  ].join('\n');

  return stripExistingSeo(html)
    .replace('</head>', `    ${metaBlock}\n  </head>`)
    .replace('<div id="app"></div>', `<div id="app">${fallbackHtml}</div>`);
};

const writeStaticPage = async (html, page) => {
  if (page.path === '/') {
    await writeFile(resolve(distDir, 'index.html'), injectStaticSeo(html, page), 'utf8');
    return;
  }

  const routeDir = resolve(distDir, page.path.replace(/^\//, ''));
  await mkdir(routeDir, { recursive: true });
  await writeFile(resolve(routeDir, 'index.html'), injectStaticSeo(html, page), 'utf8');
};

const writeProductPage = async (html, product) => {
  if (!product.slug) return;

  const routeDir = resolve(distDir, 'product', product.slug);
  await mkdir(routeDir, { recursive: true });
  await writeFile(resolve(routeDir, 'index.html'), injectSeo(html, product), 'utf8');
};

const indexHtml = await readFile(resolve(distDir, 'index.html'), 'utf8');
const products = await fetchProducts();

await Promise.all(staticPages.map((page) => writeStaticPage(indexHtml, page)));
await Promise.all(products.map((product) => writeProductPage(indexHtml, product)));

console.log(`[prerender] Generated ${staticPages.length} static HTML pages and ${products.length} product HTML pages in dist.`);
