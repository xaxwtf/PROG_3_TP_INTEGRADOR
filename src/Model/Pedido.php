<?php

namespace App\Model;
use App\Db\AccesoDatos;


use PDO;

class Pedido{

    public $id;
    public $mesaId;
    public $cliente;
    public $detalle;
    public $estado;
    public $codigoAlfa;
    public $tiempoPreparacion;
    public $fecha_emision;
    public $fecha_finalizacion;
    //public $imagen;
    public $demora;

    public function __construct(){
        
        
    }
    public static function ObtenerTotal($idPedido){//usado?
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, pedidos.codigoAlfa, sum(productos.precio) as total  FROM tp_integrador.pedidos
        inner join detalle on detalle.id_pedido=pedidos.id
        inner join productos on productos.id=detalle.id_producto
        where pedidos.id= :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_OBJ);
    }

    public static function CalcularTiempoEsperado($idPedido){ ///solo lo usare al subir el detalle
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE Pedidos
        inner join (
            SELECT detalle.id_Pedido as pedido , sum(productos.tiempo_prom_preparacion) as tiempo
            FROM detalle
            inner join productos on detalle.id_producto=productos.id and detalle.estado='pendiente'
            group by(pedido)
            ) as t2 on pedidos.id=t2.pedido
            set pedidos.tiempoPreparacion=t2.tiempo
            where pedidos.id=:idPedido" );
        $consulta->bindValue(':idPedido', $idPedido , PDO::PARAM_INT); 
        $aux=$consulta->execute();

        if($aux){
            $filasAfectadas = $consulta->rowCount();
            if (!$filasAfectadas > 0) {
                $consulta = $objAccesoDatos->prepararConsulta("UPDATE Pedidos
                    set pedidos.tiempoPreparacion=0
                    where pedidos.id=:idPedido" );
                    $consulta->bindValue(':idPedido', $idPedido , PDO::PARAM_INT); 
                    $aux=$consulta->execute();
            }
        }

    }

    private function CrearListaDetalleInDB(){
        for($i=0;$i<count($this->detalle);$i++){
            Detalle::AgregarProducto($this->id, $this->detalle[$i]->id);
        }
        
    }

    public function CreateInDB(){
            
            //$this->fecha_emision= date("Y-m-d H:i:s");
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (mesaid, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion) VALUES ( :mesaId, :cliente, :estado, :codigoAlfa, :tiempoPreparacion, :emision, :final)");
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':codigoAlfa', $this->codigoAlfa, PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', 0, PDO::PARAM_STR);
            $consulta->bindValue(':emision', $this->fecha_emision, PDO::PARAM_STR);
            $consulta->bindValue(':final', $this->fecha_finalizacion, PDO::PARAM_STR);
            $consulta->execute();

            $this->id=$objAccesoDatos->obtenerUltimoId();
            $this->CrearListaDetalleInDB();
            Pedido::CalcularTiempoEsperado($this->id);
            return "Creando Pedido ID: ". $this->id;
       
    }
    public static function TraerTodos(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos");
        $consulta->execute();
        $recuperados=$consulta->fetchAll(PDO::FETCH_CLASS, "App\Model\Pedido");
    
        return $recuperados;
    }
    public static function TraerTodosListos(){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos where estado='listo'");
        $consulta->execute();
        $recuperados=$consulta->fetchAll(PDO::FETCH_CLASS, "App\Model\Pedido");
    
        return $recuperados;
    }
     public static function TraerTodosEntregadosConDemora(){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos where estado='entregado' and demora!=0");
        $consulta->execute();
        $recuperados=$consulta->fetchAll(PDO::FETCH_CLASS, "App\Model\Pedido");
    
        return $recuperados;
    }
    public static function TraerUno($id){
        $r=new Pedido();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos  where id= :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'App\Model\Pedido');
        return $consulta->fetch();
    }

    public static function CrearConArchivoCSV($file){
        $lista=ArchivosCSV::LeerArchivoCSV($file);
        foreach($lista as $r){
            $aux=new Pedido();
            $aux->mesaId=$r["0"];
            $aux->cliente=$r["1"];
            $aux->detalle=Detalle::ObtenerProductosDeCsvString($r["2"]);
            $aux->estado="en Preparacion";
            $aux->generaCodigo5();
            $aux->fecha_emision= date("Y-m-d H:i:s");
            $aux->CreateInDB();
        }
        return "ha cargado datos con un archivo";
    }
    public static function DescargarDatosEnCSV($namefile){
        $lista=Pedido::TraerTodos();
        $contenidoCSV="";
        for($i=0;$i<count($lista);$i++){
            $contenidoCSV= $contenidoCSV . $lista[$i]->id .",". $lista[$i]->mesaId .",". $lista[$i]->cliente.",". $lista[$i]->estado .",". Detalle::GenerarStringDetalleCsv($lista[$i]->detalle) .",". $lista[$i]->codigoAlfa .",". $lista[$i]->tiempoPreparacion."\n";
        }
        ArchivosCSV::EscribirArchivo($namefile,$contenidoCSV);
    }

    public static function TraerUnoXCodAlfa($cod){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos  where codigoAlfa= :cod");
        $consulta->bindValue(':cod',$cod, PDO::PARAM_STR);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, "App\Model\Pedido");
        return $consulta->fetch();

    }
    public static function TraerUltimoId(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedidos WHERE id = (SELECT MAX(id) FROM pedidos)");
        $consulta->execute();
        $aux=$consulta->fetch();
        return $aux['id'];
    }




    function generaCodigo5 () {
        $caracteres = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $codigo = '';
    
        for ($i = 1; $i <= 5; $i++) {
            $codigo = $codigo . $caracteres[$this->numeroAleatorio(0, 35)];
        }
    
        $this->codigoAlfa=$codigo;
    }
    
    function numeroAleatorio ($ninicial, $nfinal) {
        $numero = rand($ninicial, $nfinal);
        return $numero;
    }
    public function CargarDetalleConJson($json){
        $this->detalle= json_decode($json);
    }

    public static function EntregarPedido($codAlf){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET estado = 'entregado' WHERE (codigoAlfa = :cod)");
        $consulta->bindValue(':cod',$codAlf, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos  where codigoAlfa = :id");
        $consulta->bindValue(':id',$codAlf, PDO::PARAM_STR);
        $consulta->execute();
        
        $aux=$consulta->setFetchMode(PDO::FETCH_CLASS,"App\Model\Pedido");
        $r=$consulta->fetch();
        Mesa::CambiarEstadoMesa($r->mesaId,"Cliente Comiendo");
        $r->detalle=Detalle::RecuperarDetalle($r->id);
        return $r;
    }

    public static function CobrarPedido($codAlf){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $ahora=date("Y-m-d H:i:s");
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET estado = 'Finalizado', fecha_finalizacion=:ahora WHERE (codigoAlfa = :cod)");
        $consulta->bindValue(':cod',$codAlf, PDO::PARAM_STR);
        $consulta->bindValue(':ahora',$ahora, PDO::PARAM_STR);
        $consulta->execute();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,  mesaid as mesaId, cliente, estado, codigoAlfa, tiempoPreparacion, fecha_emision, fecha_finalizacion, demora FROM pedidos  where codigoAlfa = :id");
        $consulta->bindValue(':id',$codAlf, PDO::PARAM_STR);
        $consulta->execute();
        
        $aux=$consulta->setFetchMode(PDO::FETCH_CLASS,static::class);
        $r=$consulta->fetch();
        Mesa::CambiarEstadoMesa($r->mesaId,"Libre");
        $r->detalle=Detalle::RecuperarDetalle($r->id);
        
        return $r;
    }
    public static function DemorarPedido($id,$minuntos){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET tp_integrador.pedidos.tiempoPreparacion = tp_integrador.pedidos.tiempoPreparacion + :demora, tp_integrador.pedidos.demora =:demora  WHERE id = :id");
        $consulta->bindValue(':id',$id, PDO::PARAM_INT);
        $consulta->bindValue(':demora',$minuntos, PDO::PARAM_INT);
        return $consulta->execute();
    }
    public static function VerificarEstadoDelPedido($idPedido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tp_integrador.pedidos SET estado='listo' where id=:id and tiempoPreparacion=0");
        $consulta->bindValue(':id',$idPedido, PDO::PARAM_INT);
        return $consulta->execute();
    }
}