<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;


// function base64ToImage($b64) {
//     $LOCALSERVER = 'http://localhost/backrutas/public/';
//     $SERVIDOR = 'http://nation-service.com/Api/public/';
//     // Define the Base64 value you need to save as an image
//     // $b64 = 'iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAbFBMVEX///8AAACDg4O2tralpaWTk5NHR0fb29vk5OSYmJjg4OCioqLz8/Nubm6bm5shISF4eHg4ODjDw8PQ0ND5+fnX19dMTEzr6+t+fn5eXl6ysrJWVlYyMjK9vb0nJycXFxc/Pz8PDw+Li4toaGi5z6Y9AAADNklEQVR4nO3c61biMBSGYYucjwJV5CAI3v89zowOmE0KpU3KTsr7/JkloVn7G0t3m1KfngAAAAAAAAAAAAAAAAAAAAAAjyztDrVLqNYoSZK3hXYV1RlOkm+v2oVUpZsczbRLqcY8+dXULqYKz4mpp12Of+NEetcuyLf35NxSuyS/llbAv11DuyiP0s+MgEmyrU3zP7ZBy74mvb8vjzDip452cT7MRKSZ9XP0Vtbv7FW8stIu0FXLTDP5+dx1RcTIe/+LmWWa/n91uDVfbqhW6GhtJjFafNo2B770CnSUTsVBVIyJ7LH2/sXu2sdN7L+bKHt/RxxPnq3xgzm8i7D3y7Y3z3hHU7wjut4v22A/8z0fuf8JARNt8OIZ9lxEtHfkgI3MytuX3ydPWSNa2hCt4Gq3W4jLjta9CnSUbsyqc85YZM8c3adCR7IN5u95l857giWvHD5u2EJ8aLdp/ga6ZBu8bXE7qt4vuvjNixQR9X7x25jefrIpV4sD7v3iE7UusmUkvf/NrPKl2Lay9w+qKdDRULTBws1bNpkQr/u7e7PCEutLofd+2QbLHSvEXh7adb88GGZfLOUTR6ppUL1fXA3uyi9JNMx5JgEtbYgF0CsXS/nkXcay+4J/Zl2O587yrC+Y0xvjbsvBdS7Z+31U58NvQg+tWuzyody4OR0gxj5mM283hnJT45TQzwmlccs4lKUbzwmN3h/KObj3hN4ndEVC/QldkVB/Qlck1J/QFQn1J3RFQv0JXZFQf0JXJNSf0BUJ9Sd0RUL9CV09eMLhYd2+bJm5whpVQvklzAxZ33qLKWGaFzDzVk5MCVfZqQT7FlNMCRvZoQT7i1N1S3hlKxLeCwkfI+G435G6o5ol9DWkg4QkLDKkg4QkLDKkg4QkLDKkg4QkLDKkg4QkLDKkg4Q1SmivfB5uiFFoKx2nWr96Lal35du+5bbS4bigVmgrHSQk4WW3PO1+D638UksmDOXZrnl+qeUSbhTCZGvnF1sqYUDPra9zi7WfDLb/4OeZfSjPdX3rr5qZBuPBz7/2o4SzfyPjwfEd51a1/XvKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOrvDwc5IW1z/m/XAAAAAElFTkSuQmCC';



//     $bin = base64_decode($b64);

//     $im = imageCreateFromString($bin);
    
//     if (!$im) {
//         die('Base64 value is not a valid image');
//     }

//     $micarpeta = '../imagenes';
//     if (!file_exists($micarpeta)) {
//         mkdir($micarpeta, 0777, true);
//     }
 
//     // Specify the location where you want to save the image
//     $date = new DateTime();
//     $date =  $date->format('YmdHis');
//     $img_file = "../imagenes/".$date.".png";

//     imagepng($im, $img_file, 0);
//     $img_file = $LOCALSERVER."imagenes/".$date.".png";
   
//     return $img_file;
// }

function base64ToImage($b64) {
    $LOCALSERVER = 'http://localhost/backrutas/public/';
    $defaultImage = $LOCALSERVER . 'default-image.png'; // Ruta de la imagen por defecto

    // Check if the input is a URL of the specified format
    if (strpos($b64, $LOCALSERVER . 'imagenes/') === 0) {
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

    $micarpeta = '../imagenes';
    if (!file_exists($micarpeta)) {
        mkdir($micarpeta, 0777, true);
    }
 
    // Specify the location where you want to save the image
    $date = new DateTime();
    $date =  $date->format('YmdHis');
    $img_file = "../imagenes/".$date.".png";

    imagepng($im, $img_file, 0);
    $img_file = $LOCALSERVER."imagenes/".$date.".png";
   
    return $img_file;
}
//GET ALL CLIENTS
$app->get('/api/negocio/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_ALL_NEGOCIOS";
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $negocios = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($negocios);
        } else {
            return $response->withJson("No se encontro marcas");
        }
    } catch (PDOException $error) {

        $errores =  array(
            "text" => $error->getMessage()
        );
        return $response->withJson($errores, 500);  
    }   
   
});

$app->get('/api/negocio/list/idNegocio={idNegocio}', function (Request $request, Response $response) {
    try {
        $idNegocio = $request->getAttribute('idNegocio');
        $sql =  "select * from Negocios where $idNegocio = 0 or ID=$idNegocio";
        $cnx = new Conexion();
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($clientes);
        } else {
            return $response->withJson("No existen Negocios");
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
$app->post('/api/negocio/registrar', function (Request $request, Response $response) {

  
    

    $IDRubro				= $request->getParam('IDRubro');
    $Nombre				    = $request->getParam('Nombre');
    $Eslogan				= $request->getParam('Eslogan');
    $Contacto				= $request->getParam('Contacto');

    $Imagen_Ico			    = $request->getParam('Imagen_Ico');
    // echo($Imagen_Ico);
    $rest = base64ToImage($Imagen_Ico);
    // echo($rest);
    $Imagen_Logo			= $request->getParam('Imagen_Logo');
    $IDTurno				= $request->getParam('IDTurno');
    $Delivery				= $request->getParam('Delivery');
    $Telefono				= $request->getParam('Telefono');
    $Celular				= $request->getParam('Celular');
    $Direccion				= $request->getParam('Direccion');
    $Cellphone				= $request->getParam('Cellphone');
    $Email					= $request->getParam('Email');
    $store_url				= $request->getParam('store_url');
    $color_primari			= $request->getParam('color_primari');
    $color_secondari		= $request->getParam('color_secondari');
    $color_contraste		= $request->getParam('color_contraste');
    $UsuarioRegistro		= $request->getParam('UsuarioRegistro');
    $State					= $request->getParam('State');
    $sql = trim("EXEC SP_INSERT_NEGOCIO 
    @IDRubro=" . agregarComillas($IDRubro) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Eslogan=" . agregarComillas($Eslogan) . "			
    ,@Contacto=" . agregarComillas($Contacto) . "				
    ,@Imagen_Ico=" . agregarComillas($rest) . "			
    ,@Imagen_Logo=" . agregarComillas($Imagen_Logo) . "			
    ,@IDTurno=" . agregarComillas($IDTurno) . "				
    ,@Delivery=" . agregarComillas($Delivery) . "				
    ,@Telefono=" . agregarComillas($Telefono) . "				
    ,@Celular=" . agregarComillas($Celular) . "				
    ,@Direccion=" . agregarComillas($Direccion) . "				
    ,@Cellphone=" . agregarComillas($Cellphone) . "				
    ,@Email=" . agregarComillas($Email) . "					
    ,@store_url=" . agregarComillas($store_url) . "				
    ,@color_primari=" . agregarComillas($color_primari) . "			
    ,@color_secondari=" . agregarComillas($color_secondari) . "		
    ,@color_contraste=" . agregarComillas($color_contraste) . "		
    ,@UsuarioRegistro=" . agregarComillas($UsuarioRegistro) . "		
    ,@State=" . agregarComillas($State));
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

$app->put('/api/negocio/modificar', function (Request $request, Response $response) {
    $IDNegocio				= $request->getParam('IDNegocio');		
    $IDRubro				= $request->getParam('IDRubro');
    $Nombre				    = $request->getParam('Nombre');
    $Eslogan				= $request->getParam('Eslogan');
    $Contacto				= $request->getParam('Contacto');
    $Imagen_Ico			    = $request->getParam('Imagen_Ico');
    $rest = base64ToImage($Imagen_Ico);

    $Imagen_Logo			= $request->getParam('Imagen_Logo');
    $IDTurno				= $request->getParam('IDTurno');
    $Delivery				= $request->getParam('Delivery');
    $Telefono				= $request->getParam('Telefono');
    $Celular				= $request->getParam('Celular');
    $Direccion				= $request->getParam('Direccion');
    $Cellphone				= $request->getParam('Cellphone');
    $Email					= $request->getParam('Email');
    $store_url				= $request->getParam('store_url');
    $color_primari			= $request->getParam('color_primari');
    $color_secondari		= $request->getParam('color_secondari');
    $color_contraste		= $request->getParam('color_contraste');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');
    $State					= $request->getParam('State');
    $ACCION	                = $request->getParam('ACCION');

    $sql = trim("EXEC SP_UPDATE_NEGOCIO 
    @ID=" . agregarComillas($IDNegocio) . "	
    ,@IDRubro=" . agregarComillas($IDRubro) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Eslogan=" . agregarComillas($Eslogan) . "			
    ,@Contacto=" . agregarComillas($Contacto) . "				
    ,@Imagen_Ico=" . agregarComillas($rest) . "			
    ,@Imagen_Logo=" . agregarComillas($Imagen_Logo) . "			
    ,@IDTurno=" . agregarComillas($IDTurno) . "				
    ,@Delivery=" . agregarComillas($Delivery) . "				
    ,@Telefono=" . agregarComillas($Telefono) . "				
    ,@Celular=" . agregarComillas($Celular) . "				
    ,@Direccion=" . agregarComillas($Direccion) . "				
    ,@Cellphone=" . agregarComillas($Cellphone) . "				
    ,@Email=" . agregarComillas($Email) . "					
    ,@store_url=" . agregarComillas($store_url) . "				
    ,@color_primari=" . agregarComillas($color_primari) . "			
    ,@color_secondari=" . agregarComillas($color_secondari) . "		
    ,@color_contraste=" . agregarComillas($color_contraste) . "		
    ,@UsuarioModificacion=" . agregarComillas($UsuarioModificacion) . "	
    ,@State=" . agregarComillas($State));
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

$app->post('/api/negocio/eliminar', function (Request $request, Response $response) {
    $IDNegocio				= $request->getParam('IDNegocio');	
    $sql = trim("EXEC SP_DELETE_NEGOCIO 
    @ID=" . agregarComillas($IDNegocio));
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