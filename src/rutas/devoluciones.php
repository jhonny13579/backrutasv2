<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/devoluciones/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_DEVOLUCIONES";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($clientes);
        } else {
            return $response->withJson("No se encontro DEVOLUCIONES");
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
$app->post('/api/devoluciones/registrar', function (Request $request, Response $response) {
    $IDPedido				   = $request->getParam('IDPedido');
    $IDProducto				    = $request->getParam('IDProducto');
    $IDTienda                  = $request->getParam('IDTienda');
    $IDCondicionDevolucion				= $request->getParam('IDCondicionDevolucion');
    $CantidadDevuelta				= $request->getParam('CantidadDevuelta');
    $UsuarioRegistro		= $request->getParam('UsuarioRegistro');
  
    $sql = trim("EXEC SP_INSERT_DEVOLUCION
    @IDPedido=" . agregarComillas($IDPedido) . "		
    ,@IDProducto=" . agregarComillas($IDProducto) . "
    ,@IDTienda=" . agregarComillas($IDTienda) . "
    ,@IDCondicionDevolucion=" . agregarComillas($IDCondicionDevolucion) . "	
    ,@CantidadDevuelta=" . agregarComillas($CantidadDevuelta) . "			
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
   

   $app->put('/api/devoluciones/modificar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');		
    $IDPedido				   = $request->getParam('IDPedido');
    $IDProducto				    = $request->getParam('IDProducto');
    $IDTienda                  = $request->getParam('IDTienda');
    $IDCondicionDevolucion				= $request->getParam('IDCondicionDevolucion');
    $CantidadDevuelta				= $request->getParam('CantidadDevuelta');
    $UsuarioModificacion		= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_DEVOLUCION
    @ID=" . agregarComillas($ID) . "	
    ,@IDPedido=" . agregarComillas($IDPedido) . "		
    ,@IDProducto=" . agregarComillas($IDProducto) . "
    ,@IDTienda=" . agregarComillas($IDTienda) . "
    ,@IDCondicionDevolucion=" . agregarComillas($IDCondicionDevolucion) . "	
    ,@CantidadDevuelta=" . agregarComillas($CantidadDevuelta) . "			
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


$app->post('/api/devoluciones/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_DEVOLUCION 
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