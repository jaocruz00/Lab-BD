<?php
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# Nome do Programa.: relatorio02.php
# Objetivo.........: Relatório dos 5 funcionários com a maior contagem de participação em empresas diferentes.
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

require_once("../toolskit.php");
conecta("PostgreSQL"); 

$acao = ( ISSET($_REQUEST['acao'])  ) ? $_REQUEST['acao'] : "Navegar";
$bloco= ( ISSET($_REQUEST['bloco']) ) ? $_REQUEST['bloco'] : '1';
$salto= ( ISSET($_REQUEST['salto']) ) ? $_REQUEST['salto']+1 : '1';   

$corfundo=( $acao=="Relat02" && $bloco==3) ? "#FFFFFF" : "#EBEBEB"; 
$corfonte="#000000"; 

iniciapagina("Top 5 Func. Rotatividade", $corfundo, $corfonte, $acao, $salto);

if ( !($acao=="Relat02" && $bloco==3) )
{
  printf("<table border=0 style=' border-collapse: collapse;' width=900>\n");
  printf("<tr><td align='justify'>\n");
  printf("Este &eacute; o relat&oacute;rio de An&aacute;lise Profissional (Top 5 Empresas).<br>\n");
  printf("Se um erro ocorrer no uso do Programa, comunique o Suporte T&eacute;cnico informando os dados que aparecem no final da p&aacute;gina.<br>\n");
  printf("</td>\n</tr>\n</table>\n");
}

switch (TRUE)
{
  case ( $acao=="Navegar" ):
  { 
    printf(" <form action='./relatorio02.php' method='POST'>\n");
    printf("  <input type='hidden' name='acao'  value='$acao'>\n");
    printf("  <input type='hidden' name='bloco' value='$bloco'>\n");
    printf("  <input type='hidden' name='salto' value='$salto'>\n");
    printf("  <button type='submit' name='acao' value='Relat02' style='background-color: transparent;'>Aceder Relat&oacute;rio 02</button>\n");
    printf(" </form>\n");
    break;
  }

  case ( $acao=="Relat02" ):
  { 
    switch (TRUE)
    {
      case ( $bloco==1 ):
      { 
        printf("<form action='./relatorio02.php' method='post'>\n");
        printf("<input type='hidden' name='acao'  value='$acao'>\n");
        printf("<input type='hidden' name='bloco' value='2'>\n");
        printf("<input type='hidden' name='salto' value='$salto'>\n");
        printf("<center>\n<table>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td COLSPAN=2><b>Escolha a ORDENAÇÃO dos dados em tela</b></td></tr>\n");
        printf("<tr><td>Total de Empresas do Top 5</td> <td><INPUT TYPE=RADIO NAME='ordem' VALUE='tf.qtd_empresas DESC, hp.dtinicio DESC' CHECKED></td></tr>\n");
        printf("<tr><td>Nome do Funcion&aacute;rio</td> <td><INPUT TYPE=RADIO NAME='ordem' VALUE='f.txprenomes, hp.dtinicio DESC'></td></tr>\n");
        printf("<tr><td>Data de In&iacute;cio </td>     <td><INPUT TYPE=RADIO NAME='ordem' VALUE='hp.dtinicio DESC, tf.qtd_empresas DESC'></td></tr>\n");
        printf("<tr><td COLSPAN=2><HR></td></tr>\n");
        printf("<tr><td></td><td>");
        botoes(FALSE,TRUE,TRUE,TRUE,"Gerar Listagem Top 5",$salto);
        printf("</td></tr>\n<tr><td COLSPAN=2><HR></td></tr>\n</table>\n</center>\n</form>\n</td></tr>\n</table>\n");
        break;
      }
      
      case ( $bloco==2 || $bloco==3 ):
      { 
        $ordem=$_REQUEST['ordem'];
        
        $query="WITH top_func AS (
                    SELECT funcionarioid, COUNT(DISTINCT empresaid) AS qtd_empresas
                    FROM historicoprofissional
                    WHERE empresaid IS NOT NULL
                    GROUP BY funcionarioid
                    ORDER BY qtd_empresas DESC
                    LIMIT 5
                )
                SELECT f.idfuncionario, f.txprenomes, f.txsobrenome, tf.qtd_empresas, 
                       e.idempresa, e.txnomeusual, hp.dtinicio, hp.dttermino
                FROM top_func tf
                INNER JOIN funcionarios f ON tf.funcionarioid = f.idfuncionario
                INNER JOIN historicoprofissional hp ON f.idfuncionario = hp.funcionarioid
                INNER JOIN empresas e ON hp.empresaid = e.idempresa
                ORDER BY $ordem";
                ''
        $sql = pg_query($nuconexao, $query);
        
        printf("<table border=1 style='padding: 0px; border: 1px solid black; border-collapse: collapse; width: 100%%;'>\n");
        printf("<tr bgcolor='lightblue'><td>Funcion&aacute;rio</td>
                                        <td>Qtd. Empresas</td>
                                        <td>Empresa</td>
                                        <td>Dt. In&iacute;cio</td>
                                        <td>Dt. T&eacute;rmino</td></tr>\n");
        $cor="WHITE";
        while ($le = pg_fetch_array($sql))
        { 
          printf("<tr bgcolor='$cor'><td>%s - %s %s</td>
                                     <td align='center'><b>%s</b></td>
                                     <td>%s - %s</td>
                                     <td align='center'>%s</td>
                                     <td align='center'>%s</td></tr>\n",
                 $le['idfuncionario'], $le['txprenomes'], $le['txsobrenome'],
                 $le['qtd_empresas'],
                 $le['idempresa'], $le['txnomeusual'],
                 date_format(date_create($le['dtinicio']), 'd-m-Y'),
                 date_format(date_create($le['dttermino']), 'd-m-Y'));
          $cor=( $cor == "WHITE" ) ? "LIGHTGREEN" : "WHITE";
        } 
        printf("</table>\n");
        
        if ( $bloco==2 )
        {
          printf("<br><form action='./relatorio02.php' method='POST' target='_NEW'>\n");
          printf("<input type='hidden' name='acao'  value='$acao'>\n");
          printf("<input type='hidden' name='bloco' value='3'>\n");
          printf("<input type='hidden' name='salto' value='$salto'>\n");
          printf("<input type='hidden' name='ordem' value='$ordem'>\n");
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
terminapagina("Análise Profissional", "relatorio02.php", TRUE);
?>