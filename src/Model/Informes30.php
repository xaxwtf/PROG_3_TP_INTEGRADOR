<?php
namespace App\Model;
use App\Model;
use App\Db\AccesoDatos;
use PDO;
// create document


class Informes30{

    public static function Logo(){
        try{
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML('LA COMADA');
            $mpdf->WriteHTML('<div>TP integrador</div>');
            $r=$mpdf->Output("laComanda.pdf");
        } 
        catch(\Mpdf\MpdfException $e){
            echo $e->getMessage();
        }  
        
    }
    public static function MejoresEncuestas(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *  FROM tp_integrador.encuestas
        where puntuacionPromedio > 6");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
    public static function MesaMasUsada(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesaId , count(mesaid) as vecesUsada FROM tp_integrador.pedidos
        group by (mesaId)");
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_OBJ);
        $max=$resultado[0];
        foreach($resultado as $uno){
            if($uno->vecesUsada>$max->vecesUsada){
                $max=$uno->vecesUsada;
            }
        }
        return $max;
    }
    public static function PedidosEntregadosConDemora(){
        $todos=array();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaId,cliente, estado, codigoAlfa, tiempoPreparacion FROM pedidos where demora>0 , estado = 'entregado' ");
        $consulta->execute();
        $recuperados=$consulta->fetchAll(PDO::FETCH_CLASS);
        $aux;
        for($i=0; $i<count($recuperados); $i++)
        {
            $aux= new Pedido();
            $aux->id=$recuperados[$i]->id;
            $aux->cliente=$recuperados[$i]->cliente;
            $aux->detalle=Detalle::RecuperarDetalle($recuperados[$i]->id);
            $aux->estado=$recuperados[$i]->estado;
            $aux->codigoAlfa=$recuperados[$i]->codigoAlfa;
            $aux->mesaId=$recuperados[$i]->mesaId;
            $aux->tiempoPreparacion=$recuperados[$i]->tiempoPreparacion;
            $todos[count($todos)]=$aux;
        }

        return $todos;
    }
    public static function CantidadDeAccionesxUsuario(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT usuario , count(usuario) as acciones FROM tp_integrador.registro_movimientos
        group by (usuario)");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
    public static function UnidadesVendidasxProducto(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.id, productos.descripcion, productos.tiempo_prom_preparacion, productos.categoria ,productos.precio , count(detalle.id_producto) as ventas FROM tp_integrador.productos
        inner join detalle on detalle.id_producto=productos.id
        group by (productos.id)
        order by (ventas) DESC");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }
    public static function ListadeLogeos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM tp_integrador.registro_movimientos
        where accion= 'el usuario se ha logeado!'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

}














