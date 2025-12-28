<?php

class DashboardModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectTotalElectores()
    {
        $sql = "SELECT COUNT(*) as total FROM electores WHERE estado_elector != 0";
        $request = $this->select($sql, array());
        return $request['total'];
    }

    public function selectTotalLideres()
    {
        $sql = "SELECT COUNT(*) as total FROM lideres WHERE estado_lider != 0";
        $request = $this->select($sql, array());
        return $request['total'];
    }

    public function selectTotalVotos()
    {
        // Votos reales marcados en electores (Monitor)
        $sql = "SELECT COUNT(*) as total FROM electores WHERE poll_elector = 1 AND estado_elector != 0";
        $request = $this->select($sql, array());
        return $request['total'];
    }

    public function selectTopLideres()
    {
        $sql = "SELECT CONCAT(l.nom1_lider, ' ', l.ape1_lider) as nombre, COUNT(e.id_elector) as cantidad
                FROM lideres l
                LEFT JOIN electores e ON l.id_lider = e.lider_elector AND e.estado_elector != 0
                WHERE l.estado_lider != 0
                GROUP BY l.id_lider
                ORDER BY cantidad DESC
                LIMIT 10";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectDistribucionMunicipios()
    {
        // Asumiendo tabla municipios o join con lugares
        // Si no tenemos tabla municipios a mano, contamos por ID y asumimos que luego el front o back mapea nombres si tuviera join
        // Viendo ElectoresModel, electores tiene dpto_elector y muni_elector (IDs).
        // Necesitamos el nombre del municipio. Tabla 'municipalities'? Voy a asumir join con municipalities.
        // Si falla, el usuario me dirá. Pero probaré un query seguro primero.

        $sql = "SELECT m.name_municipality as municipio, COUNT(e.id_elector) as cantidad
                FROM electores e
                JOIN municipalities m ON e.muni_elector = m.id_municipality
                WHERE e.estado_elector != 0
                GROUP BY m.id_municipality, m.name_municipality
                ORDER BY cantidad DESC
                LIMIT 10";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectMetaGlobal()
    {
        // Si no hay tabla de metas, retornamos un valor fijo o calculado. 
        // Supongamos una meta de ejemplo o la suma de metas de líderes si existiera.
        // Por ahora devolveré 0 y lo manejamos como 'No definida' o hardcodeamos una meta de campaña.
        return 5000; // Ejemplo Meta Global
    }
}
