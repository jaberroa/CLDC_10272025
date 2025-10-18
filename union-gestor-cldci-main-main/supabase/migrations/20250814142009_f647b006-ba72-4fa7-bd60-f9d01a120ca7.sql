-- Create database function to safely increment vote count
CREATE OR REPLACE FUNCTION increment_vote_count(election_id UUID)
RETURNS void
LANGUAGE plpgsql
SECURITY DEFINER
AS $$
BEGIN
  UPDATE elecciones 
  SET votos_totales = COALESCE(votos_totales, 0) + 1
  WHERE id = election_id;
END;
$$;