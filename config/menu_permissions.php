<?php

return [
    'menus' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-gauge',
            'color' => 'text-primary'
        ],
        'customers' => [
            'label' => 'Customers',
            'route' => 'customers.index',
            'icon' => 'fas fa-users',
            'color' => 'text-info'
        ],
        'items' => [
            'label' => 'Items',
            'route' => 'items.index',
            'icon' => 'fas fa-box',
            'color' => 'text-success'
        ],
        'bills' => [
            'label' => 'Bills',
            'route' => 'bills.index',
            'icon' => 'fas fa-file-invoice',
            'color' => 'text-danger'
        ],
        'payments' => [
            'label' => 'Payments',
            'route' => 'payments.index',
            'icon' => 'fas fa-credit-card',
            'color' => 'text-warning'
        ],
        'personal_accounts' => [
            'label' => 'Personal Account',
            'route' => 'personal_accounts.index',
            'icon' => 'fas fa-wallet',
            'color' => 'text-primary'
        ],
        'carton_costing' => [
            'label' => 'Carton Costing',
            'route' => 'carton_costing.index',
            'icon' => 'fas fa-cube',
            'color' => 'text-secondary'
        ],
        'outstanding_payments_report' => [
            'label' => 'Outstanding Payments Report',
            'route' => 'reports.outstanding_payments',
            'icon' => 'fas fa-clock',
            'color' => 'text-danger'
        ],
        'sales_report' => [
            'label' => 'Sales Report',
            'route' => 'reports.sales',
            'icon' => 'fas fa-chart-line',
            'color' => 'text-success'
        ],
        'payment_parties' => [
            'label' => 'Payment Parties',
            'route' => 'payment_parties.index',
            'icon' => 'fas fa-hand-holding-usd',
            'color' => 'text-info'
        ],
        'customer_statement_report' => [
            'label' => 'Customer Statement Report',
            'route' => 'reports.customer_statement',
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'text-warning'
        ],
        'cash_statement_report' => [
            'label' => 'Cash Statement Report',
            'route' => 'reports.cash_statement',
            'icon' => 'fas fa-cash-register',
            'color' => 'text-danger'
        ],
        'users' => [
            'label' => 'User Management',
            'route' => 'users.index',
            'icon' => 'fas fa-user-gear',
            'color' => 'text-primary'
        ],
        'user_rights' => [
            'label' => 'User Rights Assignment',
            'route' => 'users.rights',
            'icon' => 'fas fa-user-shield',
            'color' => 'text-secondary'
        ],
        'settings' => [
            'label' => 'Company Setup',
            'route' => 'settings.company.edit',
            'icon' => 'fas fa-gear',
            'color' => 'text-dark'
        ],
        'audit_logs' => [
            'label' => 'Audit Logs',
            'route' => 'settings.audits.index',
            'icon' => 'fas fa-history',
            'color' => 'text-muted'
        ],
        'salman_commission' => [
            'label' => 'Salman Commission',
            'route' => 'salman_commissions.index',
            'icon' => 'fas fa-hand-holding-dollar',
            'color' => 'text-success'
        ],
    ],

    'form_groups' => [
        'General' => ['dashboard'],
        'Finance' => ['customers', 'items', 'bills', 'payments', 'personal_accounts', 'carton_costing', 'payment_parties', 'salman_commission'],
        'Reports' => ['outstanding_payments_report', 'sales_report', 'customer_statement_report', 'cash_statement_report'],
        'Settings' => ['users', 'user_rights', 'settings', 'audit_logs'],
    ],
];
