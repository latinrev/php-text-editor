<?php

$dirPath = './resources/dir.txt';
    function Open(){
        if(isset($_GET['open'])){
            $filename = $_GET['open'];
            $filename = str_replace(' ', '_',$filename);
            if(is_file(GetWorkingDir() . $filename)){
                if($filename != "index.php" && $filename != "resources/functions/crear.php" && $filename != "resources/functions/borrar.php" && $filename != "resources/dir.txt"){
                return OpenFile(GetWorkingDir() . $filename);
                }
            }else{
                //Refactor para luego trate de usar strpos pero no quiso funcionar (?(?(?()))) bug de php
                if($filename != "resources" && $filename != 'resources/functions' && $filename != 'resources/css' && $filename != 'resources/css/icons'){
                    OpenFolder($filename);
                }
            }
        }
    }

    function OpenFile($filename){
                if(is_file($filename)){
                    $info = "";
                    $file = fopen($filename,'r');
                    while(!feof($file)){
                    $info .= fgets($file);
                    }
                return $info;
                }
        }
    function OpenFolder($dirname){
        WriteWorkingDir($dirname);
    }
    
function Delete(){
    if(isset($_GET['delete']))
        {
            if(!empty($_GET['delete']))
            {
            try {
                $fileOrDir = $_GET['delete'];
                $fileOrDir = GetWorkingDir() . $fileOrDir;

                if(strpos($fileOrDir,'resources') == false && strpos($fileOrDir,'now.json') && strpos($fileOrDir,'index.php') == false && strpos($fileOrDir,'../') == false && strpos($fileOrDir,'InstruccionesParaElEditor.txt') && strpos($fileOrDir,'ParaJuanMedina.txt')){
                    if(is_dir($fileOrDir)){
                        removeDir($fileOrDir);
                    }else{
                        removeFile($fileOrDir);
                    }
                }else{
                echo $fileOrDir . " No se puede eliminar permisos denegado archivo o directorio del sistema";
                }
            } catch (\Throwable $th) {
                echo "No hay archivo o directorio con el nombre de " . $_GET['delete'];
            }
        }else{
            echo "El nombre no puede estar vacio";
        }
    }   
}


function removeDir($dirname) {
    try {
        if(count(array_diff(scandir($dirname),['..','.',''])) ==false ){
            rmdir($dirname);
        }else{
            echo "La carpeta contiene archivos o no cuentas con los permisos necesario para eliminarla";
        }
    } catch (\Throwable $th) {
        echo "La carpeta contiene archivos, no existe o no cuentas con los permisos necesario para eliminarla";
    }
}
function removeFile($file){
    try {
        if(is_dir($file)){
            removeDir($file);
        }else{
         unlink($file);
        }
    } catch (\Throwable $th) {
        
        echo "El arhcivo no existe o no cuentas con los permisos necesario para eliminarlo";
    }
}
function createDir( $dirname ) {
    $filePath = GetWorkingDir() . $dirname;
    if ( !is_dir( $filePath ) ) {
        mkdir( $filePath );
    } else {
        echo 'Directorio ya existe';
    }
}

function createFile( $filename ) {
    $filePath =  GetWorkingDir() . $filename;
    $filePath = substr($filePath,0,strlen($filePath)-1);
    if ( !is_file( $filePath ) ) {
        fopen( $filePath, 'w' );
    } else {
        echo 'Archivo ya existe';
    }
}

function Create() {
    if ( isset( $_GET['create'] ) ) {
        if ( !empty( $_GET['create'] ) )
            {
            try {
                $fileOrDir = $_GET['create'];
                $fileOrDir = str_replace(' ', '_',$fileOrDir);
                $fileOrDir =  $fileOrDir . '/';
                if ( strpos( $fileOrDir, 'resources' ) == false && strpos( $fileOrDir, 'index.php' ) == false && strpos( $fileOrDir, '../' ) == false ) {
                    if ( strpos( $fileOrDir, '.' ) == false ) {
                        createDir( $fileOrDir );
                    } else {
                        createFile( $fileOrDir );
                    }
                } else {
                    echo $fileOrDir . ' Es un nombre reservado del sistema porfavor seleccione otro';
                }
            } catch ( \Throwable $th ) {
                echo 'No hay archivo o directorio con el nombre de ' . $_GET['delete'];
            }
        } else {
            echo 'El nombre no puede estar vacio';
        }
    }

}
function Save(){
    if(isset($_POST['canvas'])){
        $saveFilePath = GetWorkingDir() . $_GET['open'];
        file_put_contents($saveFilePath,$_POST['canvas']);
    }
}
function listFiles($dir){
    $files= [];
    if($dir != ""){ 
        $path = "./$dir";
    }else{ $path = './';}
    if(is_dir($path)){
        $sanitized = array_diff(scandir($path),['.','..','','resources','index.php','now.json']);
        foreach ($sanitized as $file){
           array_push($files,$file);
        }       
        return $files;
    }

}

function GetWorkingDir(){
    $file = fopen($GLOBALS['dirPath'],'r');
    $path = fgets($file);
    return $path;
}
function WriteWorkingDir($dirname){
    $file = fopen($GLOBALS['dirPath'],'r+');
    $tmp = fgets($file);
    if($dirname != '../'){
        $newPath = $dirname .'/';
    }else{
        fclose($file);
        $file = fopen($GLOBALS['dirPath'],'w');
        $newPath = substr($tmp,0,strrpos(substr($tmp,0,strlen($tmp)-1),'/'))  ;
        if($newPath != ""){
            $newPath .= '/';
        }
    }
    fwrite($file,$newPath);
    fclose($file);
}

    Create();
    Delete();
    Save();
    $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
    if($pageWasRefreshed ) {
        header('Location: '.$_SERVER['PHP_SELF']);
        die;  
    } else {
    //do nothing;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="resources/css/style.css">
</head>
<body>
    <div class="container">
        <div class="right-part">
            <div class='filename'><?php echo (isset($_GET['open'])) ? $_GET['open'] : ' ' ?></div>
            <form action="" method="post">
                <textarea class='note-block' id="" cols="30" rows="15" name='canvas' value=''; 
                <?php if(isset($_GET['open'])){
                    $filename =  $_GET['open'];
                    if(!is_file(GetWorkingDir() . $_GET['open'])){
                            echo "disabled";
                    }else{
                        if($filename != "index.php" && $filename != "resources/functions/crear.php" && $filename != "resources/functions/borrar.php" && $filename != "resources/dir.txt"){
                            echo "!disabled";
                        }else{
                            echo "disabled";
                        }

                    }
                }
                ?>><?php echo Open() ?></textarea>
                <button class='save' type='submit'>GUARDAR</button>
            </form>
        </div>
        <div class="left-part">
            <form class='create-container' action="<?php $_PHP_SELF ?>" method="get">
                <input name ='create' type="text" class='' placeholder = "Nombre de archivo a crear..."></input>
                <button class='' type='submit'>Crear</button>
            </form>
            <ul class="directory-list">
            <li>
            <div class= 'icon-holder'>
                <a href ='./?open=../' class='icon open' ><i class='material-icons'>folder</i>../</a>
            </div>
  
             </li>
            
            <?php //is_dir(GetWorkingDir() . $file) ? "" : ""
            
            foreach(listFiles(GetWorkingDir()) as $file){ 
                $fileAndIcon = (is_dir(GetWorkingDir() . $file)) ? "<i class='material-icons'>folder</i>" . $file : "<i class='material-icons'>description</i> $file ";

                echo"<li>
                        <div class= 'icon-holder'>
                            <a href ='./?open=$file' class='icon open' >$fileAndIcon</a>
                            <div class='icon-holder'>
                                <a class='icon download'><i class='material-icons'>get_app</i></a>
                                <a href= './?delete=$file' class='icon ' ><i class='material-icons delete'>delete</i></a>
                            </div>
                        </div>

             </li>";} 
             ?>
            </ul>
            
        <div>
    </div>
</div>
</div>
</body>
</html>    
