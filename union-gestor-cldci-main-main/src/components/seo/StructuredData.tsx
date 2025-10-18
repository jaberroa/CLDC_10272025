import { Helmet } from 'react-helmet-async';

/**
 * Structured Data Component
 * Renders JSON-LD structured data in the document head
 */

interface StructuredDataProps {
  data: object;
}

export const StructuredData = ({ data }: StructuredDataProps) => {
  return (
    <Helmet>
      <script type="application/ld+json">
        {JSON.stringify(data)}
      </script>
    </Helmet>
  );
};
