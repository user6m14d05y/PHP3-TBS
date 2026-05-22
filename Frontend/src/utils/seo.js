import { imageUrl, siteBaseUrl } from '@/utils/api';

const defaultSiteName = 'TBS Flower Shop';
const getCurrentOrigin = () => (typeof window !== 'undefined' ? window.location.origin : '');
const defaultBaseUrl = siteBaseUrl || getCurrentOrigin();

const normalizeText = (value = '') => String(value || '').replace(/\s+/g, ' ').trim();

const truncateText = (value = '', maxLength = 160) => {
  const text = normalizeText(value);
  if (text.length <= maxLength) return text;
  return text.slice(0, maxLength - 1).trim() + '...';
};

const resolveUrl = (path = '') => {
  if (/^https?:\/\//i.test(path)) return path;
  return `${defaultBaseUrl}${path.startsWith('/') ? path : `/${path}`}`;
};

const resolveImageUrl = (imagePath = '') => {
  if (!imagePath) return resolveUrl('/favicon.ico');
  if (/^https?:\/\//i.test(imagePath)) return imagePath;
  if (imagePath.startsWith('/')) return resolveUrl(imagePath);
  return imageUrl(imagePath);
};

const setMetaTag = (selector, attributes) => {
  let element = document.head.querySelector(selector);

  if (!element) {
    element = document.createElement('meta');
    document.head.appendChild(element);
  }

  Object.entries(attributes).forEach(([key, value]) => {
    element.setAttribute(key, value);
  });
};

const setLinkTag = (selector, attributes) => {
  let element = document.head.querySelector(selector);

  if (!element) {
    element = document.createElement('link');
    document.head.appendChild(element);
  }

  Object.entries(attributes).forEach(([key, value]) => {
    element.setAttribute(key, value);
  });
};

const setJsonLd = (id, data) => {
  let element = document.getElementById(id);

  if (!element) {
    element = document.createElement('script');
    element.id = id;
    element.type = 'application/ld+json';
    document.head.appendChild(element);
  }

  element.textContent = JSON.stringify(data);
};

export const setPageSeo = ({
  title,
  description,
  path = '/',
  image = '',
  type = 'website',
  noindex = false,
  schema = null,
}) => {
  if (typeof document === 'undefined') return;

  const pageTitle = truncateText(title || defaultSiteName, 70);
  const pageDescription = truncateText(description || 'TBS Flower Shop provides fresh flower designs with same-day delivery.', 170);
  const canonicalUrl = resolveUrl(path);
  const imageUrl = resolveImageUrl(image);

  document.title = pageTitle;
  setMetaTag('meta[name="description"]', { name: 'description', content: pageDescription });
  setMetaTag('meta[name="robots"]', {
    name: 'robots',
    content: noindex ? 'noindex, nofollow' : 'index, follow',
  });
  setLinkTag('link[rel="canonical"]', { rel: 'canonical', href: canonicalUrl });

  setMetaTag('meta[property="og:site_name"]', { property: 'og:site_name', content: defaultSiteName });
  setMetaTag('meta[property="og:type"]', { property: 'og:type', content: type });
  setMetaTag('meta[property="og:title"]', { property: 'og:title', content: pageTitle });
  setMetaTag('meta[property="og:description"]', { property: 'og:description', content: pageDescription });
  setMetaTag('meta[property="og:url"]', { property: 'og:url', content: canonicalUrl });
  setMetaTag('meta[property="og:image"]', { property: 'og:image', content: imageUrl });

  setMetaTag('meta[name="twitter:card"]', { name: 'twitter:card', content: 'summary_large_image' });
  setMetaTag('meta[name="twitter:title"]', { name: 'twitter:title', content: pageTitle });
  setMetaTag('meta[name="twitter:description"]', { name: 'twitter:description', content: pageDescription });
  setMetaTag('meta[name="twitter:image"]', { name: 'twitter:image', content: imageUrl });

  if (schema) {
    setJsonLd('page-schema-jsonld', schema);
  }
};

export const buildProductSchema = (product, path) => {
  const variants = product.variants || [];
  const prices = variants
    .map((variant) => Number(variant.sale_price || variant.price))
    .filter((price) => price > 0);
  const minPrice = prices.length ? Math.min(...prices) : undefined;
  const maxPrice = prices.length ? Math.max(...prices) : undefined;
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
      name: defaultSiteName,
    },
    category: product.categoryItem?.name || product.category_item?.name || product.category?.name || 'Fresh flowers',
    url: resolveUrl(path),
  };

  if (prices.length) {
    schema.offers = {
      '@type': 'AggregateOffer',
      priceCurrency: 'VND',
      lowPrice: minPrice,
      highPrice: maxPrice,
      offerCount: prices.length,
      availability: inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
      url: resolveUrl(path),
    };
  }

  return schema;
};

export const getSeoDescription = (source, fallback = '') => {
  return truncateText(source?.meta_description || source?.description || fallback, 170);
};

export const getSeoTitle = (source, fallback = defaultSiteName) => {
  return truncateText(source?.seo_title || source?.name || fallback, 70);
};
