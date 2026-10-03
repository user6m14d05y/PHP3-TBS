import { readFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const allowedKeys = new Set(['VITE_SITE_URL', 'VITE_API_URL', 'SEO_API_URL']);
const envFiles = ['.env', '.env.local', '../backend/.env'];

const cleanUrl = (value = '') => String(value || '').replace(/\/+$/, '');

const stripQuotes = (value = '') => {
  const trimmed = value.trim();
  if (
    (trimmed.startsWith('"') && trimmed.endsWith('"')) ||
    (trimmed.startsWith("'") && trimmed.endsWith("'"))
  ) {
    return trimmed.slice(1, -1);
  }

  return trimmed;
};

const parseEnvFile = (content = '') => {
  const parsed = {};

  content.split(/\r?\n/).forEach((line) => {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) return;

    const separatorIndex = trimmed.indexOf('=');
    if (separatorIndex === -1) return;

    const key = trimmed.slice(0, separatorIndex).trim();
    const value = stripQuotes(trimmed.slice(separatorIndex + 1));

    if (allowedKeys.has(key)) {
      parsed[key] = value;
    }
  });

  return parsed;
};

export const loadSeoEnv = async () => {
  const env = { ...process.env };

  for (const file of envFiles) {
    try {
      const content = await readFile(resolve(process.cwd(), file), 'utf8');
      const parsed = parseEnvFile(content);

      Object.entries(parsed).forEach(([key, value]) => {
        if (!env[key]) {
          env[key] = value;
        }
      });
    } catch {
      // Optional env files may not exist in every environment.
    }
  }

  return {
    siteUrl: cleanUrl(env.VITE_SITE_URL),
    apiUrl: cleanUrl(env.VITE_API_URL),
    seoApiUrl: cleanUrl(env.SEO_API_URL),
  };
};

export const buildSeoApiCandidates = ({ apiUrl = '', seoApiUrl = '' }) => {
  return [
    seoApiUrl,
    'http://webserver',
    'http://webserver:80',
    apiUrl,
  ]
    .filter(Boolean)
    .map(cleanUrl);
};
