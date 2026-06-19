<?php
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Nome do Programa.: relatorio01.php
# Objetivo.........: Relatório de Clientes que compraram todos os produtos da empresa num período.
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

require_once("../toolskit.php");
conecta("PostgreSQL"); 

$acao = ( ISSET($_REQUEST['acao'])  ) ? $_REQUEST['acao'] : "Navegar";
$bloco= ( ISSET($_REQUEST['bloco']) ) ? $_REQUEST['bloco'] : '1';
$salto= ( ISSET($_REQUEST['salto']) ) ? $_REQUEST['salto']+1 : '1';   

$corfundo=( $acao=="Relat01" && $bloco==3) ? "#FFFFFF" : "#EBEBEB"; 
$corfonte="#000000"; 

iniciapagina("Clientes Fidelizados (Todos os Produtos)", $corfundo, $corfonte, $acao, $salto);

if ( !($acao=="Relat01" && $bloco==3) )
{
  printf("<table border=0 style=' border-collapse: collapse;' width=900>\n");
  printf("<tr><td align='justify'>\n");
  printf("Este &eacute; o relat&oacute;rio de Divis&atilde;o Relacional de Clientes.<br>\n");
  printf("Se um erro ocorrer no uso do Programa, comunique o Suporte T&eacute;cnico informando os dados que aparecem no final da p&aacute;gina.<br>\n");
  printf("</td>\n</tr>\n</table>\n");
}

switch (TRUE)
{
  case ( $acao=="Navegar" ):
  { 
    printf(" <form action='./relatorio01.php' method='POST'>\n");
    printf("  <input type='hidden' name='acao'  value='$acao'>\n");
    printf("  <input type='hidden' name='bloco' value='$bloco'>\n");
    printf("  <input type='hidden' name='salto' value='$salto'>\n");
    printf("  <button type='submit' name='acao' value='Relat01' style='background-color: transparent;'>Aceder Relat&oacute;rio 01</button>\n");
    printf(" </form>\n");
    break;
  } 
  
  case ( $acao=="Relat01" ):
  { 
    switch (TRUE)
    { 
      case ( $bloco==1 ):
      { 
        printf("<form action='./relatorio01.php' method='post'>\n");
        printf("<input type='hidden' name='acao'  value='$acao'>\n");
        printf("<input type='hidden' name='bloco' value='2'>\n");
        printf("<input type='hidden' name='salto' value='$salto'>\n");
        printf("<center>\n<table>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td COLSPAN=2><b>Escolha a ORDENAÇÃO dos dados do relat&oacute;rio</b></td></tr>\n");
        printf("<tr><td>C&oacute;digo do Cliente</td>   <td><INPUT TYPE=RADIO NAME='ordem' VALUE='c.idcliente' CHECKED></td></tr>\n");
        printf("<tr><td>Nome do Cliente</td>            <td><INPUT TYPE=RADIO NAME='ordem' VALUE='c.txnomecliente'></td></tr>\n");
        printf("<tr><td>Data de Cadastro</td>           <td><INPUT TYPE=RADIO NAME='ordem' VALUE='c.dtcadcliente'></td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td COLSPAN=2><b>Filtro de SELEÇÃO - Per&iacute;odo de Compras</b></td></tr>\n");
        
        $dtini=date("Y-m-d", strtotime("-1 year"));
        $dtfim=date("Y-m-d");
        printf("<tr><td>Data da Venda:</td><td>de <input type='date' name='dtvendaini' value='$dtini'> at&eacute; <input type='date' name='dtvendafim' value='$dtfim'></td></tr>");
        printf("<tr><td></td><td>");
        
        botoes(FALSE,TRUE,TRUE,TRUE,"Gerar Listagem",$salto);
        printf("</td></tr>\n<tr><td COLSPAN=2><HR></td></tr>\n</table>\n</center>\n</form>\n</td></tr>\n</table>\n");
        break;
      } 
      
      case ( $bloco==2 || $bloco==3 ):
      { 
        $ordem=$_REQUEST['ordem'];
        
        $query="SELECT c.idcliente, c.txnomecliente, c.txrazaosocial, c.dtcadcliente
                FROM clientes AS c
                WHERE NOT EXISTS (
                    SELECT p.idproduto FROM produtos AS p
                    WHERE NOT EXISTS (
                        SELECT 1 FROM nfvendas AS nf
                        INNER JOIN nfvendasitens AS nfi ON nf.idnunfvenda = nfi.nunfvendaid
                        WHERE nf.clienteid = c.idcliente
                          AND nfi.produtoid = p.idproduto
                          AND nf.dtvenda BETWEEN '$_REQUEST[dtvendaini]' AND '$_REQUEST[dtvendafim]'
                    )
                )
                ORDER BY $ordem";
                
        $sql = pg_query($nuconexao, $query);
        
        printf("<table border=1 style='padding: 0px; border: 1px solid black; border-collapse: collapse; width: 100%%;'>\n");
        printf("<tr bgcolor='lightblue'><td>C&oacute;digo</td>
                                        <td>Nome do Cliente</td>
                                        <td>Raz&atilde;o Social</td>
                                        <td>Dt. Cadastro</td></tr>\n");
        $cor="WHITE";
        while ($le = pg_fetch_array($sql))
        { 
          printf("<tr bgcolor='$cor'><td align='center'>%s</td>
                                     <td>%s</td>
                                     <td>%s</td>
                                     <td align='center'>%s</td></tr>\n",
                 $le['idcliente'], $le['txnomecliente'], $le['txrazaosocial'], 
                 date_format(date_create($le['dtcadcliente']), 'd-m-Y'));
          $cor=( $cor == "WHITE" ) ? "LIGHTGREEN" : "WHITE";
        } 
        printf("</table>\n");
        
        if ( $bloco==2 )
        { 
          printf("<br><form action='./relatorio01.php' method='POST' target='_NEW'>\n");
          printf("<input type='hidden' name='acao'  value='$acao'>\n");
          printf("<input type='hidden' name='bloco' value='3'>\n");
          printf("<input type='hidden' name='salto' value='$salto'>\n");
          printf("<input type='hidden' name='ordem' value='$ordem'>\n");
          printf("<input type='hidden' name='dtvendaini' value='$_REQUEST[dtvendaini]'>\n");
          printf("<input type='hidden' name='dtvendafim' value='$_REQUEST[dtvendafim]'>\n");
          botoes(TRUE,TRUE,TRUE,FALSE,"Gerar para Impress&atilde;o",$salto);
          printf("O mesmo relat&oacute;rio ser&aacute; montado numa janela para impress&atilde;o.\n</form>\n");
        } 
        else
        { 
          printf("<hr>\nDepois de Imprimir rasgue na linha acima<br>\n");
          printf("<input type='submit' value='Imprimir' onclick='javascript:window.print();'>");
          $ano=date('Y');
          printf("</dir>\n <hr> \n<font size=2 color='gray'>&copy; Copyright $ano, FATEC Ourinhos\n</titulo>\n");
        }
        break;
      }
    }
    break;
  }
}
terminapagina("Clientes Fidelizados", "relatorio01.php", TRUE);
?>