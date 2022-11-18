<?php
    include ("cl_tipo.php");
    include ("cl_banco.php");
    if(isset($_GET['op'])) 
        $op=$_GET['op'];
    else 
        $op="";
    if($op=="") 
    {
        header("Location: index.php"); 
        exit;
    }
    if($op=="asu") 
    {
        header("Location: asenha.php"); 
        exit;
    }

    //Verificação de ação de Inclusão do Tipo
    if($op=="iu")
    {
        print "<p align='center'>Novo Tipo</p>
        <form method='post' action='mtipo.php?op=iiu'>
        <p align='center'>
        <br>ID Tipo<input type='text' name='idt' size='2' maxglength='2'>
        <br>Nome Tipo<input type='text' name='nomet' size='30' maxglength='30'>
        <br><input type='submit' value='Incluir'></p></form>"; 
        exit;
    }

    //Inclusão do Tipo
    if($op=="iiu")
    {
        $mensagem="";
        $tipo=new tipo($_POST['idt'],$_POST['nomet']);
        if($tipo->getIdT()=="") 
            $mensagem.="<br>ID Tipo é obrigatório";
        if($tipo->getNomeT()=="") 
            $mensagem.="<br>Nome Tipo é obrigatório";
        if($mensagem!="")
        {
            print $mensagem;
            print"<br><a href='mtipo.php?op=iu'>Voltar</a>";
            exit;
        }
        $conec=conec::conecta_mysql("localhost","root","","contatos");
        try
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("INSERT INTO tipo values(?,?)");
            $sth->execute(array ($tipo->getIdT(), $tipo->getNomeT()));
            print "<br>Tipo incluido com sucesso <br><a href='sistema.php'>Voltar</a>";
        }
        catch(Exception $e)
        {
            print "Erro ".$e->getMessage(). "<br><a href='sistema.php'>Voltar</a>"; 
            exit;
        }
        exit;
    }

    //Listagem de Tipos
    if($op=="lu")
    {
        $conec=conec::conecta_mysql("localhost", "root", "", "contatos");
        try
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("SELECT * FROM tipo");
            $sth->execute();
            print "<table border='1'> <tr><td>IdTipo</td><td>NomeTipo</td></tr>";
            if($sth->rowCount()==0)
            {
                print "<tr><td>Nada para listar</td></tr></table> <br><a href='sistema.php'>Voltar</a>"; 
                exit;
            }
            $linha=$sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_FIRST);
            do
            {
                $ous= new tipo($linha[0], $linha[1]);
                print "<TR><TD>". $ous->getIdT()."</TD>". "<TD>".$ous->getNomeT()."</TD></TR>";
            }
            while($linha=$sth->fetch(PDO::FETCH_NUM,PDO::FETCH_ORI_NEXT));
                print"</TABLE><br><a href='sistema.php'>Voltar</a>";
        }
        catch(Exception $e)
        { 
            print"<br>Falha: Tipos não listados“. $e->getMessage() ";
            print "<br><a href='sistema.php'> Voltar</a>";
            exit; 
        }
        exit;
    }

    //Listagem para Exclusão de Tipos
    if($op=="eu")
    {
        $conec=conec::conecta_mysql("localhost", "root", "", "contatos");
        try
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("SELECT * FROM tipo");
            $sth->execute();
            if($sth->rowCount()==1)
            {
                print " <p>Nenhum Tipo para excluir</p>";
                print "<br><a href='sistema.php'> Voltar</a>"; 
                exit; 
            }
            print "<form method= 'post' action= 'mtipo.php?op=eeu'> <select name='idt'> 
            <option value= '' >Selecione para excluir";
            $linha=$sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_FIRST);
            do
            {
                $ous= new tipo($linha[0],$linha[1]);
                print "<option value='".$ous->getIdT(). "'>".$ous->getNomeT();
            }
            while($linha=$sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT));
                print"</SELECT><br><input type='submit' value='Excluir'> </form><br><a href='sistema.php'>Voltar</a>";
        }
        catch (Exception $e)
        { 
            print"<br>Falha: ". $e->getMessage();
            print "<br><a href='sistema.php'>Voltar</a>"; 
            exit; 
        }
        exit;
    }

    //Exclusão do Tipo
    if($op=="eeu")
    { 
        $tipo= new tipo($_POST["idt"], "");
        if($tipo->getIdT()=="")
        { 
            print "Selecione um Tipo para Excluir";
            print "<br><a href='sistema.php'>Voltar</a>"; 
            exit;
        }
        $conec=conec::conecta_mysql ("localhost", "root", "", "contatos");
        try
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("DELETE FROM tipo WHERE idt=?");
            $sth->execute(array($tipo->getIdT()));
            if($sth->rowCount()==0) 
                print "Tipo não excluído";
            else 
                print "Tipo ".$tipo->getNomeT(). "Excluído com sucesso";
                print "<br><a href='sistema.php'>Voltar</a>";
        }
        catch(Exception $e)
        {
            print "<br>Falha: Tipo não excluido " . $e->getMessage(). "<br><a href='sistema.php'>Voltar</a>"; 
            exit; 
        }
        exit;
    }

    //Seleção para alteração de Tipo
    if($op=="au")
    {
        $conec=conec::conecta_mysql ("localhost", "root", "", "contatos");
        try 
        { 
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            print "<h2>Alteração de Tipo</h2>";
            $sth=$conec->prepare("SELECT * FROM tipo",
            array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $sth->execute();
            if($sth->rowCount()==0)
            {
                print "Nenhum tipo para alterar <br><a href='sistema.php'>Voltar</a>";
                exit;
            }
            $linha = $sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_FIRST);
            print "<form method='post' action='mtipo.php?op=aeu'>";
            print "<SELECT name='idt'>";
            do
            {
                $ous= new tipo($linha[0], $linha[1]);
                print "<option value='".$ous->getIdT(). "'>".$ous->getNomeT();
            } 
            while($linha=$sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT));
                print"</SELECT><INPUT type='submit' value='Editar'></form> <br><a href='sistema.php'>Voltar</a>";
        } 
        catch (Exception $e)
        {   
            print "<br>Falha: " . $e->getMessage(). "<br><a href='sistema.php'>Voltar</a>";
            exit; 
        }
        exit;
    }

    //Alteração de Tipo
    if ($op=="aeu")
    {
        $tipo=$_POST["idt"];
        $conec=conec::conecta_mysql ("localhost", "root", "", "contatos");
        try 
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("SELECT * FROM tipo WHERE idt='$tipo'",
            array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $sth->execute();
            $linha = $sth->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_FIRST);
            $ous= new tipo($linha[0], $linha[1]);
            print "<h2>Alteração de Tipo</h2>";
            print "<form method='post' action='mtipo.php? op=acu'>";
            print "ID Tipo:<INPUT type='text' name='idt' value='".$ous->getIdT()."' readonly>";
            print "Nome Tipo:<INPUT type='text' name='nomet' value='".$linha[1]."'>";

            print "</SELECT><INPUT type='submit' value='Confirmar'></form> <br><a href='sistema.php'>Voltar</a>";
        }
        catch(Exception $e)
        {
            print "<br>Falha: " . $e->getMessage(). "<br><a href='sistema.php'>Voltar</a>";
            exit; 
        }
        exit;
    }
    //Confirmação da Alteração do Tipo
    if ($op=="acu")
    {
        $ous= new tipo($_POST["idt"],$_POST["nomet"]);
        if($ous->getIdT()=="") 
            print "<br>Selecione para Alterar";
        if($ous->getNomeT()=="") 
            print "<br>O nome está em branco";
        if($ous->getIdT()=="" || $ous->getNomeT()=="")
        {
            print "<br><a href='mtipo.php?op=au'>Voltar</a>";
            exit;
        }
        $conec=conec::conecta_mysql ("localhost", "root", "", "contatos");
        try 
        {
            $conec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sth=$conec->prepare("UPDATE tipo set idt=?,nomet=? WHERE idt=?");
            $sth->execute(array($ous->getIdT(), $ous->getNomeT(), $ous->getIdT()));
            if($sth->rowCount()==0) 
                print "<br>Tipo não alterado";
            else 
                print "<br>Tipo ".$ous->getNomeT()." Alterado com sucesso";
                print "<br><a href='sistema.php'>Voltar</a>";
        }
        catch (Exception $e)
        {
            print "<br>Falha: Tipo não alterado ".$e->getMessage(). "<br><a href='sistema.php'>Voltar</a>";
            exit;
        }
        exit;
    }
?>