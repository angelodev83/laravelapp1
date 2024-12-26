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
            DROP VIEW IF EXISTS view_clinical_orders;
            CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_clinical_orders` AS (
            SELECT
            `items`.`id`                        AS `id`,
            `items`.`ndc`                       AS `ndc`,
            `clinical_orders`.`order_number`             AS `order_number`,
            `clinical_orders`.`shipment_tracking_number` AS `shipment_tracking_number`,
            `clinical_orders`.`order_date`               AS `order_date`,
            `clinical_orders`.`comments`                 AS `comments`,
            `clinical_orders`.`pharmacy_store_id`                 AS `pharmacy_store_id`,
            `shipment_statuses`.`name`         AS `shipment_status`,
            `shipment_statuses`.`color`        AS `color`,
            `shipment_statuses`.`id`           AS `shipment_status_id`,
            `shipment_statuses`.`class`        AS `statuses_class`,
            `medications`.`name`               AS `drugname`,
            `medications`.`med_id`             AS `drug_id`,
            `medications`.`rx_price`           AS `rx_price`,
            `medications`.`340b_price`         AS `price_340b`,
            `items`.`inventory_type`            AS `inventory_type`,
            `items`.`quantity`                  AS `quantity`,
            `items`.`order_type`                AS `order_type`,
            `clinics`.`name`                    AS `clinic`,
            `clinics`.`id`                      AS `clinic_id`,
            `prescriptions`.`prescriber_name`   AS `prescriber`
            FROM (((((`clinical_orders`
                JOIN `shipment_statuses`
                    ON (`clinical_orders`.`shipment_status_id` = `shipment_statuses`.`id`))
                JOIN `clinics`
                    ON (`clinical_orders`.`clinic_id` = `clinics`.`id`))
                JOIN `items`
                ON (`items`.`order_id` = `clinical_orders`.`id` AND `items`.`order_type` = 'clinical'))
                JOIN `medications`
                ON (`items`.`medication_id` = `medications`.`med_id`))
            JOIN `prescriptions`
                ON (`clinical_orders`.`order_number` = `prescriptions`.`order_number`)));
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_clinical_orders');
    }
};
