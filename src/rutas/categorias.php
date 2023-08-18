<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//GET ALL CLIENTS
$app->get('/api/categorias/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_CATEGORIAS";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro categorias");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});

$app->post('/api/categorias/listbynegocio', function (Request $request, Response $response) {
    $IDNegocio = $request->getParam('IDNegocio');
    try {
        $sql =  "EXEC SP_LISTAR_CATEGORIASXNEGOCIO @IDNegocio = :IDNegocio";
        
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->prepare($sql);
        $resultado->bindParam(':IDNegocio', $IDNegocio);
        $resultado->execute();

        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro categorias");
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
$app->post('/api/categorias/registrar', function (Request $request, Response $response) {
    $IDNegocio				   = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
  
  
    $sql = trim("EXEC SP_INSERT_CATEGORIAS
    @IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
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
   

   $app->put('/api/categorias/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDNegocio			    = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_CATEGORIAS
    @ID=" . agregarComillas($ID) . "	
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
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


$app->post('/api/categorias/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_CATEGORIAS
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