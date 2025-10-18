-- Create submissions table for Registro y Adecuación
create table if not exists public.seccional_submissions (
  id uuid primary key default gen_random_uuid(),
  seccional_nombre text not null,
  directiva text,
  miembros_csv_path text,
  actas_paths text[],
  miembros_min_ok boolean not null default false,
  miembros_contados integer not null default 0,
  observaciones text,
  created_by uuid,
  created_at timestamptz not null default now()
);

-- Enable RLS
alter table public.seccional_submissions enable row level security;

-- TESTING POLICIES (anon). Note: tighten before production.
create policy "Public can insert submissions for testing"
  on public.seccional_submissions
  for insert
  to anon
  with check (true);

create policy "Public can view submissions for testing"
  on public.seccional_submissions
  for select
  to anon
  using (true);

-- Optional index for listing by recency
create index if not exists idx_seccional_submissions_created_at
  on public.seccional_submissions (created_at desc);

-- Storage bucket for expedientes
insert into storage.buckets (id, name, public)
values ('expedientes', 'expedientes', true)
on conflict (id) do nothing;

-- Storage policies for testing (anon)
create policy "Public can view expedientes files"
  on storage.objects
  for select
  to anon
  using (bucket_id = 'expedientes');

create policy "Public can upload expedientes files"
  on storage.objects
  for insert
  to anon
  with check (bucket_id = 'expedientes');