<?php

class InfsaldosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectMovimientosElemento($idElemento)
    {
        // Obtener historial unificado de Entradas y Salidas para un elemento
        $sql = "
            (SELECT 
                e.fecha_entrada as fecha,
                'ENTRADA' as tipo,
                t.nombre_tercero as detalle,
                e.cantidad_entrada as cantidad,
                e.unitario_entrada as precio,
                e.total_entrada as total
            FROM entradas e
            INNER JOIN terceros t ON e.tercero_entrada = t.id_tercero
            WHERE e.elemento_entrada = $idElemento AND e.estado_entrada != 0)
            
            UNION ALL
            
            (SELECT 
                s.fecha_salida as fecha,
                'SALIDA' as tipo,
                CONCAT(l.nom1_lider, ' ', l.ape1_lider) as detalle,
                s.cantidad_salida as cantidad,
                0 as precio,
                0 as total
            FROM salidas s
            INNER JOIN lideres l ON s.lider_salida = l.id_lider
            WHERE s.elemento_salida = $idElemento AND s.estado_salida != 0)
            
            ORDER BY fecha ASC
        ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectSaldosGenerales()
    {
        // Resumen de inventario para TODOS los elementos
        // 1. Totales Entradas
        $sqlEntradas = "SELECT elemento_entrada as id_elemento, SUM(cantidad_entrada) as cant_ent, SUM(total_entrada) as dinero_ent 
                        FROM entradas WHERE estado_entrada != 0 GROUP BY elemento_entrada";

        // 2. Totales Salidas
        $sqlSalidas = "SELECT elemento_salida as id_elemento, SUM(cantidad_salida) as cant_sal 
                       FROM salidas WHERE estado_salida != 0 GROUP BY elemento_salida";

        // Obtener elementos
        $sqlElementos = "SELECT id_elemento, nombre_elemento FROM elementos WHERE estado_elemento != 0";
        $elementos = $this->select_all($sqlElementos);

        $entradas = $this->select_all($sqlEntradas);
        $salidas = $this->select_all($sqlSalidas);

        // Procesar en PHP (es más fácil que un query complejo en MySQL 5.7/8.0 sin CTEs o vistas)
        $data = [];

        // Indexar entradas y salidas
        $entMap = [];
        foreach ($entradas as $e) $entMap[$e['id_elemento']] = $e;

        $salMap = [];
        foreach ($salidas as $s) $salMap[$s['id_elemento']] = $s;

        foreach ($elementos as $el) {
            $id = $el['id_elemento'];
            $cantEnt = isset($entMap[$id]) ? $entMap[$id]['cant_ent'] : 0;
            $dineroEnt = isset($entMap[$id]) ? $entMap[$id]['dinero_ent'] : 0;
            $cantSal = isset($salMap[$id]) ? $salMap[$id]['cant_sal'] : 0;

            $saldoCant = $cantEnt - $cantSal;

            // Precio Promedio Ponderado = Total Dinero / Total Cantidad_Entrada
            $precioPromedio = ($cantEnt > 0) ? ($dineroEnt / $cantEnt) : 0;

            // Saldo Valorizado
            $saldoPesos = $saldoCant * $precioPromedio;

            $data[] = [
                'id_elemento' => $id,
                'nombre_elemento' => $el['nombre_elemento'],
                'saldo_cantidad' => $saldoCant,
                'precio_promedio' => $precioPromedio,
                'saldo_pesos' => $saldoPesos
            ];
        }

        return $data;
    }
}
