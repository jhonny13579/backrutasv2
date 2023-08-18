<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->post('/api/detallepedido/list', function (Request $request, Response $response) {
    $IDPedido = $request->getParam('IDPedido');
  
    try {
        $sql = "EXEC SP_LISTAR_DETALLE_PEDIDO @IDPedido = :IDPedido";
        $cnx = new Conexion();
      
        $query = $cnx->Conectar();
        $stmt = $query->prepare($sql);
        $stmt->bindParam(':IDPedido', $IDPedido);
      
        $stmt->execute();

        $pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($stmt->rowCount() > 0) {
            return $response->withJson($pedidos);
        } else {
            return $response->withJson("No se encontraron DETALLE pedidos");
        }
    } catch (PDOException $error) {
        $errores = array(
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
$app->post('/api/detallepedido/registrar', function (Request $request, Response $response) {
    $IDPedido				   = $request->getParam('IDPedido');
    $IDProducto				   = $request->getParam('IDProducto');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $Cantidad				    = $request->getParam('Cantidad');
    $PrecioUnitario				   = $request->getParam('PrecioUnitario');
  
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
  
  
    $sql = trim("EXEC SP_INSERT_DETALLE_PEDIDO
    @IDPedido=" . agregarComillas($IDPedido) . "		
   ,@IDProducto=" . agregarComillas($IDProducto) . "		
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@Cantidad=" . agregarComillas($Cantidad) . "
   ,@PrecioUnitario=" . agregarComillas($PrecioUnitario) . "		
   
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
   

   $app->put('/api/detallepedido/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDPedido				   = $request->getParam('IDPedido');
    $IDProducto				   = $request->getParam('IDProducto');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $Cantidad				    = $request->getParam('Cantidad');
    $PrecioUnitario				   = $request->getParam('PrecioUnitario');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_DETALLE_PEDIDO
    @ID=" . agregarComillas($ID) . "	
   , @IDPedido=" . agregarComillas($IDPedido) . "		
   ,@IDProducto=" . agregarComillas($IDProducto) . "		
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@Cantidad=" . agregarComillas($Cantidad) . "
   ,@PrecioUnitario=" . agregarComillas($PrecioUnitario) . "		
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


// $app->post('/api/promociones/eliminar', function (Request $request, Response $response) {
//     $ID				= $request->getParam('ID');	
//     $sql = trim("EXEC SP_DELETE_PROMOCION
//     @ID=" . agregarComillas($ID));
//     $sql = preg_replace('/\s+/', ' ', $sql);
//     try {
//         $cnx = new Conexion();
//         $query = $cnx->Conectar();
//         $resultado = $query->prepare($sql);        
//         $resultado->execute();
//         $resultado->nextRowset();
//         $resultado = $resultado->fetch(PDO::FETCH_ASSOC);    
//         if ($resultado) {
//             return $response->withJson($resultado, 200);
//         } else {
//             $errores = array(
//                 "text" => "La consulta no devolvió resultados."
//             );
//             return $response->withJson($errores, 500);
//         }
//     } catch (PDOException $error) {

//         $errores =  array(
//             "text" => $error->getMessage()
//         );
//         return $response->withJson($errores, 500);  
//     }
// });
?>