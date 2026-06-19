<?php

#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function iniciapagina($objeto,$cordefundo,$corfonte,$acao,$salto)
{ # Função.....: iniciapagina
  # Parametros.: Cor de fundo da página ($cordefundo), a cor do fonte das telas ($corfonte), texto com a funcionalidade em execução ($acao).
  # Descrição..: Emite as TAGS que iniciam uma tela com a cor de fundo padrao, alinha o texto com um TAB para a direita e a determina o fonte do projeto.
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2009-09-23
  # Atualização: 2018-04-27 - Tirei a variável $titulo colocando os operadores ternários dentro do printf();
  #              2018-09-17 - Escrevi o segmento de código que define a classe button em CSS para ser usado em tags de <form> ou <a href...>
  #################################################################################################################################################################################
  printf("<html xml:lang='pt-BR' lang='pt-BR' dir='ltr'>\n");
  # declara o conjunto de caracteres universais (UTF-8)
  printf("<head>\n");
  printf(" <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n"); # ISO-8859-1
  # Aqui especificamos alguns seletores CSS para formatação de alguns elementos HTML. Como o conteúdo é pequeno fica mais interessante lançar em corpo de função.
  # Note: - Os valores de alguns atributos foram passados como argumentos de parâmetros desta função.
  #       - A parametrização de valores de atributos em componentes CSS (seletores, classes, identificadores ou grupos de seletores) torna a estrutura
  #         do arquivo externo CSS mais complexa e a identificação de quais valores devem ser evocados do código HTML mais complicada de ser resolvido pelo segmento PHP.
  #         POR ISSO usou-se o modo interno na especificação de componentes CSS.
  printf(" <style>\n");
  printf("  body   { background-color: $cordefundo; border: none; padding: 0px 0px; text-align: center; text-decoration: none; display: inline; margin: 0px 10px; cursor: pointer; font-size: 14px; color: black; overflow: scroll; }\n");
  printf("  button { background-color: $cordefundo; border: none; padding: 0px 0px; text-align: center; text-decoration: none; display: inline; margin: 0px 10px; cursor: pointer; font-size: 16px; color: black; }\n");
  printf("  .button{ background-color: $cordefundo; border: none; padding: 0px 0px; text-align: center; text-decoration: none; display: inline; margin: 0px 10px; cursor: pointer; font-size: 16px; color: black; }\n");
  printf(" </style>\n");
  printf("</head>\n");
  # inicia o corpo da pagina com a cor indicada no parametro
  printf("<body>\n");
  # A função recebe como par?metro a cor do fonte usado nos textos das telas (exceção dos destaques)
  # determina a fonte TAHOMA com tamanho 3
  printf("<font face='tahoma' size=3 color='$corfonte'>\n");
  # posiciona os textos com um TAB para a direita. Este alinhamento melhora a visibilidade da tela.
  printf("<center>\n");
  # No próximo printf existe um grupo de operadores ternários formatando o texto que é mostrado na string do printf();
  # printf("<font color=red>%s</titulo>\n",( $acao=='Abertura')?'Abertura<br>':(( $acao=='Incluir')?'Inclus&atilde;o':(( $acao=='Consultar')?'Consulta':(( $acao=='Alterar')?'Altera&ccedil;&atilde;o':(( $acao=='Excluir')?'Exclus&atilde;o':(( $acao=='Listar')?'Listagem:':''))))));
  $titulo = ( $acao=="Inicio")  ? "In&iacute;cio" : (( $acao=="Incluir")   ? "Inclus&atilde;o" : (( $acao=="Consultar") ? "Consulta" : (( $acao=="Alterar")   ? "Altera&ccedil;&atilde;o" : (( $acao=="Excluir")   ? "Exclus&atilde;o" : (( $acao=="Listar")    ? "Listagem" : "Navegar" ) ) ) ) );
  printf("<table border=0 style=' border-collapse: collapse;' width=900>\n");
  $listwhite=( $acao.$salto=="Listar5" ) ? TRUE : FALSE;
  printf("<tr><td><font color=red><b>$objeto</b> - <i>$titulo</i></titulo></td><td width=20>%s</td></tr>\n",$listwhite ? "" : "<button type='button' onclick='history.go(-$salto)'>SAIR<!--<img src='../imgs/DB2Saida.png' width=25 height=25>--></button>");
  printf("</table>\n");
}
################################ Fim da Função IniciaPagina
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function botoes($pagina,$menu,$saida,$reset,$acao,$salto)
{ # Função.....: botoes
  # Parametros.: Esta Função recebe TRUE|FALSE para os parâmetros que apontam para montar as tags de exibição dos botões de navegação
  # Descrição..: Esta Função emite as TAGS para "< 1 Pag.", "< Menu","Saída","Limpar" e "Ação"
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2017-05-31
  # Atualização: 2017-05-31 - Todo desenvolvimento e teste da função.
  #              2018-04-27 - Alterei a ordem dos textos que formam a barra de botões.
  #              2018-09-17 - Mudei a aparencia dos botões usando o CSS que foi codificado no iniciapagina.
  #################################################################################################################################################################################
  $barra=( ISSET($acao) ) ? "<button type='submit' style='font-size:120%%;'>Procesar</button>\n" : ""; # <img src='../imgs/DB2Sav.png' width=25 height=25> ou &#10004;
  $barra=($reset)  ? $barra."<button type=reset    style='font-size:120%%;'>Limpar</button>\n" : $barra; # <img src='../imgs/DB2Ref.png' width=25 height=25> ou &#11118;
  $barra=($pagina) ? $barra."<button type='button' style='font-size:120%%;' onclick='history.go(-1)'> < 1</button>\n" : $barra; # <img src='../imgs/backblack.png' height='25'> ou &#9204;
  $barra=($menu)   ? $barra."<button type='button' style='font-size:120%%;' onclick='history.go(-($salto-1))'> < Menu </button>\n" : $barra; # <img src='../imgs/backblackini.png' height='25'> ou  &#9194;&#65038;
  #$barra=($saida)  ? $barra."<button type='button' onclick='history.go(-($salto+2))'><img src='../imgs/DB2Saida.png' width='25' height='25'></button>\n" : $barra;
  #$barra=($pagina) ? $barra."<input class='button' type='button' value='< Voltar' onclick='history.go(-1)'>" : $barra;
  #$barra=($menu) ? $barra."<input class='button'   type='button' value='< In&iacute;cio' onclick='history.go(-($salto+1))'>" : $barra;
  #$barra=($saida) ? $barra."<input class='button'  type='button' value='< Sa&iacute;da' onclick='history.go(-($salto+1))'>" : $barra;
  printf("$barra\n<br>\n");
}
################################ Fim da Função botoes
#----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function terminapagina($texto,$prg,$center)
{ # Função.....: terminapagina
  # Parametros.: $texto - descreve a ação (apresentado no lado esquerdo da linha de rodap?),
  #              $prg - c?digo do programa (apresentado lado direito da linha de rodap?) e
  #              $center - TRUE/FALSE para colocar a linha de rodap? centralizada ou n?o.
  # Descrição..: Esta Função emite uma linha no final da p?gina e coloca uma mensagem de Autoria.
  #################################################################################################################################################################################
  # Autor......: João Maurício Hypólito - Use! Mas fale quem fez!
  # Criação....: 2009-03-27
  # Atualização: 2009-09-17
  #################################################################################################################################################################################
  printf("%s",($center) ? "<center>" : "" ); # Este comando combina um operador tern?rio DENTRO print().
  printf("<font size=2 color='gray'>$texto - Resolu&ccedil;&atilde;o m&iacute;nima de 1280x720 &copy; Copyright %s, FATEC Ourinhos - $prg</titulo>\n",date('Y'));
  printf("</dir>\n</titulo>\n"); # Estas duas TAGS fecham TAGS aberta no iniciap?gina.
  printf("%s</body>\n</html>\n",($center) ? "</center>" : "" );
}
function conecta($tipoBanco)
{
  global $nuconexao;

  $tipoBanco == "PostgreSQL";
  $host     = "localhost";
  $port     = "5432";
  $dbname   = "Praticasql";
  $user     = "postgres";
  $password = "Gui-2806";

  $string_conexao = "host=$host port=$port dbname=$dbname user=$user password=$password";
    
  # Conecta nativamente no PostgreSQL
  $nuconexao = pg_connect($string_conexao);

  if (!$nuconexao) {
    die("Erro ao conectar ao PostgreSQL.");
  }

  return $nuconexao;
}
################################ Fim da Função terminapagina

?>
