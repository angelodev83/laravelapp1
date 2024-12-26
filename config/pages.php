<?php

return [
    /****************
     * System Users
     ***************/

    //  Users
    ['name'=>'user.index','display_name'=>'User List','division_name'=>'system_settings','group_name'=>'user'],
    ['name'=>'user.create','display_name'=>'Create User','division_name'=>'system_settings','group_name'=>'user'],
    ['name'=>'user.update','display_name'=>'Update User','division_name'=>'system_settings','group_name'=>'user'],
    ['name'=>'user.delete','display_name'=>'Delete User','division_name'=>'system_settings','group_name'=>'user'],

    // Roles
    ['name'=>'role.index','display_name'=>'Role List','division_name'=>'system_settings','group_name'=>'role'],
    ['name'=>'role.create','display_name'=>'Create Role','division_name'=>'system_settings','group_name'=>'role'],
    ['name'=>'role.update','display_name'=>'Update Role','division_name'=>'system_settings','group_name'=>'role'],
    ['name'=>'role.delete','display_name'=>'Delete Role','division_name'=>'system_settings','group_name'=>'role'],

    // RBAC
    ['name'=>'rbac.index','display_name'=>'Role-Based Access Control List','division_name'=>'system_settings','group_name'=>'rbac'],

    /*******************
     * Pharmacy Settings
     *******************/

    //  Pharmacy Staff
    ['name'=>'pharmacy_staff.index','display_name'=>'Pharmacy List','division_name'=>'system_settings','group_name'=>'pharmacy_staff'],
    ['name'=>'pharmacy_staff.create','display_name'=>'Add Staff assgined to Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_staff'],
    ['name'=>'pharmacy_staff.update','display_name'=>'Update Staff assgined to Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_staff'],
    ['name'=>'pharmacy_staff.delete','display_name'=>'Remove Staff assgined to Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_staff'],

    // Pharmacy Store
    ['name'=>'pharmacy_store.index','display_name'=>'Pharmacy Store List','division_name'=>'system_settings','group_name'=>'pharmacy_store'],
    ['name'=>'pharmacy_store.create','display_name'=>'Create Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_store'],
    ['name'=>'pharmacy_store.update','display_name'=>'Update Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_store'],
    ['name'=>'pharmacy_store.delete','display_name'=>'Delete Pharmacy Store','division_name'=>'system_settings','group_name'=>'pharmacy_store'],
    

    // Pharmacy Operations
    ['name'=>'pharmacy_operation.index','display_name'=>'Pharmacy Operations List','division_name'=>'system_settings','group_name'=>'pharmacy_operation'],
    ['name'=>'pharmacy_operation.create','display_name'=>'Create Pharmacy Operations','division_name'=>'system_settings','group_name'=>'pharmacy_operation'],
    ['name'=>'pharmacy_operation.update','display_name'=>'Update Pharmacy Operations','division_name'=>'system_settings','group_name'=>'pharmacy_operation'],
    ['name'=>'pharmacy_operation.delete','display_name'=>'Delete Pharmacy Operations','division_name'=>'system_settings','group_name'=>'pharmacy_operation'],

    /*********************
     * Executive Dashboard
     *********************/
    ['name'=>'executive_dashboard.index','display_name'=>'View Executive Dashboard','division_name'=>'general','group_name'=>'executive_dashboard'],

    /*********************
     * ACCOUNTING
     *********************/
    ['name'=>'accounting.sales_monitoring.index','display_name'=>'View Accounting Sales Monitoring','division_name'=>'general','group_name'=>'accounting'],
    ['name'=>'accounting.payroll_percentage.index','display_name'=>'View Accounting Payroll Percentage','division_name'=>'general','group_name'=>'accounting'],
    ['name'=>'accounting.ar_aging.index','display_name'=>'View Accounting AR Aging','division_name'=>'general','group_name'=>'accounting'],
    ['name'=>'accounting.profitability.index','display_name'=>'View Accounting Profitability','division_name'=>'general','group_name'=>'accounting'],
    ['name'=>'accounting.partnershiip_reconciliation.index','display_name'=>'View Accounting Partnership Reconciliation','division_name'=>'general','group_name'=>'accounting'],
    ['name'=>'accounting.accounts_payable.index','display_name'=>'View Accounting Accounts Payable','division_name'=>'general','group_name'=>'accounting'],

    /*********************
     * HUMAN RESOURCE
     *********************/
    // Hub
    ['name'=>'hr.hub.index','display_name'=>'HR Hub List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.hub.create','display_name'=>'Create HR Hub','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.hub.update','display_name'=>'Update HR Hub','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.hub.delete','display_name'=>'Delete HR Hub','division_name'=>'general','group_name'=>'hr'],

    // Employees
    ['name'=>'hr.employees.index','display_name'=>'Employees List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employees.create','display_name'=>'Create Employees','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employees.update','display_name'=>'Update Employees','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employees.delete','display_name'=>'Delete Employees','division_name'=>'general','group_name'=>'hr'],
    // Employee Reviews
    ['name'=>'hr.employee_reviews.index','display_name'=>'Employee Reviews List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employee_reviews.create','display_name'=>'Create Employee Reviews','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employee_reviews.update','display_name'=>'Update Employee Reviews','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.employee_reviews.delete','display_name'=>'Delete Employee Reviews','division_name'=>'general','group_name'=>'hr'],

    // Recruitment & Hiring
    ['name'=>'hr.recruitment_and_hiring.index','display_name'=>'Recruitment & Hiring List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.recruitment_and_hiring.create','display_name'=>'Create Recruitment & Hiring','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.recruitment_and_hiring.update','display_name'=>'Update Recruitment & Hiring','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.recruitment_and_hiring.delete','display_name'=>'Delete Recruitment & Hiring','division_name'=>'general','group_name'=>'hr'],
    // Announcements
    ['name'=>'hr.announcements.index','display_name'=>'Announcements List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.announcements.create','display_name'=>'Create Announcements','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.announcements.update','display_name'=>'Update Announcements','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.announcements.delete','display_name'=>'Delete Announcements','division_name'=>'general','group_name'=>'hr'],

    // File Manager
    ['name'=>'hr.file_manager.index','display_name'=>'HR File Manager List','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.file_manager.create','display_name'=>'Create HR File Manager','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.file_manager.update','display_name'=>'Update HR File Manager','division_name'=>'general','group_name'=>'hr'],
    ['name'=>'hr.file_manager.delete','display_name'=>'Delete HR File Manager','division_name'=>'general','group_name'=>'hr'],

    /****************************
     * COMPLIANCE AND REGULATORY
     ***************************/
    ['name'=>'cnr.oig_check.index','display_name'=>'View Compliance & Regulatory OIG Check','division_name'=>'general','group_name'=>'cnr'],
    ['name'=>'cnr.oig_list.index','display_name'=>'View Compliance & Regulatory OIG List','division_name'=>'general','group_name'=>'cnr'],
    ['name'=>'cnr.licensure.index','display_name'=>'View Compliance & Regulatory Licensure','division_name'=>'general','group_name'=>'cnr'],
    ['name'=>'cnr.audits.index','display_name'=>'View Compliance & Regulatory Audits','division_name'=>'general','group_name'=>'cnr'],
    ['name'=>'cnr.provider_manuals.index','display_name'=>'View Compliance & Regulatory Provider Manuals','division_name'=>'general','group_name'=>'cnr'],
    ['name'=>'cnr.bop.index','display_name'=>'View Compliance & Regulatory BOP','division_name'=>'general','group_name'=>'cnr'],

    /*********************
     * STORES PAGES
     *********************/

    // BULLETIN
    // BULLETIN > Dashboard
    ['name'=>'menu_store.bulletin.dashboard.index','display_name'=>'View Bulletin Dashboard','division_name'=>'menu_store','group_name'=>'bulletin'],

    // BULLETIN > Task Reminders
    ['name'=>'menu_store.bulletin.task_reminders.index','display_name'=>'View Bulletin Task Reminders Assigned/Created','division_name'=>'menu_store','group_name'=>'bulletin'],
    ['name'=>'menu_store.bulletin.task_reminders.create','display_name'=>'Create Bulletin Task Reminder','division_name'=>'menu_store','group_name'=>'bulletin'],
    ['name'=>'menu_store.bulletin.task_reminders.update','display_name'=>'Update Bulletin Task Reminder','division_name'=>'menu_store','group_name'=>'bulletin'],
    ['name'=>'menu_store.bulletin.task_reminders.delete','display_name'=>'Delete Bulletin Task Reminder','division_name'=>'menu_store','group_name'=>'bulletin'],
    ['name'=>'menu_store.bulletin.task_reminders.view_all','display_name'=>'View Bulletin ALL Task Reminders','division_name'=>'menu_store','group_name'=>'bulletin'],

    // BULLETIN > Launch Pads
    ['name'=>'menu_store.bulletin.launch_pad.index','display_name'=>'View Bulletin Launch Pad','division_name'=>'menu_store','group_name'=>'bulletin'],

    // OPERTAIONS
    // OPERTAIONS > Mail Orders
    ['name'=>'menu_store.operations.mail_orders.index','display_name'=>'Operations Mail Orders List','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.mail_orders.create','display_name'=>'Create Operation Mail Orders','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.mail_orders.update','display_name'=>'Update Operation Mail Orders','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.mail_orders.delete','display_name'=>'Delete Operation Mail Orders','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.mail_orders.download','display_name'=>'Download PDF Operation Mail Orders','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.mail_orders.upload','display_name'=>'Upload PDF Operation Mail Orders','division_name'=>'menu_store','group_name'=>'operations'],
   

    // OPERTAIONS > Return To Stock
    ['name'=>'menu_store.operations.rts.index','display_name'=>'Operations Return To Stock List','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.rts.create','display_name'=>'Create Operation Return To Stock','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.rts.update','display_name'=>'Update Operation Return To Stock','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.rts.delete','display_name'=>'Delete Operation Return To Stock','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.rts.export','display_name'=>'Export Operation Return To Stock','division_name'=>'menu_store','group_name'=>'operations'],

    // OPERTAIONS > For shipping today
    ['name'=>'menu_store.operations.for_shipping_today.index','display_name'=>'Operations For Shipping Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_shipping_today.create','display_name'=>'Create Operation For Shipping Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_shipping_today.update','display_name'=>'Update Operation For Shipping Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_shipping_today.delete','display_name'=>'Delete Operation For Shipping Today','division_name'=>'menu_store','group_name'=>'operations'],

    // OPERTAIONS > For delivery today
    ['name'=>'menu_store.operations.for_delivery_today.index','display_name'=>'Operations For Delivery Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_delivery_today.create','display_name'=>'Create Operation For Delivery Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_delivery_today.update','display_name'=>'Update Operation For Delivery Today','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.for_delivery_today.delete','display_name'=>'Delete Operation For Delivery Today','division_name'=>'menu_store','group_name'=>'operations'],

    // OPERTAIONS > Meetings
    ['name'=>'menu_store.operations.meetings.index','display_name'=>'Operations > Meetings','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.meetings.create','display_name'=>'Create Operation > Meetings','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.meetings.update','display_name'=>'Update Operation > Meetings','division_name'=>'menu_store','group_name'=>'operations'],
    ['name'=>'menu_store.operations.meetings.delete','display_name'=>'Delete Operation > Meetings','division_name'=>'menu_store','group_name'=>'operations'],

    // FINANCIAL REPORTS
    // FINANCIAL REPORTS > Pharmacy Gross Revenue
    ['name'=>'menu_store.financial_reports.pharmacy_gross_revenue.index','display_name'=>'View Financial Reports Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.pharmacy_gross_revenue.create','display_name'=>'Create Financial Reports Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.pharmacy_gross_revenue.update','display_name'=>'Update Financial Reports Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.pharmacy_gross_revenue.delete','display_name'=>'Delete Financial Reports Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Payments Overview
    ['name'=>'menu_store.financial_reports.payments_overview.index','display_name'=>'View Financial Reports Payments Overview','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payments_overview.create','display_name'=>'Create Financial Reports Payments Overview','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payments_overview.update','display_name'=>'Update Financial Reports Payments Overview','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payments_overview.delete','display_name'=>'Delete Financial Reports Payments Overview','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Collected Payments
    ['name'=>'menu_store.financial_reports.collected_payments.index','display_name'=>'View Financial Reports Collected Payments','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.collected_payments.create','display_name'=>'Create Financial Reports Collected Payments','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.collected_payments.update','display_name'=>'Update Financial Reports Collected Payments','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.collected_payments.delete','display_name'=>'Delete Financial Reports Collected Payments','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Gross Revenue and Cogs
    ['name'=>'menu_store.financial_reports.gross_revenue_and_cogs.index','display_name'=>'View Financial Reports Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.gross_revenue_and_cogs.create','display_name'=>'Create Financial Reports Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.gross_revenue_and_cogs.update','display_name'=>'Update Financial Reports Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.gross_revenue_and_cogs.delete','display_name'=>'Delete Financial Reports Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Remittance Advice
    ['name'=>'menu_store.financial_reports.remittance_advice.index','display_name'=>'View Financial Reports Remittance Advice List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.remittance_advice.create','display_name'=>'Create Financial Reports Remittance Advice','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.remittance_advice.update','display_name'=>'Update Financial Reports Remittance Advice','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.remittance_advice.delete','display_name'=>'Delete Financial Reports Remittance Advice','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Payroll Percentage
    ['name'=>'menu_store.financial_reports.payroll_percentage.index','display_name'=>'View Financial Reports Payroll Percentage List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.create','display_name'=>'Create Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.update','display_name'=>'Update Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.delete','display_name'=>'Delete Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Scalable Exp. Analyzer
    ['name'=>'menu_store.financial_reports.scalable_exp_analyzer.index','display_name'=>'View Financial Reports Scalable Exp. Analyzer List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.scalable_exp_analyzer.create','display_name'=>'Create Financial Reports Scalable Exp. Analyzer','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.scalable_exp_analyzer.update','display_name'=>'Update Financial Reports Scalable Exp. Analyzer','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.scalable_exp_analyzer.delete','display_name'=>'Delete Financial Reports Scalable Exp. Analyzer','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Cash Flow
    ['name'=>'menu_store.financial_reports.cash_flow.index','display_name'=>'View Financial Reports Cash Flow List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.cash_flow.create','display_name'=>'Create Financial Reports Cash Flow','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.cash_flow.update','display_name'=>'Update Financial Reports Cash Flow','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.cash_flow.delete','display_name'=>'Delete Financial Reports Cash Flow','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Xero PL (Profit and Loss)
    ['name'=>'menu_store.financial_reports.xero_pl.index','display_name'=>'View Financial Reports Profit and Loss List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.xero_pl.create','display_name'=>'Create Financial Reports Profit and Loss','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.xero_pl.update','display_name'=>'Update Financial Reports Profit and Loss','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.xero_pl.delete','display_name'=>'Delete Financial Reports Profit and Loss','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Payroll Percentage
    ['name'=>'menu_store.financial_reports.payroll_percentage.index','display_name'=>'View Financial Reports Payroll Percentage List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.create','display_name'=>'Create Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.update','display_name'=>'Update Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.payroll_percentage.delete','display_name'=>'Delete Financial Reports Payroll Percentage','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > EOD Reports
    ['name'=>'menu_store.financial_reports.eod_reports.index','display_name'=>'View Financial Reports EOD Reports List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports.create','display_name'=>'Create Financial Reports EOD Reports','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports.update','display_name'=>'Update Financial Reports EOD Reports','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports.delete','display_name'=>'Delete Financial Reports EOD Reports','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > EOD Reports > NUC01
    ['name'=>'menu_store.financial_reports.eod_reports_nuc01.index','display_name'=>'View Financial Reports EOD Reports TRP-NUC01 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc01.create','display_name'=>'Create Financial Reports EOD Reports TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc01.update','display_name'=>'Update Financial Reports EOD Reports TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc01.delete','display_name'=>'Delete Financial Reports EOD Reports TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    // FINANCIAL REPORTS > EOD Reports > NUC05
    ['name'=>'menu_store.financial_reports.eod_reports_nuc05.index','display_name'=>'View Financial Reports EOD Reports TRP-NUC05 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc05.create','display_name'=>'Create Financial Reports EOD Reports TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc05.update','display_name'=>'Update Financial Reports EOD Reports TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc05.delete','display_name'=>'Delete Financial Reports EOD Reports TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    // FINANCIAL REPORTS > EOD Reports > NUC06
    ['name'=>'menu_store.financial_reports.eod_reports_nuc06.index','display_name'=>'View Financial Reports EOD Reports TRP-NUC06 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc06.create','display_name'=>'Create Financial Reports EOD Reports TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc06.update','display_name'=>'Update Financial Reports EOD Reports TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.eod_reports_nuc06.delete','display_name'=>'Delete Financial Reports EOD Reports TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Transaction Receipts
    ['name'=>'menu_store.financial_reports.transaction_receipts.index','display_name'=>'View Financial Reports Transaction Receipts List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts.create','display_name'=>'Create Financial Reports Transaction Receipts','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts.update','display_name'=>'Update Financial Reports Transaction Receipts','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts.delete','display_name'=>'Delete Financial Reports Transaction Receipts','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // FINANCIAL REPORTS > Transaction Receipts > NUC01
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc01.index','display_name'=>'View Financial Reports Transaction Receipts TRP-NUC01 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc01.create','display_name'=>'Create Financial Reports Transaction Receipts TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc01.update','display_name'=>'Update Financial Reports Transaction Receipts TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc01.delete','display_name'=>'Delete Financial Reports Transaction Receipts TRP-NUC01','division_name'=>'menu_store','group_name'=>'financial_reports'],
    // FINANCIAL REPORTS > Transaction Receipts > NUC05
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc05.index','display_name'=>'View Financial Reports Transaction Receipts TRP-NUC05 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc05.create','display_name'=>'Create Financial Reports Transaction Receipts TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc05.update','display_name'=>'Update Financial Reports Transaction Receipts TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc05.delete','display_name'=>'Delete Financial Reports Transaction Receipts TRP-NUC05','division_name'=>'menu_store','group_name'=>'financial_reports'],
    // FINANCIAL REPORTS > Transaction Receipts > NUC06
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc06.index','display_name'=>'View Financial Reports Transaction Receipts TRP-NUC06 List','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc06.create','display_name'=>'Create Financial Reports Transaction Receipts TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc06.update','display_name'=>'Update Financial Reports Transaction Receipts TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],
    ['name'=>'menu_store.financial_reports.transaction_receipts_nuc06.delete','display_name'=>'Delete Financial Reports Transaction Receipts TRP-NUC06','division_name'=>'menu_store','group_name'=>'financial_reports'],

    // DATA INSIGHTS
    // DATA INSIGHTS > Pharmacy Gross Revenue
    ['name'=>'menu_store.data_insights.pgr.index','display_name'=>'View Data Insights Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.pgr.create','display_name'=>'Create Data Insights Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.pgr.update','display_name'=>'Update Data Insights Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.pgr.delete','display_name'=>'Delete Data Insights Pharmacy Gross Revenue','division_name'=>'menu_store','group_name'=>'data_insights'],

    // DATA INSIGHTS > Payments Overview
    ['name'=>'menu_store.data_insights.payments_overview.index','display_name'=>'View Data Insights Payments Overview','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.payments_overview.create','display_name'=>'Create Data Insights Payments Overview','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.payments_overview.update','display_name'=>'Update Data Insights Payments Overview','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.payments_overview.delete','display_name'=>'Delete Data Insights Payments Overview','division_name'=>'menu_store','group_name'=>'data_insights'],

    // DATA INSIGHTS > Collected Payments
    ['name'=>'menu_store.data_insights.collected_payments.index','display_name'=>'View Data Insights Collected Payments','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.collected_payments.create','display_name'=>'Create Data Insights Collected Payments','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.collected_payments.update','display_name'=>'Update Data Insights Collected Payments','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.collected_payments.delete','display_name'=>'Delete Data Insights Collected Payments','division_name'=>'menu_store','group_name'=>'data_insights'],

    // DATA INSIGHTS > Gross Revenue and Cogs
    ['name'=>'menu_store.data_insights.gross_revenue_and_cogs.index','display_name'=>'View Data Insights Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_revenue_and_cogs.create','display_name'=>'Create Data Insights Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_revenue_and_cogs.update','display_name'=>'Update Data Insights Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_revenue_and_cogs.delete','display_name'=>'Delete Data Insights Gross Revenue and Cogs','division_name'=>'menu_store','group_name'=>'data_insights'],

    // DATA INSIGHTS > Account Receivables
    ['name'=>'menu_store.data_insights.account_receivables.index','display_name'=>'View Data Insights Account Receivables List','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.account_receivables.create','display_name'=>'Create Data Insights Account Receivables','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.account_receivables.update','display_name'=>'Update Data Insights Account Receivables','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.account_receivables.delete','display_name'=>'Delete Data Insights Account Receivables','division_name'=>'menu_store','group_name'=>'data_insights'],

    // DATA INSIGHTS > Gross Sales
    ['name'=>'menu_store.data_insights.gross_sales.index','display_name'=>'View Data Insights Gross Sales','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_sales.create','display_name'=>'Create Data Insights Gross Sales','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_sales.update','display_name'=>'Update Data Insights Gross Sales','division_name'=>'menu_store','group_name'=>'data_insights'],
    ['name'=>'menu_store.data_insights.gross_sales.delete','display_name'=>'Delete Data Insights Gross Sales','division_name'=>'menu_store','group_name'=>'data_insights'],

    // CLINICAL
    // CLINICAL > KPI
    ['name'=>'menu_store.clinical.kpi.index','display_name'=>'Clinical KPI List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.kpi.create','display_name'=>'Create Clinical KPI','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.kpi.update','display_name'=>'Update Clinical KPI','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.kpi.delete','display_name'=>'Delete Clinical KPI','division_name'=>'menu_store','group_name'=>'clinical'],
    
    // CLINICAL > Outreach
    ['name'=>'menu_store.clinical.outreach.index','display_name'=>'Clinical outreach List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.outreach.create','display_name'=>'Create Clinical outreach','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.outreach.update','display_name'=>'Update Clinical outreach','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.outreach.delete','display_name'=>'Delete Clinical outreach','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Prio Authroization
    ['name'=>'menu_store.clinical.prio_authorization.index','display_name'=>'Clinical Prio Authorization List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.prio_authorization.create','display_name'=>'Create Clinical Prio Authorization','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.prio_authorization.update','display_name'=>'Update Clinical Prio Authorization','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.prio_authorization.delete','display_name'=>'Delete Clinical Prio Authorization','division_name'=>'menu_store','group_name'=>'clinical'],
    
    // CLINICAL > Tebra Patients
    ['name'=>'menu_store.clinical.tebra_patients.index','display_name'=>'Clinical Tebra Patients List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.tebra_patients.create','display_name'=>'Create Clinical Tebra Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.tebra_patients.update','display_name'=>'Update Clinical Tebra Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.tebra_patients.delete','display_name'=>'Delete Clinical Tebra Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    // CLINICAL > Pioneer Patients
    ['name'=>'menu_store.clinical.pioneer_patients.index','display_name'=>'Clinical Pioneer Patients List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pioneer_patients.create','display_name'=>'Create Clinical Pioneer Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pioneer_patients.update','display_name'=>'Update Clinical Pioneer Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pioneer_patients.delete','display_name'=>'Delete Clinical Pioneer Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    // CLINICAL > MTM, Outcomes Reports
    ['name'=>'menu_store.clinical.mtm_outcomes_report.index','display_name'=>'View Clinical MTM, Outcomes Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.mtm_outcomes_report.create','display_name'=>'Create Clinical MTM, Outcomes Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.mtm_outcomes_report.update','display_name'=>'Update Clinical MTM, Outcomes Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.mtm_outcomes_report.delete','display_name'=>'Delete Clinical MTM, Outcomes Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    // CLINICAL > Adherence Reports
    ['name'=>'menu_store.clinical.adherence_report.index','display_name'=>'View Clinical Adherence Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.adherence_report.create','display_name'=>'Create Clinical Adherence Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.adherence_report.update','display_name'=>'Update Clinical Adherence Reports','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.adherence_report.delete','display_name'=>'Delete Clinical Adherence Reports','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Meetings
    ['name'=>'menu_store.clinical.meetings.index','display_name'=>'Clinical > Meetings','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.meetings.create','display_name'=>'Create Clinical > Meetings','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.meetings.update','display_name'=>'Update Clinical > Meetings','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.meetings.delete','display_name'=>'Delete Clinical > Meetings','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Renewal
    ['name'=>'menu_store.clinical.renewals.index','display_name'=>'Clinical > Renewal List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.renewals.create','display_name'=>'Create Clinical > Renewal','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.renewals.update','display_name'=>'Update Clinical > Renewal','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.renewals.delete','display_name'=>'Delete Clinical > Renewal','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.renewals.archive','display_name'=>'Archive or Un-archive Clinical > Renewal','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.renewals.export','display_name'=>'Export Clinical > Renewal List','division_name'=>'menu_store','group_name'=>'clinical'],

    // Automations:

    // CLINICAL > Pending Fill Request
    ['name'=>'menu_store.clinical.pending_fill_request.index','display_name'=>'Clinical > Pending Fill Request List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_fill_request.create','display_name'=>'Create Clinical > Pending Fill Request','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_fill_request.update','display_name'=>'Update Clinical > Pending Fill Request','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_fill_request.delete','display_name'=>'Delete Clinical > Pending Fill Request','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Brand Switching IOU
    ['name'=>'menu_store.clinical.brand_switchings.index','display_name'=>'Clinical > Brand Switching IOU List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.brand_switchings.create','display_name'=>'Create Clinical > Brand Switching IOU','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.brand_switchings.update','display_name'=>'Update Clinical > Brand Switching IOU','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.brand_switchings.delete','display_name'=>'Delete Clinical > Brand Switching IOU','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Therapy change+reco for existing patients
    ['name'=>'menu_store.clinical.therapy_change_and_reco.index','display_name'=>'Clinical > Therapy change+reco for existing patients List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.therapy_change_and_reco.create','display_name'=>'Create Clinical > Therapy change+reco for existing patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.therapy_change_and_reco.update','display_name'=>'Update Clinical > Therapy change+reco for existing patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.therapy_change_and_reco.delete','display_name'=>'Delete Clinical > Therapy change+reco for existing patients','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > THRC px+Rx Daily Census
    ['name'=>'menu_store.clinical.rx_daily_census.index','display_name'=>'Clinical > THRC px+Rx Daily Census List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_census.create','display_name'=>'Create Clinical > THRC px+Rx Daily Census','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_census.update','display_name'=>'Update Clinical > THRC px+Rx Daily Census','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_census.delete','display_name'=>'Delete Clinical > THRC px+Rx Daily Census','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Daily Rx Transfers
    ['name'=>'menu_store.clinical.rx_daily_transfers.index','display_name'=>'Clinical > Daily Rx Transfers List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_transfers.create','display_name'=>'Create Clinical > Daily Rx Transfers','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_transfers.update','display_name'=>'Update Clinical > Daily Rx Transfers','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.rx_daily_transfers.delete','display_name'=>'Delete Clinical > Daily Rx Transfers','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Bridged Patients
    ['name'=>'menu_store.clinical.bridged_patients.index','display_name'=>'Clinical > Bridged Patients List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.bridged_patients.create','display_name'=>'Create Clinical > Bridged Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.bridged_patients.update','display_name'=>'Update Clinical > Bridged Patients','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.bridged_patients.delete','display_name'=>'Delete Clinical > Bridged Patients','division_name'=>'menu_store','group_name'=>'clinical'],

    // CLINICAL > Pending Refill Requests
    ['name'=>'menu_store.clinical.pending_refill_requests.index','display_name'=>'Clinical > Pending Refill Requests List','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_refill_requests.create','display_name'=>'Create Clinical > Pending Refill Requests','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_refill_requests.update','display_name'=>'Update Clinical > Pending Refill Requests','division_name'=>'menu_store','group_name'=>'clinical'],
    ['name'=>'menu_store.clinical.pending_refill_requests.delete','display_name'=>'Delete Clinical > Pending Refill Requests','division_name'=>'menu_store','group_name'=>'clinical'],

    // PROCUREMENT
    // PROCUREMENT > Pharmacy > Drug Orders
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.index','display_name'=>'View Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.create','display_name'=>'Create Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.update','display_name'=>'Update Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.delete','display_name'=>'Delete Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.download','display_name'=>'Download Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.pdfview','display_name'=>'PDF view of Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_orders.upload','display_name'=>'Upload File Procurement Pharmacy Drug Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    
    // PROCUREMENT > Pharmacy > Drug Orders (Invoice File)
    ['name'=>'menu_store.procurement.pharmacy.drug_order_invoice.index','display_name'=>'View Procurement Pharmacy Drug Order Invoice','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_order_invoice.create','display_name'=>'Create Procurement Pharmacy Drug Order Invoice','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_order_invoice.update','display_name'=>'Update Procurement Pharmacy Drug Order Invoice','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.drug_order_invoice.delete','display_name'=>'Delete Procurement Pharmacy Drug Order Invoice','division_name'=>'menu_store','group_name'=>'procurement'],

    // PROCUREMENT > Pharmacy > Supplies Orders
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.index','display_name'=>'View Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.create','display_name'=>'Create Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.update','display_name'=>'Update Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.delete','display_name'=>'Delete Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.updateall','display_name'=>'Update Excep Actual Qty Fields Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.updateactualqty','display_name'=>'Update Actual Qty Fields Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.download','display_name'=>'Download Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.pdfview','display_name'=>'PDF view of Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.supplies_orders.upload','display_name'=>'Upload File Procurement Pharmacy Supplies Orders','division_name'=>'menu_store','group_name'=>'procurement'],

    // PROCUREMENT > Pharmacy > Wholesale Drugs Returns
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.index','display_name'=>'View Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.create','display_name'=>'Create Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.update','display_name'=>'Update Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.delete','display_name'=>'Delete Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.download','display_name'=>'Download Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.pdfview','display_name'=>'PDF view of Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.wholesale_drug_returns.upload','display_name'=>'Upload File Procurement Pharmacy Wholesale Drugs Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    
    // PROCUREMENT > Pharmacy > INMAR Returns
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.index','display_name'=>'View Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.create','display_name'=>'Create Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.update','display_name'=>'Update Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.delete','display_name'=>'Delete Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.download','display_name'=>'Download Invoice of Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.pdfview','display_name'=>'PDF View of Invoice of Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.pharmacy.inmar_returns.upload','display_name'=>'Upload File Invoice of Procurement Pharmacy INMAR Returns','division_name'=>'menu_store','group_name'=>'procurement'],
   
    // PROCUREMENT > Clinical Orders
    ['name'=>'menu_store.procurement.clinical_orders.index','display_name'=>'Procurement Clinical Orders List','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.create','display_name'=>'Create Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.update','display_name'=>'Update Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.delete','display_name'=>'Delete Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.download','display_name'=>'Download Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.pdfview','display_name'=>'PDF View of Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.clinical_orders.upload','display_name'=>'Upload File Procurement Clinical Orders','division_name'=>'menu_store','group_name'=>'procurement'],

    // PROCUREMENT > Drug Recall Notifications
    ['name'=>'menu_store.procurement.drug_recall_notifications.index','display_name'=>'View Procurement > Drug Recall Notifications List','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.drug_recall_notifications.create','display_name'=>'Create Procurement > Drug Recall Notifications','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.drug_recall_notifications.update','display_name'=>'Update Procurement > Drug Recall Notifications','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.drug_recall_notifications.delete','display_name'=>'Delete Procurement > Drug Recall Notifications','division_name'=>'menu_store','group_name'=>'procurement'],
    ['name'=>'menu_store.procurement.drug_recall_notifications.view_all','display_name'=>'View Bulletin ALL Procurement > Drug Recall Notifications','division_name'=>'menu_store','group_name'=>'procurement'],

    // COMPLIANCE AND REGULATION
    // COMPLIANCE AND REGULATION > Audit
    ['name'=>'menu_store.cnr.audit.index','display_name'=>'Compliance and Regulation PBM Audit List','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.audit.create','display_name'=>'Create Compliance and Regulation PBM Audit','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.audit.delete','display_name'=>'Delete Compliance and Regulation PBM Audit','division_name'=>'menu_store','group_name'=>'cnr'],
    // COMPLIANCE AND REGULATION > OIG Check
    ['name'=>'menu_store.cnr.oig_check.index','display_name'=>'View Compliance and Regulation OIG Check','division_name'=>'menu_store','group_name'=>'cnr'],
    // COMPLIANCE AND REGULATION > Self-Audit Documents ?

    ['name'=>'menu_store.cnr.self_audit_documents.index','display_name'=>'View Compliance & Regulatory > Self-Audit Documents','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.create','display_name'=>'Create Compliance & Regulatory > Self-Audit Documents','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.delete','display_name'=>'Delete Compliance & Regulatory > Self-Audit Documents','division_name'=>'menu_store','group_name'=>'cnr'],
    
    // Monthly Pharmacy DFI/QA
    ['name'=>'menu_store.cnr.self_audit_documents.m_p_dfiqa.index','display_name'=>'Compliance and Regulation Monthly Pharmacy DFI/QA List','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_p_dfiqa.create','display_name'=>'Create Compliance and Regulation Monthly Pharmacy DFI/QA','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_p_dfiqa.delete','display_name'=>'Delete Compliance and Regulation Monthly Pharmacy DFI/QA','division_name'=>'menu_store','group_name'=>'cnr'],
    // Monthly IHS Audit Checklist
    ['name'=>'menu_store.cnr.self_audit_documents.m_ihs_a_c.index','display_name'=>'Compliance and Regulation Monthly Audit Checklist List','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_ihs_a_c.create','display_name'=>'Create Compliance and Regulation Monthly Audit Checklist','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_ihs_a_c.delete','display_name'=>'Delete Compliance and Regulation Monthly Audit Checklist','division_name'=>'menu_store','group_name'=>'cnr'],
    // Monthly Self Assessment QA
    ['name'=>'menu_store.cnr.self_audit_documents.m_s_a_qa.index','display_name'=>'Compliance and Regulation Monthly Self Assessment QA List','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_s_a_qa.create','display_name'=>'Create Compliance and Regulation Monthly Self Assessment QA','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.self_audit_documents.m_s_a_qa.delete','display_name'=>'Delete Compliance and Regulation Monthly Self Assessment QA','division_name'=>'menu_store','group_name'=>'cnr'],

    // COMPLIANCE AND REGULATION > OIG Documents
    ['name'=>'menu_store.cnr.oig_documents.index','display_name'=>'Compliance and Regulation OIG Documents List','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.oig_documents.create','display_name'=>'Create Compliance and Regulation OIG Documents','division_name'=>'menu_store','group_name'=>'cnr'],
    ['name'=>'menu_store.cnr.oig_documents.delete','display_name'=>'Delete Compliance and Regulation OIG Documents','division_name'=>'menu_store','group_name'=>'cnr'],

    // PATIENT SUPPORT
    // PATIENT SUPPORT > Transfer RX
    ['name'=>'menu_store.patient_support.transfer_rx.index','display_name'=>'View Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],
    ['name'=>'menu_store.patient_support.transfer_rx.create','display_name'=>'Create Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],
    ['name'=>'menu_store.patient_support.transfer_rx.update','display_name'=>'Update Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],
    ['name'=>'menu_store.patient_support.transfer_rx.delete','display_name'=>'Delete Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],
    ['name'=>'menu_store.patient_support.transfer_rx.upload','display_name'=>'Upload Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],
    ['name'=>'menu_store.patient_support.transfer_rx.comment','display_name'=>'Comment Patient Support Transfer RX','division_name'=>'menu_store','group_name'=>'patient_support'],

    // ESCALATION
    // ESCALATION > Tickets
    ['name'=>'menu_store.escalation.tickets.index','display_name'=>'View Escalation Tickets List Assigned/Created','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.tickets.create','display_name'=>'Create Escalation Tickets','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.tickets.update','display_name'=>'Update Escalation Tickets','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.tickets.semi_update','display_name'=>'Semi Update Escalation Tickets','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.tickets.delete','display_name'=>'Delete Escalation Tickets','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.tickets.view_all','display_name'=>'View Bulletin ALL Tickets','division_name'=>'menu_store','group_name'=>'escalation'],

    // ESCALATION > Reviews
    ['name'=>'menu_store.escalation.reviews.index','display_name'=>'View Escalation Reviews','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.reviews.create','display_name'=>'Create Escalation Reviews','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.reviews.update','display_name'=>'Update Escalation Reviews','division_name'=>'menu_store','group_name'=>'escalation'],
    ['name'=>'menu_store.escalation.reviews.delete','display_name'=>'Delete Escalation Reviews','division_name'=>'menu_store','group_name'=>'escalation'],

    // KNOWLEDGE BASE
    // KNOWLEDGE BASE > SOPs
    ['name'=>'menu_store.knowledge_base.sops.index','display_name'=>'View SOPs List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.sops.create','display_name'=>'Create SOP','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.sops.update','display_name'=>'Update SOP','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.sops.delete','display_name'=>'Delete SOP','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    // KNOWLEDGE BASE > P&Ps
    ['name'=>'menu_store.knowledge_base.pnps.index','display_name'=>'View P&Ps List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pnps.create','display_name'=>'Create P&P','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pnps.update','display_name'=>'Update P&P','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pnps.delete','display_name'=>'Delete P&P','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    // KNOWLEDGE BASE > Process Documents
    ['name'=>'menu_store.knowledge_base.pd.index','display_name'=>'View Process Documents List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pd.create','display_name'=>'Create Process Document','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pd.update','display_name'=>'Update Process Document','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pd.delete','display_name'=>'Delete Process Document','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    // KNOWLEDGE BASE > How to Guide
    ['name'=>'menu_store.knowledge_base.htg.index','display_name'=>'View How to Guides List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.htg.create','display_name'=>'Create How to Guide','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.htg.update','display_name'=>'Update How to Guide','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.htg.delete','display_name'=>'Delete How to Guide','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    // KNOWLEDGE BASE > Board of Pharmacy
    ['name'=>'menu_store.knowledge_base.bop.index','display_name'=>'View Board of Pharmacys List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.bop.create','display_name'=>'Create Board of Pharmacy','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.bop.update','display_name'=>'Update Board of Pharmacy','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.bop.delete','display_name'=>'Delete Board of Pharmacy','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    // KNOWLEDGE BASE > Pharmacy Form
    ['name'=>'menu_store.knowledge_base.pf.index','display_name'=>'View Pharmacy Forms List','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pf.create','display_name'=>'Create Pharmacy Form','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pf.update','display_name'=>'Update Pharmacy Form','division_name'=>'menu_store','group_name'=>'knowledge_base'],
    ['name'=>'menu_store.knowledge_base.pf.delete','display_name'=>'Delete Pharmacy Form','division_name'=>'menu_store','group_name'=>'knowledge_base'],

    // EOD Register Report 
    // EOD Register Report > Register
    ['name'=>'menu_store.eod_register_report.register.index','display_name'=>'View EOD Report List','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.register.create','display_name'=>'Create EOD Report','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.register.update','display_name'=>'Update EOD Report','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.register.delete','display_name'=>'Delete EOD Report','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.register.download','display_name'=>'Download EOD Report','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.register.pdfview','display_name'=>'PDF View of EOD Report','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    // EOD Register Report > Deposit
    ['name'=>'menu_store.eod_register_report.deposit.index','display_name'=>'View EOD Deposit List','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.deposit.create','display_name'=>'Create EOD Deposit','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.deposit.update','display_name'=>'Update EOD Deposit','division_name'=>'menu_store','group_name'=>'eod_register_report'],
    ['name'=>'menu_store.eod_register_report.deposit.delete','display_name'=>'Delete EOD Deposit','division_name'=>'menu_store','group_name'=>'eod_register_report'],

    // Inventory Reconciliation
    // Inventory Reconciliation > Control Counts
    ['name'=>'menu_store.compliance.monthly_control_counts.index','display_name'=>'View Compliance & Regulatory > Control Counts','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.compliance.monthly_control_counts.create','display_name'=>'Create Compliance & Regulatory > Control Counts','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.compliance.monthly_control_counts.delete','display_name'=>'Delete Compliance & Regulatory > Control Counts','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],

    // Inventory Reconciliation > Control Counts > C2
    ['name'=>'menu_store.inventory_reconciliation.monthly.c2.index','display_name'=>'View Inventory Reconciliation > Control Counts > C2','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.monthly.c2.create','display_name'=>'Create Inventory Reconciliation > Control Counts > C2','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.monthly.c2.delete','display_name'=>'Delete Inventory Reconciliation > Control Counts > C2','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    // Inventory Reconciliation > Control Counts > C3 - 5
    ['name'=>'menu_store.inventory_reconciliation.monthly.c3_5.index','display_name'=>'View Inventory Reconciliation > Control Counts > C3 - 5','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.monthly.c3_5.create','display_name'=>'Create Inventory Reconciliation > Control Counts > C3 - 5','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.monthly.c3_5.delete','display_name'=>'Delete Inventory Reconciliation > Control Counts > C3 - 5','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    // Inventory Reconciliation > Daily Inventory Evaluation
    ['name'=>'menu_store.inventory_reconciliation.daily.index','display_name'=>'View Inventory Reconciliation > Daily Inventory Evaluation','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.daily.create','display_name'=>'Create Inventory Reconciliation > Daily Inventory Evaluation','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.daily.delete','display_name'=>'Delete Inventory Reconciliation > Daily Inventory Evaluation','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    // Inventory Reconciliation > Inventory Audit (Weekly)
    ['name'=>'menu_store.inventory_reconciliation.weekly.index','display_name'=>'View Inventory Reconciliation > Inventory Audit (Weekly)','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.weekly.create','display_name'=>'Create Inventory Reconciliation > Inventory Audit (Weekly)','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    ['name'=>'menu_store.inventory_reconciliation.weekly.delete','display_name'=>'Delete Inventory Reconciliation > Inventory Audit (Weekly)','division_name'=>'menu_store','group_name'=>'inventory_reconciliation'],
    
    // HUMAN RESOURCE (within store) (renamed to people & culture)
    // HR > CTCLUSI - Three Rivers Pharmacy
    ['name'=>'menu_store.hr.organization.index','display_name'=>'View Store CTCLUSI Three Rivers Pharmacy Page','division_name'=>'menu_store','group_name'=>'hr'],
    // HR > Employees
    ['name'=>'menu_store.hr.employees.index','display_name'=>'View Store Employee List','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.employees.create','display_name'=>'Create Store Employee','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.employees.update','display_name'=>'Update Store Employee','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.employees.delete','display_name'=>'Delete Store Employee','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.employees.import','display_name'=>'Import Store Employee','division_name'=>'menu_store','group_name'=>'hr'],

    // HR > Schedules
    ['name'=>'menu_store.hr.schedules.index','display_name'=>'View Store Employee Schedule List','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.schedules.create','display_name'=>'Create Store Employee Schedule','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.schedules.update','display_name'=>'Update Store Employee Schedule','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.schedules.delete','display_name'=>'Delete Store Employee Schedule','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.schedules.import','display_name'=>'Import Store Employee Schedule','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.schedules.export','display_name'=>'Export Store Employee Schedule','division_name'=>'menu_store','group_name'=>'hr'],

    // HR > Leaves
    ['name'=>'menu_store.hr.leaves.index','display_name'=>'View Store Employee Leave List','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.leaves.create','display_name'=>'Create Store Employee Leave','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.leaves.update','display_name'=>'Update Store Employee Leave','division_name'=>'menu_store','group_name'=>'hr'],
    ['name'=>'menu_store.hr.leaves.delete','display_name'=>'Delete Store Employee Leave','division_name'=>'menu_store','group_name'=>'hr'],

    // Marketing > News
    ['name'=>'menu_store.marketing.news.index','display_name'=>'View News & Events List','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.news.create','display_name'=>'Create News & Events','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.news.update','display_name'=>'Update News & Events','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.news.delete','display_name'=>'Delete News & Events','division_name'=>'menu_store','group_name'=>'marketing'],

    // Marketing > Announcements
    ['name'=>'menu_store.marketing.announcements.index','display_name'=>'View Marketing Announcements','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.announcements.create','display_name'=>'Create Marketing Announcement','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.announcements.update','display_name'=>'Update Marketing Announcement','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.announcements.delete','display_name'=>'Delete Marketing Announcement','division_name'=>'menu_store','group_name'=>'marketing'],

    // Marketing > News
    ['name'=>'menu_store.marketing.events.index','display_name'=>'View Events List','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.events.create','display_name'=>'Create Events','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.events.update','display_name'=>'Update Events','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.events.delete','display_name'=>'Delete Events','division_name'=>'menu_store','group_name'=>'marketing'],

    // Marketing > Decks
    ['name'=>'menu_store.marketing.decks.index','display_name'=>'View Marketing Decks','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.decks.create','display_name'=>'Create Marketing Deck','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.decks.update','display_name'=>'Update Marketing Deck','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.decks.delete','display_name'=>'Delete Marketing Deck','division_name'=>'menu_store','group_name'=>'marketing'],

    // Marketing > References
    ['name'=>'menu_store.marketing.references.index','display_name'=>'View Marketing References','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.references.create','display_name'=>'Create Marketing Reference','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.references.update','display_name'=>'Update Marketing Reference','division_name'=>'menu_store','group_name'=>'marketing'],
    ['name'=>'menu_store.marketing.references.delete','display_name'=>'Delete Marketing Reference','division_name'=>'menu_store','group_name'=>'marketing'],
    

    // ACCOUNTING AND FINANCE
    // ACCOUNTING AND FINANCE > Proforma and Budget
    ['name'=>'accounting_and_finance.proforma_and_budget.index','display_name'=>'View Accounting and Finance > Proforma and Budget','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.proforma_and_budget.create','display_name'=>'Create Accounting and Finance > Proforma and Budget','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.proforma_and_budget.update','display_name'=>'Update Accounting and Finance > Proforma and Budget','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.proforma_and_budget.delete','display_name'=>'Delete Accounting and Finance > Proforma and Budget','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Weekly Financial Snapshots
    ['name'=>'accounting_and_finance.weekly_financial_snapshots.index','display_name'=>'View Accounting and Finance > Weekly Financial Snapshots','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.weekly_financial_snapshots.create','display_name'=>'Create Accounting and Finance > Weekly Financial Snapshots','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.weekly_financial_snapshots.update','display_name'=>'Update Accounting and Finance > Weekly Financial Snapshots','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.weekly_financial_snapshots.delete','display_name'=>'Delete Accounting and Finance > Weekly Financial Snapshots','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Monthly Income Statement
    ['name'=>'accounting_and_finance.monthly_income_statement.index','display_name'=>'View Accounting and Finance > Monthly Income Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.monthly_income_statement.create','display_name'=>'Create Accounting and Finance > Monthly Income Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.monthly_income_statement.update','display_name'=>'Update Accounting and Finance > Monthly Income Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.monthly_income_statement.delete','display_name'=>'Delete Accounting and Finance > Monthly Income Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Payroll Percentage
    ['name'=>'accounting_and_finance.payroll_percentage.index','display_name'=>'View Accounting and Finance > Payroll Percentage','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.payroll_percentage.create','display_name'=>'Create Accounting and Finance > Payroll Percentage','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.payroll_percentage.update','display_name'=>'Update Accounting and Finance > Payroll Percentage','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.payroll_percentage.delete','display_name'=>'Delete Accounting and Finance > Payroll Percentage','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Scalable Analyzer
    ['name'=>'accounting_and_finance.scalable_analyzer.index','display_name'=>'View Accounting and Finance > Scalable Analyzer','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.scalable_analyzer.create','display_name'=>'Create Accounting and Finance > Scalable Analyzer','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.scalable_analyzer.update','display_name'=>'Update Accounting and Finance > Scalable Analyzer','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.scalable_analyzer.delete','display_name'=>'Delete Accounting and Finance > Scalable Analyzer','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Cash Flow Statement
    ['name'=>'accounting_and_finance.cash_flow_statement.index','display_name'=>'View Accounting and Finance > Cash Flow Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.cash_flow_statement.create','display_name'=>'Create Accounting and Finance > Cash Flow Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.cash_flow_statement.update','display_name'=>'Update Accounting and Finance > Cash Flow Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.cash_flow_statement.delete','display_name'=>'Delete Accounting and Finance > Cash Flow Statement','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // ACCOUNTING AND FINANCE > Process Document (Scribe)
    ['name'=>'accounting_and_finance.process_document.index','display_name'=>'View Accounting and Finance > Process Document (Scribe)','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.process_document.create','display_name'=>'Create Accounting and Finance > Process Document (Scribe)','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.process_document.update','display_name'=>'Update Accounting and Finance > Process Document (Scribe)','division_name'=>'general','group_name'=>'accounting_and_finance'],
    ['name'=>'accounting_and_finance.process_document.delete','display_name'=>'Delete Accounting and Finance > Process Document (Scribe)','division_name'=>'general','group_name'=>'accounting_and_finance'],

    // FORMS (jot form)
    // FORMS > New Patients (Patient Intakes)
    ['name'=>'menu_store.jot_form.patient_intakes.index','display_name'=>'View Forms > New Patients List','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_intakes.create','display_name'=>'Create Forms > New Patients','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_intakes.update','display_name'=>'Update Forms > New Patients','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_intakes.delete','display_name'=>'Delete Forms > New Patients','division_name'=>'menu_store','group_name'=>'jot_form'],

    // FORMS > Records Release (Release of Information)
    ['name'=>'menu_store.jot_form.release_of_information.index','display_name'=>'View Forms > Records Release List','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.release_of_information.create','display_name'=>'Create Forms > Records Release','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.release_of_information.update','display_name'=>'Update Forms > Records Release','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.release_of_information.delete','display_name'=>'Delete Forms > Records Release','division_name'=>'menu_store','group_name'=>'jot_form'],

    // FORMS > Patient Prescription Transfer
    ['name'=>'menu_store.jot_form.patient_prescription_transfer.index','display_name'=>'View Forms > Patient Prescription Transfer List','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_prescription_transfer.create','display_name'=>'Create Forms > Patient Prescription Transfer','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_prescription_transfer.update','display_name'=>'Update Forms > Patient Prescription Transfer','division_name'=>'menu_store','group_name'=>'jot_form'],
    ['name'=>'menu_store.jot_form.patient_prescription_transfer.delete','display_name'=>'Delete Forms > Patient Prescription Transfer','division_name'=>'menu_store','group_name'=>'jot_form'],
];
