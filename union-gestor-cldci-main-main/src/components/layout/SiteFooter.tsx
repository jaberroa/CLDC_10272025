export const SiteFooter = () => {
  return (
    <footer className="border-t bg-background">
      <div className="container mx-auto py-8 text-center text-sm text-muted-foreground">
        <p>
          © {new Date().getFullYear()} Círculo de Locutores Dominicanos
          Colegiado, Inc. — CLDCI
        </p>
      </div>
    </footer>
  );
};
