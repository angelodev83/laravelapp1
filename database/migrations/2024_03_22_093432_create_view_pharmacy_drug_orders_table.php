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
            CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_pharmacy_drug_orders` AS (
                SELECT
                    `drug_order_items`.`id`                     AS `id`,
                    `drug_order_items`.`ndc`                    AS `ndc`,
                    `drug_orders`.`order_number`                AS `order_number`,
                    `drug_orders`.`shipment_tracking_number`    AS `shipment_tracking_number`,
                    `drug_orders`.`order_date`                  AS `order_date`,
                    `drug_orders`.`comments`                    AS `comments`,
                    `drug_orders`.`pharmacy_store_id`           AS `pharmacy_store_id`,
                    `store_statuses`.`name`                     AS `shipment_status`,
                    `store_statuses`.`color`                    AS `color`,
                    `store_statuses`.`id`                       AS `shipment_status_id`,
                    `store_statuses`.`class`                    AS `statuses_class`,
                    `medications`.`name`                        AS `drugname`,
                    `medications`.`med_id`                      AS `drug_id`,
                    `medications`.`rx_price`                    AS `rx_price`,
                    `medications`.`340b_price`                  AS `price_340b`,
                    `drug_order_items`.`inventory_type`         AS `inventory_type`,
                    `drug_order_items`.`quantity`               AS `quantity`,
                    `pharmacy_prescriptions`.`prescriber_name`  AS `prescriber`,
                    `patients`.`firstname`                      AS `patient_firstname`,
                    `patients`.`lastname`                       AS `patient_lastname`
                FROM `drug_orders`
                LEFT JOIN `store_statuses`
                    ON `drug_orders`.`shipment_status_id` = `store_statuses`.`id`
                INNER JOIN `drug_order_items`
                    ON `drug_order_items`.`order_id` = `drug_orders`.`id`
                INNER JOIN `medications`
                    ON `drug_order_items`.`med_id` = `medications`.`med_id`
                LEFT JOIN `pharmacy_prescriptions`
                    ON `drug_orders`.`pharmacy_prescription_id` = `pharmacy_prescriptions`.`id`
                LEFT JOIN `patients`
                    ON `patients`.`id` = `pharmacy_prescriptions`.`patient_id`
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_pharmacy_drug_orders');
    }
};
