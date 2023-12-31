<?php 

    require '../../includes/app.php';

    use App\Propiedad;
    use App\Vendedor;
    $propiedad = new Propiedad;
    use Intervention\Image\ImageManagerStatic as Image; 

    estaAutenticado();

    //consulta para obtener todos los vendedores
    $vendedores = Vendedor::all();
    

    // Arreglo con mensajes de errores
    $errores = Propiedad::getErrores();

    // Ejecutar el código después de que el usuario envia el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        /**crea una nueva instancia*/
        $propiedad = new Propiedad($_POST['propiedad']);


        /** SUBIDA DE ARCHIVOS */

        // Crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if(!is_dir($carpetaImagenes)) {
             mkdir($carpetaImagenes);
        }
        // Generar un nombre único
        $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg"; 

        //setear la imagen
        //realiza un resize a la imagen con intervention
        if($_FILES['propiedad']['tmp_name']['imagen']){
            $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
            $propiedad->setImagen($nombreImagen);
        }       

        
        //validar
        $errores = $propiedad->validar();

      

        if(empty($errores)) {

            //crear la carpeta para subir las imagenes
            if(!is_dir(CARPETA_IMAGENES)){
                mkdir(CARPETA_IMAGENES);
            }

            //Guarda la imagen en el servidor
            $image->save(CARPETA_IMAGENES . $nombreImagen);
            
            //Guarda en la base de datos
            $propiedad->guardar();

            //Mensaje de exito o error
           
        }
    }

    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        

        <a href="/admin" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php'; ?>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
        
    </main>

<?php 
    incluirTemplate('footer');
?> 