import { Helmet } from "react-helmet-async";
import { useLocation } from "react-router-dom";

interface SEOProps {
  title: string;
  description: string;
  jsonLd?: Record<string, any>;
}

export const SEO = ({ title, description, jsonLd }: SEOProps) => {
  const { pathname } = useLocation();
  const canonical = typeof window !== "undefined" ? window.location.origin + pathname : pathname;

  return (
    <Helmet prioritizeSeoTags>
      <title>{title}</title>
      <meta name="description" content={description} />
      <link rel="canonical" href={canonical} />
      {jsonLd && (
        <script type="application/ld+json">
          {JSON.stringify(jsonLd)}
        </script>
      )}
    </Helmet>
  );
};
