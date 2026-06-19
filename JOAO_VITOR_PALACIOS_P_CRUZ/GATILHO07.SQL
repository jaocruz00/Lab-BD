-- ============================================================
-- Nome do Programa : GATILHOS07.SQL
-- Objetivo         : Gatilho que limita a venda de passagens
--                    à capacidade de poltronas do ônibus para
--                    uma determinada viagem (Regra 7).
-- Autor            : João Vitor Palacios Paes da Cruz
-- Data de Criação  : 14/05/2026
-- Histórico de Atualizações:
--   14/05/2026 - Versão inicial
-- ============================================================

/*
  ALGORITMO: fn_regra07_limite_passagens

  INÍCIO
    Para cada INSERT na tabela passagens:
      1. Buscar a capacidade de poltronas (qtcapacidade) na tabela
         veiculos usando o código do ônibus (NEW.onibusid).
      2. Contar quantas passagens já foram vendidas para a mesma
         combinação de viagem (NEW.viagemid) e ônibus (NEW.onibusid).
      3. SE a quantidade de passagens já vendidas >= capacidade do ônibus ENTÃO
           LANÇAR EXCEÇÃO informando que a capacidade máxima foi atingida.
         FIM SE
      4. RETORNAR NEW para permitir a inserção.
  FIM
*/

CREATE OR REPLACE FUNCTION fn_regra07_limite_passagens()
RETURNS TRIGGER AS $$
DECLARE
    v_capacidade    INTEGER;
    v_qtpassagens   INTEGER;
BEGIN
    SELECT qtcapacidade
      INTO v_capacidade
      FROM veiculos
     WHERE idveiculo = NEW.onibusid;

    SELECT COUNT(*)
      INTO v_qtpassagens
      FROM passagens
     WHERE viagemid = NEW.viagemid
       AND onibusid = NEW.onibusid;

    IF v_qtpassagens >= v_capacidade THEN
        RAISE EXCEPTION
            'Capacidade máxima de % poltronas do ônibus % atingida para a viagem %. Venda de passagem cancelada.',
            v_capacidade, NEW.onibusid, NEW.viagemid;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ============================================================

CREATE OR REPLACE TRIGGER tg_regra07_limite_passagens
BEFORE INSERT ON passagens
FOR EACH ROW
EXECUTE FUNCTION fn_regra07_limite_passagens();
