<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/comprobantes/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_COMPROBANTES";
        $cnx = new Conexion();
    
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro Comprobantes");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});


$app->post('/api/comprobantes/listbycliente', function (Request $request, Response $response) {
    $IDCliente = $request->getParam('IDCliente');
    try {
        $sql =  "EXEC SP_LISTAR_COMPROBANTEXCLIENTE @IDCliente = :IDCliente";
        
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':IDCliente', $IDCliente);
        $resultado->execute();

        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro Comprobantes");
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
$app->post('/api/comprobantes/registrar', function (Request $request, Response $response) {
    $IDCliente			   = $request->getParam('IDCliente');
    $IDTipoComprobante				    = $request->getParam('IDTipoComprobante');
    $MontoTotal				    = $request->getParam('MontoTotal');
    $FechaEmision				= $request->getParam('FechaEmision');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
    
  
    $sql = trim("EXEC SP_INSERT_COMPROBANTE
    @IDCliente=" . agregarComillas($IDCliente) . "		
    ,@IDTipoComprobante=" . agregarComillas($IDTipoComprobante) . "
    ,@MontoTotal=" . agregarComillas($MontoTotal) . "
    ,@FechaEmision=" . agregarComillas($FechaEmision) . "
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
   

   $app->put('/api/comprobantes/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDCliente			   = $request->getParam('IDCliente');
    $IDTipoComprobante				    = $request->getParam('IDTipoComprobante');
    $MontoTotal				    = $request->getParam('MontoTotal');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_COMPROBANTE
    @ID=" . agregarComillas($ID) . "	
    ,@IDCliente=" . agregarComillas($IDCliente) . "		
    ,@IDTipoComprobante=" . agregarComillas($IDTipoComprobante) . "		
    ,@MontoTotal=" . agregarComillas($MontoTotal) . "
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


$app->post('/api/comprobantes/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_COMPROBANTE
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