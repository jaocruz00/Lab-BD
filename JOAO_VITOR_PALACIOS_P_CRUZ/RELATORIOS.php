<?php
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Nome do Programa : exgatilhos[20261].php
# Objetivo         : Demonstração dos Gatilhos da Fase 4: (1) BIprofessores - CP automática
#                    no INSERT (MAX+5); (2) BDprofessores - bloqueio do DELETE quando há
#                    turmas vinculadas ao professor.
# Descrição        : Permite escolher entre os dois relatórios/gatilhos. Cada um executa a
#                    operação no banco (INSERT ou DELETE) e exibe o resultado em tela,
#                    com suporte a versão para impressão.
# Autor            : João Cruz
# Data de Criação  : 14/06/2026
# Histórico de Atualizações:
#              14/06/2026 - Versão inicial.
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# ALGORITMO: GATILHOS - Demonstração dos dois Gatilhos da Fase 4
#
# INÍCIO
#   1. Conectar ao banco de dados PostgreSQL.
#   2. Determinar a ação ($acao) e o bloco ($bloco) da requisição.
#
#   SE $acao == "Navegar" ENTÃO
#     Exibir dois botões: Relat.01 e Relat.02.
#
#   SE $acao == "Relat01" ENTÃO   [Gatilho BIprofessores - BEFORE INSERT]
#     SE $bloco == 1 ENTÃO
#       Exibir formulário com nome, nascimento e logradouro do novo professor.
#       Exibir botão "Incluir Professor".
#     SE $bloco == 2 OU 3 ENTÃO
#       Executar INSERT sem informar cpprofessor (gatilho atribui MAX(cpprofessor)+5).
#       Exibir tabela de professores. SE bloco=2: botão impressão. SE bloco=3: imprimir.
#
#   SE $acao == "Relat02" ENTÃO   [Gatilho BDprofessores - BEFORE DELETE]
#     SE $bloco == 1 ENTÃO
#       Exibir formulário com a lista de professores cadastrados.
#       Exibir botão "Excluir Professor".
#     SE $bloco == 2 OU 3 ENTÃO
#       Executar DELETE do professor escolhido (gatilho bloqueia se houver turmas vinculadas).
#       Exibir tabela de professores. SE bloco=2: botão impressão. SE bloco=3: imprimir.
# FIM
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
include("../toolskit.php");
conecta("PostgreSQL"); # Conectando com o Servidor PostgreSQL.
# Determinando valores para as variáveis de controle de execução do PA
$acao = ( ISSET($_REQUEST['acao'])  ) ? $_REQUEST['acao'] : "Navegar";
$bloco= ( ISSET($_REQUEST['bloco']) ) ? $_REQUEST['bloco'] : '1';
$salto= ( ISSET($_REQUEST['salto']  ) ? $_REQUEST['salto']+1 : '1');
# printf("\$acao=$acao<br>\$bloco=$bloco<br>");
$corfundo=( ($acao=="Relat01" || $acao=="Relat02") && $bloco==3) ? "#FFFFFF" : "#FFDEAD"; # FFDEAD=navajowhite
$corfonte="#000000"; # black
$relatorio=($acao=="Relat01") ? "Gatilho BIprofessores - CP Autom&aacute;tica" : (($acao=="Relat02") ? "Gatilho BDprofessores - Prote&ccedil;&atilde;o de Exclus&atilde;o" :"");
iniciapagina("$relatorio",$corfundo,$corfonte,$acao,$salto);
if ( !(($acao=="Relat01" || $acao=="Relat02") && $bloco==3) )
{
  printf("<table border=0 style=' border-collapse: collapse;' width=900>\n");
  printf("<tr><td align='justify'>\n");
  printf("Este &eacute; o sistema de demonstra&ccedil;&atilde;o dos Gatilhos da Fase 4.<br>O sistema &eacute; intuitivo. O design &eacute; minimalista e limpo.<br>\n");
  printf("Se um erro ocorrer no uso do Programa, comunique o Suporte T&eacute;cnico informando os dados que aparecem no final da p&aacute;gina.<br>\n");
  printf("</td>\n</tr>\n</table>\n");
}
switch (TRUE)
{
  case ( $acao=="Navegar" ):
  { # 1-Sessão que controla a NAVEGAÇÃO sobre os dados para manutenção
    printf(" <form action='./exgatilhos[20261].php' method='POST'>\n");
    printf("  <input type='hidden' name='acao'  value='$acao'>\n");
    printf("  <input type='hidden' name='bloco' value='$bloco'>\n");
    printf("  <input type='hidden' name='salto' value='$salto'>\n");
    printf("  <button type='submit' name='acao' value='Relat01' style='background-color: transparent;'>Relat.01</button>\n");
    printf("  <button type='submit' name='acao' value='Relat02' style='background-color: transparent;'>Relat.02</button>\n");
    printf(" </form>\n");
    break;
  } # 1-Fim da Sessão que controla a NAVEGAÇÃO sobre os dados para manutenção
  case ( $acao=="Relat01" ):
  { # 2-Sessão que controla a ação: Relat01 - Gatilho BIprofessores (BEFORE INSERT) ###############################
    switch (TRUE)
    { # 2.1 - Este é o seletor de controle da emissão do Relat01
      case ( $bloco==1 ):
      { # 2.1.1-Form para digitação dos dados do novo professor
        printf("<form action='./exgatilhos[20261].php' method='post'>\n");
        printf("<input type='hidden' name='acao'  value='$acao'>\n");
        printf("<input type='hidden' name='bloco' value='2'>\n");
        printf("<input type='hidden' name='salto' value='$salto'>\n");
        printf("<center>\n");
        printf("<table>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td COLSPAN=2>Inclus&atilde;o de Professor - o campo C&oacute;digo (CP) ser&aacute; gerado automaticamente pelo Gatilho BIprofessores</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td>Nome do Professor</td>          <td><input type='text' name='txnomeprofessor' size=50 maxlength=100></td></tr>\n");
        printf("<tr><td>Data de Nascimento</td>          <td><input type='date' name='dtnascimento' value='1980-01-01'></td></tr>\n");
        printf("<tr><td>Logradouro (moradia)</td><td>\n");
        $query="select cplogradouro, txlogrcompleto from logrcompleto order by txlogrcompleto";
        $sql = pg_query($nuconexao,$query);
        printf("<select name='celogradouro'>\n");
        while ($le = pg_fetch_array($sql))
        { #
          printf("<option value='$le[cplogradouro]'>$le[txlogrcompleto] - ($le[cplogradouro])</option>\n");
        }
        printf("</select>\n</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("   <tr><td></td><td>");
        botoes(FALSE,TRUE,TRUE,TRUE,"Incluir Professor",$salto);
        printf("</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("</table>\n");
        printf("</center>\n");
        printf("</form>\n");
        printf("</td></tr>\n");
        printf("</table>\n");
        break;
      } # 2.1.1-Fim da Execução form (digitação dos dados)
      case ( $bloco==2 || $bloco==3 ):
      { # 2.1.2-Executa o INSERT (gatilho calcula a CP) e monta a listagem para tela (2) ou impressão (3)
        $txnomeprofessor = $_REQUEST['txnomeprofessor'];
        $dtnascimento    = $_REQUEST['dtnascimento'];
        $celogradouro    = $_REQUEST['celogradouro'];
        # O cpprofessor NÃO é informado: o gatilho BIprofessores() atribui MAX(cpprofessor)+5 automaticamente.
        $query="INSERT INTO professores (txnomeprofessor, dtnascimento, dtcadprofessor, celogradouro)
                VALUES ('$txnomeprofessor', '$dtnascimento', CURRENT_DATE, '$celogradouro')";
        $sql = pg_query($nuconexao,$query);
        if ($sql)
        {
          printf("<br><font color='green'><b>Professor inclu&iacute;do com sucesso!</b></font><br>\n");
          printf("O Gatilho BIprofessores atribuiu o c&oacute;digo (CP) automaticamente.<br><br>\n");
        }
        else
        {
          printf("<br><font color='red'><b>Erro na inclus&atilde;o:</b> %s</font><br><br>\n", pg_last_error($nuconexao));
        }
        $qlist="select cpprofessor, txnomeprofessor, dtnascimento, dtcadprofessor from professores order by cpprofessor";
        $sql = pg_query($nuconexao,$qlist);
        printf("<table border=1 style='padding: 0px; border: 1px solid black; border-collapse: collapse;'>\n");
        printf("<tr bgcolor='lightblue'><td>C&oacute;digo (CP)</td>
                                        <td>Nome do Professor</td>
                                        <td>Nascimento</td>
                                        <td>Cadastro</td></tr>\n");
        $cor="WHITE";
        while ($le = pg_fetch_array($sql))
        { # 2.1.2.1 ------------------------------------------------------------------------------------------------
          printf("<tr bgcolor='$cor'><td>$le[cpprofessor]</td>
                                     <td>$le[txnomeprofessor]</td>
                                     <td>%s</td>
                                     <td>%s</td></tr>\n",date_format(date_create($le['dtnascimento']), 'd-m-Y'),date_format(date_create($le['dtcadprofessor']), 'd-m-Y'));
          $cor=( $cor == "WHITE" ) ? "LIGHTGREEN" : "WHITE";
        } # 2.1.2.1 ------------------------------------------------------------------------------------------------
        printf("</table>\n");
        if ( $bloco==2 )
        { # 2.1.2.2 vamos montar o botão para impressão ----------------------------------------------------------
          printf("<form action='./exgatilhos[20261].php' method='POST' target='_NEW'>\n");
          printf("<input type='hidden' name='acao'  value='$acao'>\n");
          printf("<input type='hidden' name='bloco' value='3'>\n");
          printf("<input type='hidden' name='salto' value='$salto'>\n");
          botoes(TRUE,TRUE,TRUE,FALSE,"Gerar para Impress&atilde;o",$salto);
          printf("O mesmo relat&oacute;rio ser&aacute; montado em uma janela!<br>Depois voc&ecirc; pode escolher a impress&atilde;o pelo navegador.\n");
          printf("</form>\n");
        } # 2.1.2.2 ------------------------------------------------------------------------------------------------
        else
        { # 2.1.2.3 - O fluxo passa por aqui quando o $bloco valer 3 --------------------------------------------
          printf("<hr>\nDepois de Imprimir rasgue na linha acima<br>\n");
          printf("<input type='submit' value='Imprimir' onclick='javascript:window.print();'>");
          $ano=date('Y');
          printf("</dir>\n <hr> \n");
          printf("<font size=2 color='gray'>&copy; Copyright $ano, FATEC Ourinhos - Copie, divulgue, mas indique sempre quem fez!\n</titulo>\n");
        } # 2.1.2.3 ------------------------------------------------------------------------------------------------
        break;
      } # 2.2-Fim da Listagem
    }
    break;
  } # 2-Fim da Sessão que controla a ação: Relat01 ###############################################################
  case ( $acao=="Relat02" ):
  { # 3-Sessão que controla a ação: Relat02 - Gatilho BDprofessores (BEFORE DELETE) ###############################
    switch (TRUE)
    { # 3.1 - Este é o seletor de controle da emissão do Relat02
      case ( $bloco==1 ):
      { # 3.1.1-Form para escolha do professor a ser excluído
        printf("<form action='./exgatilhos[20261].php' method='post'>\n");
        printf("<input type='hidden' name='acao'  value='$acao'>\n");
        printf("<input type='hidden' name='bloco' value='2'>\n");
        printf("<input type='hidden' name='salto' value='$salto'>\n");
        printf("<center>\n");
        printf("<table>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td COLSPAN=2>Escolha o Professor para exclus&atilde;o - o Gatilho BDprofessores impedir&aacute; a opera&ccedil;&atilde;o se houver turmas vinculadas</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td>Professor</td><td>\n");
        $query="select cpprofessor, txnomeprofessor from professores order by txnomeprofessor";
        $sql = pg_query($nuconexao,$query);
        printf("<select name='cpprofessor'>\n");
        while ($le = pg_fetch_array($sql))
        { #
          printf("<option value='$le[cpprofessor]'>$le[txnomeprofessor] - ($le[cpprofessor])</option>\n");
        }
        printf("</select>\n</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("   <tr><td></td><td>");
        botoes(FALSE,TRUE,TRUE,FALSE,"Excluir Professor",$salto);
        printf("</td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("</table>\n");
        printf("</center>\n");
        printf("</form>\n");
        printf("</td></tr>\n");
        printf("</table>\n");
        break;
      } # 3.1.1-Fim da Execução form (escolha do professor)
      case ( $bloco==2 || $bloco==3 ):
      { # 3.1.2-Executa o DELETE (gatilho pode bloquear) e monta a listagem para tela (2) ou impressão (3)
        $cpprofessor = $_REQUEST['cpprofessor'];
        $qnome = pg_query($nuconexao,"select txnomeprofessor from professores where cpprofessor='$cpprofessor'");
        $lenome = pg_fetch_array($qnome);
        $txnome = $lenome['txnomeprofessor'];
        printf("Tentando excluir: <b>$txnome (CP=$cpprofessor)</b><br><br>\n");
        # O gatilho BDprofessores() dispara ANTES do DELETE e cancela a operação (RAISE EXCEPTION) se houver turmas vinculadas.
        $query="DELETE FROM professores WHERE cpprofessor='$cpprofessor'";
        $sql = @pg_query($nuconexao,$query); # @ suprime warning do PHP para capturar o erro via pg_last_error
        if ($sql)
        {
          printf("<font color='green'><b>Professor exclu&iacute;do com sucesso!</b></font><br><br>\n");
        }
        else
        {
          printf("<font color='red'><b>Exclus&atilde;o bloqueada pelo Gatilho BDprofessores!</b></font><br>\n");
          printf("Mensagem do banco: <i>%s</i><br><br>\n", pg_last_error($nuconexao));
        }
        $qlist="select cpprofessor, txnomeprofessor, dtnascimento, dtcadprofessor from professores order by cpprofessor";
        $sql = pg_query($nuconexao,$qlist);
        printf("<table border=1 style='padding: 0px; border: 1px solid black; border-collapse: collapse;'>\n");
        printf("<tr bgcolor='lightblue'><td>C&oacute;digo (CP)</td>
                                        <td>Nome do Professor</td>
                                        <td>Nascimento</td>
                                        <td>Cadastro</td></tr>\n");
        $cor="WHITE";
        while ($le = pg_fetch_array($sql))
        { # 3.1.2.1 ------------------------------------------------------------------------------------------------
          printf("<tr bgcolor='$cor'><td>$le[cpprofessor]</td>
                                     <td>$le[txnomeprofessor]</td>
                                     <td>%s</td>
                                     <td>%s</td></tr>\n",date_format(date_create($le['dtnascimento']), 'd-m-Y'),date_format(date_create($le['dtcadprofessor']), 'd-m-Y'));
          $cor=( $cor == "WHITE" ) ? "LIGHTGREEN" : "WHITE";
        } # 3.1.2.1 ------------------------------------------------------------------------------------------------
        printf("</table>\n");
        if ( $bloco==2 )
        { # 3.1.2.2 vamos montar o botão para impressão ----------------------------------------------------------
          printf("<form action='./exgatilhos[20261].php' method='POST' target='_NEW'>\n");
          printf("<input type='hidden' name='acao'        value='$acao'>\n");
          printf("<input type='hidden' name='bloco'       value='3'>\n");
          printf("<input type='hidden' name='salto'       value='$salto'>\n");
          printf("<input type='hidden' name='cpprofessor' value='$cpprofessor'>\n");
          botoes(TRUE,TRUE,TRUE,FALSE,"Gerar para Impress&atilde;o",$salto);
          printf("O mesmo relat&oacute;rio ser&aacute; montado em uma janela!<br>Depois voc&ecirc; pode escolher a impress&atilde;o pelo navegador.\n");
          printf("</form>\n");
        } # 3.1.2.2 ------------------------------------------------------------------------------------------------
        else
        { # 3.1.2.3 - O fluxo passa por aqui quando o $bloco valer 3 --------------------------------------------
          printf("<hr>\nDepois de Imprimir rasgue na linha acima<br>\n");
          printf("<input type='submit' value='Imprimir' onclick='javascript:window.print();'>");
          $ano=date('Y');
          printf("</dir>\n <hr> \n");
          printf("<font size=2 color='gray'>&copy; Copyright $ano, FATEC Ourinhos - Copie, divulgue, mas indique sempre quem fez!\n</titulo>\n");
        } # 3.1.2.3 ------------------------------------------------------------------------------------------------
        break;
      } # 3.2-Fim da Listagem
    }
    break;
  } # 3-Fim da Sessão que controla a ação: Relat02 ###############################################################
}
terminapagina("Gatilhos da Fase 4","exgatilhos[20261].php",TRUE);
?>