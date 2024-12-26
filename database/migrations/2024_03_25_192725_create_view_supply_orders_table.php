<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(" 
            CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_supply_orders` AS (
                SELECT
                    `supply_order_items`.`id`                     AS `id`,
                    `supply_orders`.`order_number`                AS `order_number`,
                    `supply_orders`.`shipment_tracking_number`    AS `shipment_tracking_number`,
                    `supply_orders`.`order_date`                  AS `order_date`,
                    `supply_orders`.`comments`                    AS `comments`,
                    `supply_orders`.`pharmacy_store_id`           AS `pharmacy_store_id`,
                    `store_statuses`.`name`                     AS `shipment_status`,
                    `store_statuses`.`color`                    AS `color`,
                    `store_statuses`.`id`                       AS `shipment_status_id`,
                    `store_statuses`.`class`                    AS `statuses_class`,
                    `supply_items`.`description`                AS `item_description`,
                    `supply_items`.`item_number`                  AS `item_number`,
                    `supply_items`.`model_number`                 AS `item_model_number`,
                    `supply_items`.`size`                         AS `size`,
                    `supply_order_items`.`quantity`               AS `quantity`
                FROM `supply_orders`
                LEFT JOIN `store_statuses`
                    ON `supply_orders`.`shipment_status_id` = `store_statuses`.`id`
                INNER JOIN `supply_order_items`
                    ON `supply_order_items`.`order_id` = `supply_orders`.`id`
                INNER JOIN `supply_items`
                    ON `supply_order_items`.`item_id` = `supply_items`.`id`
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_supply_orders');
    }
};
