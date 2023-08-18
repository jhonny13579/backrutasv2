<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->post('/api/usuarios/authuser2', function (Request $request, Response $response) {
    $Correo				   = $request->getParam('Correo');
    $Contrasenia				   = $request->getParam('Contrasenia');
    
    $sql = trim("EXEC SP_AUTENTICAR_USUARIO_2
    @Correo=" . agregarComillas($Correo) . "		
    ,@Contrasenia=" . agregarComillas($Contrasenia));
    $sql = preg_replace('/\s+/', ' ', $sql);
       try {
           $cnx = new Conexion();
           $query = $cnx->Conectar();
           $resultado = $query->query($sql);
           $resultado = $resultado->fetchAll(PDO::FETCH_OBJ);
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

$app->post('/api/usuarios/authuser', function (Request $request, Response $response) {
    $Correo				   = $request->getParam('Correo');
    $Contrasenia				   = $request->getParam('Contrasenia');
    
    $sql = trim("EXEC SP_AUTENTICAR_USUARIO
    @Correo=" . agregarComillas($Correo) . "		
    ,@Contrasenia=" . agregarComillas($Contrasenia));
    $sql = preg_replace('/\s+/', ' ', $sql);
       try {
           $cnx = new Conexion();
           $query = $cnx->Conectar();
           $resultado = $query->query($sql);
           $resultado = $resultado->fetchAll(PDO::FETCH_OBJ);
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
   
//GET ALL CLIENTS
$app->get('/api/usuarios/list', function (Request $request, Response $response) {

    try {
        $sql =  "EXEC SP_LISTAR_USUARIOS";
        $cnx = new Conexion();
     
        $query = $cnx->Conectar();
        $resultado = $query->query($sql);
        $categorias = $resultado->fetchAll(PDO::FETCH_OBJ);
        if ($resultado->rowCount() > 0) {
            return $response->withJson($categorias);
        } else {
            return $response->withJson("No se encontro usuarios");
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
$app->post('/api/usuarios/registrar', function (Request $request, Response $response) {
    $IDPerfil				   = $request->getParam('IDPerfil');
    $IDRuta				   = $request->getParam('IDRuta');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $IDTienda				   = $request->getParam('IDTienda');
    $Nombre				    = $request->getParam('Nombre');
    $Correo				   = $request->getParam('Correo');
    $Contraseña				   = $request->getParam('Contraseña');
    $Telefono1				   = $request->getParam('Telefono1');
    $Telefono2				   = $request->getParam('Telefono2');
    $Telefono3				   = $request->getParam('Telefono3');
    $UsuarioRegistro				= $request->getParam('UsuarioRegistro');
  
  
    $sql = trim("EXEC SP_INSERT_USUARIO
    @IDPerfil=" . agregarComillas($IDPerfil) . "		
   ,@IDRuta=" . agregarComillas($IDRuta) . "		
   ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
   ,@IDTienda=" . agregarComillas($IDTienda) . "		
   ,@Nombre=" . agregarComillas($Nombre) . "
   ,@Correo=" . agregarComillas($Correo) . "		
   ,@Contraseña=" . agregarComillas($Contraseña) . "		
   ,@Telefono1=" . agregarComillas($Telefono1) . "	
   ,@Telefono2=" . agregarComillas($Telefono2) . "		
   ,@Telefono3=" . agregarComillas($Telefono3) . "			
   
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
   

   $app->put('/api/usuarios/modificar', function (Request $request, Response $response) {
    $ID		             	= $request->getParam('ID');		
    $IDPerfil				   = $request->getParam('IDPerfil');
    $IDRuta				   = $request->getParam('IDRuta');
    $IDNegocio				   = $request->getParam('IDNegocio');
    $IDTienda				   = $request->getParam('IDTienda');
    $Nombre				    = $request->getParam('Nombre');
    $Correo				   = $request->getParam('Correo');

    $Telefono1				   = $request->getParam('Telefono1');
    $Telefono2				   = $request->getParam('Telefono2');
    $Telefono3				   = $request->getParam('Telefono3');
    $UsuarioModificacion	= $request->getParam('UsuarioModificacion');

    $sql = trim("EXEC SP_UPDATE_USUARIO
    @ID=" . agregarComillas($ID) . "	
   , @IDPerfil=" . agregarComillas($IDPerfil) . "		
    ,@IDRuta=" . agregarComillas($IDRuta) . "		
    ,@IDNegocio=" . agregarComillas($IDNegocio) . "		
    ,@IDTienda=" . agregarComillas($IDTienda) . "		
    ,@Nombre=" . agregarComillas($Nombre) . "
    ,@Correo=" . agregarComillas($Correo) . "		
    
    ,@Telefono1=" . agregarComillas($Telefono1) . "	
    ,@Telefono2=" . agregarComillas($Telefono2) . "		
    ,@Telefono3=" . agregarComillas($Telefono3) . "			
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


// $app->post('/api/usuarios/eliminar', function (Request $request, Response $response) {
//     $ID				= $request->getParam('ID');	
//     $sql = trim("EXEC SP_DELETE_PRODUCTO
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