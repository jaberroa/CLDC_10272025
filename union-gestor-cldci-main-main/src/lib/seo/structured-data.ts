/**
 * Structured data (JSON-LD) generators for SEO
 * Helps search engines understand the content better
 */

export interface OrganizationStructuredData {
  name: string;
  url: string;
  logo?: string;
  description?: string;
  address?: {
    streetAddress: string;
    addressLocality: string;
    addressRegion: string;
    postalCode: string;
    addressCountry: string;
  };
  contactPoint?: {
    telephone: string;
    contactType: string;
    email?: string;
  };
  sameAs?: string[];
}

export function generateOrganizationSchema(data: OrganizationStructuredData) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: data.name,
    url: data.url,
    logo: data.logo,
    description: data.description,
    address: data.address ? {
      '@type': 'PostalAddress',
      ...data.address,
    } : undefined,
    contactPoint: data.contactPoint ? {
      '@type': 'ContactPoint',
      ...data.contactPoint,
    } : undefined,
    sameAs: data.sameAs,
  };
}

export interface PersonStructuredData {
  name: string;
  jobTitle?: string;
  image?: string;
  description?: string;
  email?: string;
  url?: string;
}

export function generatePersonSchema(data: PersonStructuredData) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Person',
    name: data.name,
    jobTitle: data.jobTitle,
    image: data.image,
    description: data.description,
    email: data.email,
    url: data.url,
  };
}

export interface ArticleStructuredData {
  headline: string;
  description: string;
  image?: string;
  author?: string;
  datePublished?: string;
  dateModified?: string;
  publisher?: {
    name: string;
    logo?: string;
  };
}

export function generateArticleSchema(data: ArticleStructuredData) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: data.headline,
    description: data.description,
    image: data.image,
    author: data.author ? {
      '@type': 'Person',
      name: data.author,
    } : undefined,
    datePublished: data.datePublished,
    dateModified: data.dateModified,
    publisher: data.publisher ? {
      '@type': 'Organization',
      name: data.publisher.name,
      logo: data.publisher.logo ? {
        '@type': 'ImageObject',
        url: data.publisher.logo,
      } : undefined,
    } : undefined,
  };
}

export interface BreadcrumbItem {
  name: string;
  item: string;
}

export function generateBreadcrumbSchema(items: BreadcrumbItem[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.item,
    })),
  };
}
