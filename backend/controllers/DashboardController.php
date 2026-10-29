<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';

class DashboardController {
    public function stats() {
        require_auth();
        $db = (new DB())->getConnection();

        $stats = [
            "total_productos"  => (int)$db->query("SELECT COUNT(*) FROM productos")->fetchColumn(),
            "stock_total"      => (int)$db->query("SELECT COALESCE(SUM(stock),0) FROM productos")->fetchColumn(),
            "total_ordenes"    => (int)$db->query("SELECT COUNT(*) FROM ordenes")->fetchColumn(),
            "ordenes_pendientes" => (int)$db->query("SELECT COUNT(*) FROM ordenes WHERE estado='pendiente'")->fetchColumn(),
            "ingresos_totales" => (float)$db->query("SELECT COALESCE(SUM(total),0) FROM ordenes WHERE estado <> 'cancelada'")->fetchColumn(),
        ];

        $recientes = $db->query(
            "SELECT id, cliente_nombre, total, estado, created_at
             FROM ordenes ORDER BY id DESC LIMIT 5"
        )->fetchAll();

        json_response(["status" => "success", "stats" => $stats, "ordenes_recientes" => $recientes]);
    }
}