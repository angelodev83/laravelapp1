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
            CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_wholesale_drug_returns` AS (
                SELECT
                    `wholesale_drug_returns`.`id`,
                    `wholesale_drug_returns`.`dispense_quantity`,
                    `wholesale_drug_returns`.`date_filed`,
                    `wholesale_drug_returns`.`reject_comments`,
                    `wholesale_drug_returns`.`status_id` AS wdr_status_id,
                    `wholesale_drug_returns`.`user_id` AS wdr_user_id,
                    `wholesale_drug_returns`.`bin`,
                    `view_pharmacy_drug_orders`.`id` AS item_id,
                    `view_pharmacy_drug_orders`.`ndc`,
                    `view_pharmacy_drug_orders`.`order_number`,
                    `view_pharmacy_drug_orders`.`shipment_tracking_number`,
                    `view_pharmacy_drug_orders`.`order_date`,
                    `view_pharmacy_drug_orders`.`comments`,
                    `view_pharmacy_drug_orders`.`pharmacy_store_id`,
                    `view_pharmacy_drug_orders`.`shipment_status`,
                    `view_pharmacy_drug_orders`.`color`,
                    `view_pharmacy_drug_orders`.`shipment_status_id`,
                    `view_pharmacy_drug_orders`.`statuses_class`,
                    `view_pharmacy_drug_orders`.`drugname`,
                    `view_pharmacy_drug_orders`.`drug_id`,
                    `view_pharmacy_drug_orders`.`rx_price`,
                    `view_pharmacy_drug_orders`.`price_340b`,
                    `view_pharmacy_drug_orders`.`inventory_type`,
                    `view_pharmacy_drug_orders`.`quantity`,
                    `view_pharmacy_drug_orders`.`prescriber`,
                    `view_pharmacy_drug_orders`.`patient_firstname`,
                    `view_pharmacy_drug_orders`.`patient_lastname`
                FROM `wholesale_drug_returns`
                INNER JOIN `view_pharmacy_drug_orders`
                    ON `wholesale_drug_returns`.`drug_order_item_id` = `view_pharmacy_drug_orders`.`id`
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
