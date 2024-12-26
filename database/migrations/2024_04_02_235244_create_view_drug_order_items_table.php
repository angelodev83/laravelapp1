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
            CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_drug_order_items` AS (
                SELECT 
                    doiid.*,
                    dos.order_number AS do_order_number,
                    dos.order_date AS do_order_date,
                    dos.`comments` AS do_comments,
                    dos.`created_at` AS do_created_at,
                    dos.`updated_at` AS do_updated_at,
                    dos.po_name AS do_po_name,
                    dos.account_number AS do_account_number,
                    dos.wholesaler_name AS do_wholesaler_name,
                    eu.id AS do_created_by_eid,
                    eu.user_id AS do_created_by_uid,
                    eu.firstname AS do_created_by_firstname,
                    eu.lastname AS do_created_by_lastname,
                    eu.initials_random_color AS do_created_by_initials_random_color,
                    CONCAT(eu.firstname,' ',eu.lastname) AS do_created_by_fullname,
                    eu.image AS do_created_by_avatar,
                    e.id AS task_assignee_eid,
                    e.firstname AS task_assignee_firstname,
                    e.lastname AS task_assignee_lastname,
                    e.initials_random_color AS task_assignee_initials_random_color,
                    CONCAT(e.firstname,' ',e.lastname) AS task_assignee_fullname,
                    e.image AS task_assignee_avatar,
                    e.user_id AS task_assignee_uid,
                    dos.`pharmacy_store_id`,
                    ss.name AS status_name,
                    ss.id AS status_id,
                    ss.color AS status_color,
                    ss.class AS status_class,
                    ss.text_color AS status_text_color,
                    ss.sort AS status_sort,
                    sd.path AS store_document_path,
                    sd.ext AS store_document_ext,
                    t.id AS task_id,
                    t.subject AS task_subject,
                    t.description AS task_description,
                    t.due_date AS task_due_date,
                    t.priority_status_id AS task_priority_status_id,
                    sst.name AS task_priority_status_name,
                    sst.color AS task_priority_status_color,
                    sst.class AS task_priority_status_class,
                    sst.text_color AS task_priority_status_text_color,
                    sst.sort AS task_priority_status_sort
                FROM drug_order_items_import_data AS doiid
                INNER JOIN drug_orders dos ON dos.id = doiid.drug_order_id
                INNER JOIN store_statuses ss ON ss.id = dos.`status_id`
                INNER JOIN store_documents sd ON sd.id = doiid.store_document_id
                INNER JOIN tasks t ON t.id = dos.`task_id`
                INNER JOIN users u ON u.id = dos.`user_id`
                LEFT JOIN employees e ON e.id = t.`assigned_to_employee_id`
                INNER JOIN employees eu ON eu.user_id = u.`id`
                INNER JOIN store_statuses sst ON sst.id = t.`priority_status_id`
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_drug_order_items');
    }
};
