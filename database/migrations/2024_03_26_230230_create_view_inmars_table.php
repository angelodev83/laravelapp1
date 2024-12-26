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
            CREATE OR REPLACE VIEW view_inmars AS
            SELECT
                `ctclusi`.`inmars`.`id`              AS `id`,
                `ctclusi`.`inmars`.`name`            AS `name`,
                `ctclusi`.`inmars`.`quantity`        AS `quantity`,
                `ctclusi`.`inmars`.`ndc`             AS `ndc`,
                `ctclusi`.`inmars`.`drug_id`         AS `drug_id`,
                (SELECT
                    `intranet`.`medications`.`name`
                FROM `intranet`.`medications`
                WHERE `intranet`.`medications`.`med_id` = `ctclusi`.`inmars`.`drug_id`
                LIMIT 1) AS `drug_name`,
                `ctclusi`.`inmars`.`status`          AS `status`,
                `ctclusi`.`inmars`.`return_date`     AS `return_date`,
                `ctclusi`.`inmars`.`clinic_id`       AS `clinic_id`,
                `ctclusi`.`inmars`.`comments`        AS `comments`,
                `ctclusi`.`inmars`.`prescriber_name` AS `prescriber_name`,
                `ctclusi`.`clinics`.`name`           AS `clinic_name`,
                `ctclusi`.`inmars`.`type`            AS `type`
            FROM `ctclusi`.`inmars`
            JOIN `ctclusi`.`clinics` ON `ctclusi`.`inmars`.`clinic_id` = `ctclusi`.`clinics`.`id`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_inmars');
    }
};
