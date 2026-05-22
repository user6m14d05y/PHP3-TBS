const trimTrailingSlash = (value = '') => String(value || '').replace(/\/+$/, '');
const trimLeadingSlash = (value = '') => String(value || '').replace(/^\/+/, '');
const isAbsoluteUrl = (value = '') => /^(https?:)?\/\//i.test(value) || /^(data|blob):/i.test(value);

export const apiBaseUrl = trimTrailingSlash(import.meta.env.VITE_API_URL || '');
export const siteBaseUrl = trimTrailingSlash(import.meta.env.VITE_SITE_URL || '');

export const apiUrl = (path = '') => {
  const normalizedPath = trimLeadingSlash(path);
  return apiBaseUrl ? `${apiBaseUrl}/${normalizedPath}` : `/${normalizedPath}`;
};

export const assetUrl = (path = '') => {
  if (!path) return '';
  if (isAbsoluteUrl(path)) return path;

  const normalizedPath = trimLeadingSlash(path);
  return apiBaseUrl ? `${apiBaseUrl}/${normalizedPath}` : `/${normalizedPath}`;
};

export const imageUrl = (path = '') => {
  if (!path) return '';
  if (isAbsoluteUrl(path)) return path;

  return assetUrl(`images/${trimLeadingSlash(path)}`);
};

export const videoUrl = (path = '') => {
  if (!path) return '';
  if (isAbsoluteUrl(path)) return path;

  return assetUrl(`videos/${trimLeadingSlash(path)}`);
};

export const defaultImageUrl = imageUrl('default.jpg');
