<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

function base64ToImage($b64) {
    $LOCALSERVER = 'http://localhost/backrutas/public/';
    $defaultImage = $LOCALSERVER . 'default-image.png'; // Ruta de la imagen por defecto

    // Check if the input is a URL of the specified format
    if (strpos($b64, $LOCALSERVER . 'imagenesproducto/') === 0) {
        return $b64;
    }

    // Check if the input is already a complete URL
    if (filter_var($b64, FILTER_VALIDATE_URL)) {
        return $b64;
    }

    // Check if the input is empty, if so, return the default image
    if (empty($b64)) {
        return $defaultImage;
    }

    // Otherwise, assume it's a Base64-encoded image
    $bin = base64_decode($b64);

    $im = imageCreateFromString($bin);
    
    if (!$im) {
        die('Base64 value is not a valid image');
    }

    $micarpeta = '../imagenesproducto';
    if (!file_exists($micarpeta)) {
        mkdir($micarpeta, 0777, true);
    }
 
    // Specify the location where you want to save the image
    $date = new DateTime();
    $date =  $date->format('YmdHis');
    $img_file = "../imagenesproducto/".$date.".png";

    imagepng($im, $img_file, 0);
    $img_file = $LOCALSERVER."imagenesproducto/".$date.".png";
   
    return $img_file;
}
//GET ALL CLIENTS
$app->get('/api/productos/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_PRODUCTOS";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro productos");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});


$app->post('/api/productos/listproductosbynegocio', function (Request $request, Response $response) {
  
    $IDNegocio = $request->getParam('IDNegocio');
    try {
        $sql = "EXEC SP_LISTAR_PRODUCTOSXNEGOCIO @IDNegocio = :IDNegocio";
        $cnx = new Conexion();
      
        $query = $cnx->Conectar();
        $stmt = $query->prepare($sql);
       
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
$app->post('/api/productos/registrar', function (Request $request, Response $response) {
    $IDCategoria				   = $request->getParam('IDCategoria');
    $IDMarca				   = $request->getParam('IDMarca');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $Precio				   = $request->getParam('Precio');
    $Stock				   = $request->getParam('Stock');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
    $ImageProducto				= $request->getParam('ImageProducto');
    $rest = base64ToImage($ImageProducto);
  
    $sql = trim("EXEC SP_INSERT_PRODUCTO
    @IDCategoria=" . agregarComillas($IDCategoria) . "		
   ,@IDMarca=" . agregarComillas($IDMarca) . "		
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@Nombre=" . agregarComillas($Nombre) . "
   ,@Precio=" . agregarComillas($Precio) . "		
   ,@Stock=" . agregarComillas($Stock) . "		
   ,@UsuarioRegistro=" . agregarComillas($UsuarioRegistro)  . "		
   ,@ImageProducto=" . agregarComillas($rest));

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
   

   $app->put('/api/productos/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDCategoria				   = $request->getParam('IDCategoria');
    $IDMarca				   = $request->getParam('IDMarca');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $Nombre				    = $request->getParam('Nombre');
    $Precio				   = $request->getParam('Precio');
    $Stock				   = $request->getParam('Stock');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');
    $ImageProducto				= $request->getParam('ImageProducto');
    $rest = base64ToImage($ImageProducto);
    
    $sql = trim("EXEC SP_UPDATE_PRODUCTO
    @ID=" . agregarComillas($ID) . "	
   , @IDCategoria=" . agregarComillas($IDCategoria) . "		
    ,@IDMarca=" . agregarComillas($IDMarca) . "		
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Precio=" . agregarComillas($Precio) . "		
    ,@Stock=" . agregarComillas($Stock) . "		
    ,@UsuarioModificacion=" . agregarComillas($UsuarioModificacion). "		
    ,@ImageProducto=" . agregarComillas($rest));
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


$app->post('/api/productos/eliminar', function (Request $request, Response $response) {
    $ID				= $request->getParam('ID');	
    $sql = trim("EXEC SP_DELETE_PRODUCTO
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