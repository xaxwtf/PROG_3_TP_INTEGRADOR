<?php
namespace App\Model;

class ArchivosCSV{

    public static function LeerArchivoCSV($name){
        $lista= array();
        if(file_exists($name)){
            if (($handle = fopen($name, "r")) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    $nuevo=$data;
                    $lista[count($lista)]=$nuevo;
                }
                fclose($handle);
            }
            
        }else{
            echo "no existe!";
        }
        return $lista;
    }
    public static function EscribirArchivo($name, $datosString){
        $aux=false;
        try{
            $archivo = fopen($name,"w");
            if(isset($archivo)){
                echo fwrite($archivo,$datosString);
                $aux=true;
            }
            fclose($archivo);
        }
        catch(\Throwable $th){
            $aux=json_encode(array("mensaje"=>"error,". $th->getMessage()));
            echo $aux;
        }
        finally{
            return $aux;
        }
    }
}
?>