<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/promociones/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_PROMOCIONES";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro PROMOCIONES");
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
$app->post('/api/promociones/registrar', function (Request $request, Response $response) {
    $IDTienda				   = $request->getParam('IDTienda');
    $Nombre				   = $request->getParam('Nombre');
    $Descripcion				   = $request->getParam('Descripcion');
    $FechaInicio				    = $request->getParam('FechaInicio');
    $FechaFin				   = $request->getParam('FechaFin');
    $Descuento				   = $request->getParam('Descuento');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
  
  
    $sql = trim("EXEC SP_INSERT_PROMOCION
    @IDTienda=" . agregarComillas($IDTienda) . "		
   ,@Nombre=" . agregarComillas($Nombre) . "		
   ,@Descripcion=" . agregarComillas($Descripcion) . "		
   ,@FechaInicio=" . agregarComillas($FechaInicio) . "
   ,@FechaFin=" . agregarComillas($FechaFin) . "		
   ,@Descuento=" . agregarComillas($Descuento) . "		
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
   

   $app->put('/api/promociones/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDTienda				   = $request->getParam('IDTienda');
    $Nombre				   = $request->getParam('Nombre');
    $Descripcion				   = $request->getParam('Descripcion');
    $FechaInicio				    = $request->getParam('FechaInicio');
    $FechaFin				   = $request->getParam('FechaFin');
    $Descuento				   = $request->getParam('Descuento');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_PROMOCION
    @ID=" . agregarComillas($ID) . "	
   , @IDTienda=" . agregarComillas($IDTienda) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "		
    ,@Descripcion=" . agregarComillas($Descripcion) . "		
    ,@FechaInicio=" . agregarComillas($FechaInicio) . "
    ,@FechaFin=" . agregarComillas($FechaFin) . "		
    ,@Descuento=" . agregarComillas($Descuento) . "		
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


$app->post('/api/promociones/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_PROMOCION
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