<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->post('/api/pedidos/list', function (Request $request, Response $response) {
    $EstadoPedido = $request->getParam('EstadoPedido');
    $IDNegocio = $request->getParam('IDNegocio');
    try {
        $sql = "EXEC SP_LISTAR_PEDIDOS @EstadoPedido = :EstadoPedido, @IDNegocio = :IDNegocio";
        $cnx = new Conexion();
      
        $query = $cnx->Conectar();
        $stmt = $query->prepare($sql);
        $stmt->bindParam(':EstadoPedido', $EstadoPedido);
        $stmt->bindParam(':IDNegocio', $IDNegocio);
        $stmt->execute();

        $pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($stmt->rowCount() > 0) {
            return $response->withJson($pedidos);
        } else {
            return $response->withJson("No se encontraron pedidos");
        }
    } catch (PDOException $error) {
        $errores = array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);
    }
});

$app->post('/api/pedidos/listbynegociobyclient', function (Request $request, Response $response) {
    $IDCliente = $request->getParam('IDCliente');
    $IDNegocio = $request->getParam('IDNegocio');
    try {
        $sql = "EXEC SP_LISTAR_PEDIDOXNEGOCIO @IDNegocio = :IDNegocio, @IDCliente = :IDCliente";
        $cnx = new Conexion();
      
        $query = $cnx->Conectar();
        $stmt = $query->prepare($sql);
        $stmt->bindParam(':IDCliente', $IDCliente);
        $stmt->bindParam(':IDNegocio', $IDNegocio);
        $stmt->execute();

        $pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($stmt->rowCount() > 0) {
            return $response->withJson($pedidos);
        } else {
            return $response->withJson("No se encontraron pedidos");
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
$app->post('/api/pedidos/registrar', function (Request $request, Response $response) {
    $IDEstadoPedido				   = $request->getParam('IDEstadoPedido');
    $IDCliente				   = $request->getParam('IDCliente');
    $IDVendedor				   = $request->getParam('IDVendedor');
    $IDRuta				    = $request->getParam('IDRuta');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $IDEstado				   = $request->getParam('IDEstado');
    $FechaPedido				   = $request->getParam('FechaPedido');
    $MontoTotal				   = $request->getParam('MontoTotal');
    $MontoImpuestos				   = $request->getParam('MontoImpuestos');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
    $Direccion_p				= $request->getParam('Direccion_p');
  
    $sql = trim("EXEC SP_INSERT_PEDIDO
    @IDEstadoPedido=" . agregarComillas($IDEstadoPedido) . "		
   ,@IDCliente=" . agregarComillas($IDCliente) . "		
   ,@IDVendedor=" . agregarComillas($IDVendedor) . "		
   ,@IDRuta=" . agregarComillas($IDRuta) . "
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@IDEstado=" . agregarComillas($IDEstado) . "	
   ,@FechaPedido=" . agregarComillas($FechaPedido) . "		
   ,@MontoTotal=" . agregarComillas($MontoTotal) . "	
   ,@MontoImpuestos=" . agregarComillas($MontoImpuestos) . "	
   ,@UsuarioRegistro=" . agregarComillas($UsuarioRegistro) . "	
   ,@Direccion_p=" . agregarComillas($Direccion_p));
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
   

   $app->put('/api/pedidos/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDEstadoPedido				   = $request->getParam('IDEstadoPedido');
    $IDCliente				   = $request->getParam('IDCliente');
    $IDVendedor				   = $request->getParam('IDVendedor');
    $IDRuta				    = $request->getParam('IDRuta');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $IDEstado				   = $request->getParam('IDEstado');
    $FechaPedido				   = $request->getParam('FechaPedido');
    $MontoTotal				   = $request->getParam('MontoTotal');
    $MontoImpuestos				   = $request->getParam('MontoImpuestos');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_PEDIDO
    @ID=" . agregarComillas($ID) . "	
    ,@IDEstadoPedido=" . agregarComillas($IDEstadoPedido) . "		
   ,@IDCliente=" . agregarComillas($IDCliente) . "		
   ,@IDVendedor=" . agregarComillas($IDVendedor) . "		
   ,@IDRuta=" . agregarComillas($IDRuta) . "
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@IDEstado=" . agregarComillas($IDEstado) . "	
   ,@FechaPedido=" . agregarComillas($FechaPedido) . "		
   ,@MontoTotal=" . agregarComillas($MontoTotal) . "	
   ,@MontoImpuestos=" . agregarComillas($MontoImpuestos) . "	
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