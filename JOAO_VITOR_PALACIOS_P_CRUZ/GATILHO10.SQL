-- ============================================================
-- Nome do Programa : GATILHOS10.SQL
-- Objetivo         : Gatilho que impede a marcação de consulta
--                    para um funcionário que não reside na mesma
--                    cidade da clínica do médico (Regra 10).
-- Autor            : João Vitor Palacios Paes da Cruz
-- Data de Criação  : 14/05/2026
-- Histórico de Atualizações:
--   14/05/2026 - Versão inicial
-- ============================================================

/*
  ALGORITMO: fn_regra10_medico_cidade

  INÍCIO
    Para cada UPDATE na tabela consultas:
      1. SE o novo funcionarioid NÃO É NULO E foi alterado em relação
         ao valor anterior (paciente sendo marcado/trocado) ENTÃO:

           a. Buscar a cidade da clínica do médico percorrendo:
                medicos.logradouroclinicaid -> logradouros.cidadeid
              usando o medicoid da consulta (NEW.medicoid).

           b. Buscar a cidade de moradia do funcionário percorrendo:
                funcionarios.logradouroid -> logradouros.cidadeid
              usando o novo funcionarioid (NEW.funcionarioid).

           c. SE as cidades forem diferentes OU alguma não for
              encontrada (valor nulo) ENTÃO
                LANÇAR EXCEÇÃO informando que o médico não pode
                atender o funcionário por diferença de cidade.
              FIM SE

      FIM SE
      RETORNAR NEW para permitir a atualização.
  FIM
*/

CREATE OR REPLACE FUNCTION fn_regra10_medico_cidade()
RETURNS TRIGGER AS $$
DECLARE
    v_cidade_medico       INTEGER;
    v_cidade_funcionario  INTEGER;
BEGIN
    IF NEW.funcionarioid IS NOT NULL
       AND (OLD.funcionarioid IS DISTINCT FROM NEW.funcionarioid)
    THEN
        SELECT l.cidadeid
          INTO v_cidade_medico
          FROM medicos m
          JOIN logradouros l ON l.idlogradouro = m.logradouroclinicaid
         WHERE m.idmedico = NEW.medicoid;

        SELECT l.cidadeid
          INTO v_cidade_funcionario
          FROM funcionarios f
          JOIN logradouros l ON l.idlogradouro = f.logradouroid
         WHERE f.idfuncionario = NEW.funcionarioid;

        IF v_cidade_medico IS NULL
           OR v_cidade_funcionario IS NULL
           OR v_cidade_medico <> v_cidade_funcionario
        THEN
            RAISE EXCEPTION
                'O médico % não pode atender o funcionário % pois a cidade de moradia do paciente é diferente da cidade da clínica do médico. Marcação cancelada.',
                NEW.medicoid, NEW.funcionarioid;
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ============================================================

CREATE OR REPLACE TRIGGER tg_regra10_medico_cidade
BEFORE UPDATE ON consultas
FOR EACH ROW
EXECUTE FUNCTION fn_regra10_medico_cidade();
