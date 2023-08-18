<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/tienda/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_TIENDAS";
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro tiendas");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});
$app->get('/api/tienda/listtest', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_TIENDAS_PRUEBA";
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro tiendas");
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
$app->post('/api/tienda/registrar', function (Request $request, Response $response) {
  
    $IDVendedor				    = $request->getParam('IDVendedor');
    $IDRuta				    = $request->getParam('IDRuta');
    $IDNegocio				    = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $Direccion				    = $request->getParam('Direccion');
    $Contacto				    = $request->getParam('Contacto');
    $Latitud				    = $request->getParam('Latitud');
    $Longitud				    = $request->getParam('Longitud');
    $UsuarioRegistro				    = $request->getParam('UsuarioRegistro');
    $sql = trim("EXEC SP_INSERT_TIENDA
    @IDVendedor=" . agregarComillas($IDVendedor) . "		
    ,@IDRuta=" . agregarComillas($IDRuta) . "
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Direccion=" . agregarComillas($Direccion) . "
    ,@Contacto=" . agregarComillas($Contacto) . "
    ,@Latitud=" . agregarComillas($Latitud) . "		
    ,@Longitud=" . agregarComillas($Longitud) . "		
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
   

   $app->put('/api/tienda/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDVendedor				    = $request->getParam('IDVendedor');
    $IDRuta				    = $request->getParam('IDRuta');
    $IDNegocio				    = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $Direccion				    = $request->getParam('Direccion');
    $Contacto				    = $request->getParam('Contacto');
    $Latitud				    = $request->getParam('Latitud');
    $Longitud				    = $request->getParam('Longitud');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');
   

    $sql = trim("EXEC SP_UPDATE_TIENDA
    @ID=" . agregarComillas($ID) . "	
   , @IDVendedor=" . agregarComillas($IDVendedor) . "		
    ,@IDRuta=" . agregarComillas($IDRuta) . "
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Direccion=" . agregarComillas($Direccion) . "
    ,@Contacto=" . agregarComillas($Contacto) . "
    ,@Latitud=" . agregarComillas($Latitud) . "		
    ,@Longitud=" . agregarComillas($Longitud) . "				
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


$app->post('/api/tienda/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_TIENDA
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