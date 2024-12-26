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
            CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_wholesale_drug_returns` AS (
                SELECT
                    `wholesale_drug_return_items`.`id`                     AS `id`,
                    `wholesale_drug_return_items`.`ndc`                    AS `ndc`,
                    `wholesale_drug_returns`.`reference_number`            AS `reference_number`,
                    `wholesale_drug_returns`.`shipment_tracking_number`    AS `shipment_tracking_number`,
                    `wholesale_drug_returns`.`date_filed`                  AS `date_filed`,
                    `wholesale_drug_returns`.`reject_comments`             AS `reject_comments`,
                    `wholesale_drug_returns`.`pharmacy_store_id`           AS `pharmacy_store_id`,
                    `wholesale_drug_returns`.`prescriber_name`             AS `prescriber_name`,
                    `store_statuses`.`name`                     AS `shipment_status`,
                    `store_statuses`.`color`                    AS `color`,
                    `store_statuses`.`id`                       AS `shipment_status_id`,
                    `store_statuses`.`class`                    AS `statuses_class`,
                    `medications`.`name`                        AS `drugname`,
                    `medications`.`med_id`                      AS `drug_id`,
                    `medications`.`rx_price`                    AS `rx_price`,
                    `medications`.`340b_price`                  AS `price_340b`,
                    `wholesale_drug_return_items`.`inventory_type`      AS `inventory_type`,
                    `wholesale_drug_return_items`.`dispense_quantity`   AS `dispense_quantity`,
                    `patients`.`firstname`                      AS `patient_firstname`,
                    `patients`.`lastname`                       AS `patient_lastname`
                FROM `wholesale_drug_returns`
                LEFT JOIN `store_statuses`
                    ON `wholesale_drug_returns`.`shipment_status_id` = `store_statuses`.`id`
                INNER JOIN `wholesale_drug_return_items`
                    ON `wholesale_drug_return_items`.`return_id` = `wholesale_drug_returns`.`id`
                INNER JOIN `medications`
                    ON `wholesale_drug_return_items`.`med_id` = `medications`.`med_id`
                LEFT JOIN `patients`
                    ON `patients`.`id` = `wholesale_drug_returns`.`patient_id`
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_wholesale_drug_returns');
    }
};
