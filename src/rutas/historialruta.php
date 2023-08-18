<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;


//GET ALL CLIENTS


$app->post('/api/historialruta/listruta', function (Request $request, Response $response) {
    $IDRuta = $request->getParam('IDRuta');
    try {
        $sql =  "EXEC SP_LISTAR_HISTORIAL_RUTA @IDRuta = :IDRuta";
        
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':IDRuta', $IDRuta);
        $resultado->execute();

        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro Ruta");
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
$app->post('/api/historialruta/registrar', function (Request $request, Response $response) {
    $IDRuta				   = $request->getParam('IDRuta');
    $IDNegocio				    = $request->getParam('IDNegocio');
    $Fecha                  = $request->getParam('Fecha');
    $Descripcion				= $request->getParam('Descripcion');
    
    $UsuarioRegistro		= $request->getParam('UsuarioRegistro');
  
    $sql = trim("EXEC SP_INSERT_HISTORIAL_RUTA
    @IDRuta=" . agregarComillas($IDRuta) . "		
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "
    ,@Fecha=" . agregarComillas($Fecha) . "
    ,@Descripcion=" . agregarComillas($Descripcion) . "	
 	
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
   

   $app->put('/api/historialruta/modificar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');		
    $IDRuta				   = $request->getParam('IDRuta');
    $IDNegocio				    = $request->getParam('IDNegocio');
    $Fecha                  = $request->getParam('Fecha');
    $Descripcion				= $request->getParam('Descripcion');
    $UsuarioModificacion		= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_HISTORIAL_RUTA
    @ID=" . agregarComillas($ID) . "	
    ,@IDRuta=" . agregarComillas($IDRuta) . "		
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "
    ,@Fecha=" . agregarComillas($Fecha) . "
    ,@Descripcion=" . agregarComillas($Descripcion) . "	
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


?>