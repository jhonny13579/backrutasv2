<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/clientes/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_CLIENTES";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($clientes);
        } else {
            return $response->withJson("No se encontro clientes");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});


function agregarComillas($valor) {
    if (is_string($valor)) {
        return "'" . $valor . "'";
    } elseif (is_numeric($valor)) {
        return $valor;
    } else {
        return 'NULL'; // Valor no reconocido, se asume como NULL
    }
}
$app->post('/api/clientes/registrar', function (Request $request, Response $response) {
    $Nombre				   = $request->getParam('Nombre');
    $IDUsuario				   = $request->getParam('IDUsuario');
    $RUC				    = $request->getParam('RUC');
    $Direccion				= $request->getParam('Direccion');
    $UsuarioRegistro		= $request->getParam('UsuarioRegistro');
  
    $sql = trim("EXEC SP_INSERT_CLIENTES
    @Nombre=" . agregarComillas($Nombre) . "		
    ,@IDUsuario=" . agregarComillas($IDUsuario) . "		
    ,@RUC=" . agregarComillas($RUC) . "
    ,@Direccion=" . agregarComillas($Direccion) . "			
    ,@UsuarioRegistro=" . agregarComillas($UsuarioRegistro));
    $sql = preg_replace('/\s+/', ' ', $sql);
       try {
           $cnx = new Conexion();
           $query = $cnx->Conectar();
           $resultado = $query->prepare($sql);
           $resultado->execute();
           $resultado->nextRowset();
           $resultado = $resultado->fetch(PDO::FETCH_ASSOC);    
           if ($resultado) {
            return $response->withJson($resultado, 200);
        } else {
            $errores = array(
                "text" => "La consulta no devolvió resultados."
            );
            return $response->withJson($errores, 500);
        }
             
            
       } catch (PDOException $error) {
   
           $errores =  array(
               "text" => $error->getMessage()
           );
   
           return $response->withJson($errores, 500);  
       }
      
   });
   

   $app->put('/api/clientes/modificar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');		
    $Nombre				   = $request->getParam('Nombre');
    $RUC				    = $request->getParam('RUC');
    $Direccion				= $request->getParam('Direccion');
    $UsuarioModificacion		= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_CLIENTES
    @ID=" . agregarComillas($ID) . "	
    ,@Nombre=" . agregarComillas($Nombre) . "		
    ,@RUC=" . agregarComillas($RUC) . "
    ,@Direccion=" . agregarComillas($Direccion) . "	
    ,@UsuarioModificacion=" . agregarComillas($UsuarioModificacion));
    $sql = preg_replace('/\s+/', ' ', $sql);
    try {
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);        
        $resultado->execute();
        $resultado->nextRowset();
        $resultado = $resultado->fetch(PDO::FETCH_ASSOC);    
        if ($resultado) {
            return $response->withJson($resultado, 200);
        } else {
            $errores = array(
                "text" => "La consulta no devolvió resultados."
            );
            return $response->withJson($errores, 500);
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }
});


$app->post('/api/clientes/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_CLIENTE 
    @ID=" . agregarComillas($ID));
    $sql = preg_replace('/\s+/', ' ', $sql);
    try {
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);        
        $resultado->execute();
        $resultado->nextRowset();
        $resultado = $resultado->fetch(PDO::FETCH_ASSOC);    
        if ($resultado) {
            return $response->withJson($resultado, 200);
        } else {
            $errores = array(
                "text" => "La consulta no devolvió resultados."
            );
            return $response->withJson($errores, 500);
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }
});
?>