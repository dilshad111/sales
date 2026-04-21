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
        'inventory' => [
            'label' => 'Inventory Report',
            'route' => 'reports.inventory',
            'icon' => 'fas fa-boxes',
            'color' => 'text-secondary'
        ],
        'purchase_report' => [
            'label' => 'Purchase Report',
            'route' => 'reports.purchase_report',
            'icon' => 'fas fa-file-contract',
            'color' => 'text-info'
        ],
        'agent_commission_report' => [
            'label' => 'Agent Commissions',
            'route' => 'reports.agent_commission_report',
            'icon' => 'fas fa-percentage',
            'color' => 'text-warning'
        ],
        'principal_payout_report' => [
            'label' => 'Partner Payouts',
            'route' => 'reports.director_payout_report',
            'icon' => 'fas fa-wallet',
            'color' => 'text-success'
        ],
        'bills' => [
            'label' => 'Sale Invoices',
            'route' => 'bills.index',
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'text-danger'
        ],
        'direct_sale_invoices' => [
            'label' => 'Direct Sale Invoice',
            'route' => 'bills.create',
            'icon' => 'fas fa-plus-circle',
            'color' => 'text-success'
        ],
        'delivery_challans' => [
            'label' => 'Delivery Challans',
            'route' => 'delivery_challans.index',
            'icon' => 'fas fa-truck',
            'color' => 'text-primary'
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
            'label' => 'Costing Calculator',
            'route' => 'carton_costing.index',
            'icon' => 'fas fa-calculator',
            'color' => 'text-warning'
        ],
        'costing_report' => [
            'label' => 'Costing History',
            'route' => 'carton_costing.report',
            'icon' => 'fas fa-clipboard-list',
            'color' => 'text-info'
        ],
        'outstanding_payments' => [
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
        'customer_statement' => [
            'label' => 'Customer Statement Report',
            'route' => 'reports.customer_statement',
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'text-warning'
        ],
        'supplier_statement' => [
            'label' => 'Supplier Statement Report',
            'route' => 'reports.supplier_statement',
            'icon' => 'fas fa-file-lines',
            'color' => 'text-info'
        ],
        'cash_statement' => [
            'label' => 'Cash Statement Report',
            'route' => 'reports.cash_statement',
            'icon' => 'fas fa-cash-register',
            'color' => 'text-danger'
        ],
        'trial_balance' => [
            'label' => 'Trial Balance',
            'route' => 'reports.trial_balance',
            'icon' => 'fas fa-scale-balanced',
            'color' => 'text-primary'
        ],
        'profit_loss' => [
            'label' => 'Income Statement (P&L)',
            'route' => 'reports.profit_loss',
            'icon' => 'fas fa-chart-bar',
            'color' => 'text-success'
        ],
        'balance_sheet' => [
            'label' => 'Balance Sheet',
            'route' => 'reports.balance_sheet',
            'icon' => 'fas fa-balance-scale',
            'color' => 'text-warning'
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
        'banks' => [
            'label' => 'Banks',
            'route' => 'banks.index',
            'icon' => 'fas fa-university',
            'color' => 'text-primary'
        ],
        'salman_commission' => [
            'label' => 'Salman Commission',
            'route' => 'salman_commissions.index',
            'icon' => 'fas fa-hand-holding-dollar',
            'color' => 'text-success'
        ],
        'payment_parties_list' => [
            'label' => 'Payment Parties Stat',
            'route' => 'reports.payment_parties_list',
            'icon' => 'fas fa-address-book',
            'color' => 'text-info'
        ],
        'cash_ledger' => [
            'label' => 'Cash Ledger',
            'route' => 'reports.cash_ledger',
            'icon' => 'fas fa-money-bill-1',
            'color' => 'text-success'
        ],
        'bank_ledger' => [
            'label' => 'Bank Ledger',
            'route' => 'reports.bank_ledger',
            'icon' => 'fas fa-building-columns',
            'color' => 'text-primary'
        ],
        'writeoff_ledger' => [
            'label' => 'Writeoff Ledger',
            'route' => 'reports.writeoff_ledger',
            'icon' => 'fas fa-file-signature',
            'color' => 'text-danger'
        ],

        'accounts' => [
            'label' => 'Chart of Accounts',
            'route' => 'accounts.index',
            'icon' => 'fas fa-sitemap',
            'color' => 'text-indigo'
        ],
        'ledger' => [
            'label' => 'Party Ledger',
            'route' => 'ledger.index',
            'icon' => 'fas fa-book',
            'color' => 'text-teal'
        ],
        'vouchers' => [
            'label' => 'Voucher List',
            'route' => 'vouchers.index',
            'icon' => 'fas fa-file-invoice',
            'color' => 'text-info'
        ],
        'payment_voucher' => [
            'label' => 'Payment Voucher',
            'route' => 'vouchers.create',
            'params' => ['type' => 'PV'],
            'icon' => 'fas fa-money-check-dollar',
            'color' => 'text-success'
        ],
        'receive_voucher' => [
            'label' => 'Receive Voucher',
            'route' => 'vouchers.create',
            'params' => ['type' => 'RV'],
            'icon' => 'fas fa-receipt',
            'color' => 'text-warning'
        ],
        'journal_voucher' => [
            'label' => 'Journal Voucher',
            'route' => 'vouchers.create',
            'params' => ['type' => 'JV'],
            'icon' => 'fas fa-file-signature',
            'color' => 'text-primary'
        ],
        'suppliers' => [
            'label' => 'Suppliers',
            'route' => 'suppliers.index',
            'icon' => 'fas fa-truck-loading',
            'color' => 'text-info'
        ],
        'agents' => [
            'label' => 'Agents Master',
            'route' => 'agents.index',
            'icon' => 'fas fa-user-tie',
            'color' => 'text-primary'
        ],
        'purchase_items' => [
            'label' => 'Purchase Items',
            'route' => 'purchase_items.index',
            'icon' => 'fas fa-shopping-basket',
            'color' => 'text-success'
        ],
        'purchase_invoices' => [
            'label' => 'Purchase Invoices',
            'route' => 'purchase_invoices.index',
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'text-primary'
        ],
        'purchase_delivery_challans' => [
            'label' => 'Purchase DC',
            'route' => 'purchase_delivery_challans.index',
            'icon' => 'fas fa-truck-ramp-box',
            'color' => 'text-warning'
        ],
        'recoveries' => [
            'label' => 'Recoveries tracking',
            'route' => 'recoveries.index',
            'icon' => 'fas fa-hand-holding-dollar',
            'color' => 'text-warning'
        ],
        'financial_years' => [
            'label' => 'Financial Years',
            'route' => 'financial_years.index',
            'icon' => 'fas fa-calendar-days',
            'color' => 'text-primary'
        ],
    ],

    'form_groups' => [
        'General' => ['dashboard'],
        'Purchases' => ['suppliers', 'agents', 'purchase_items', 'purchase_delivery_challans', 'purchase_invoices', 'recoveries'],
        'Sales' => ['customers', 'items', 'delivery_challans', 'bills', 'direct_sale_invoices', 'salman_commission'],
        'Finance' => ['accounts', 'payments', 'personal_accounts', 'payment_parties', 'receive_voucher', 'payment_voucher', 'journal_voucher', 'vouchers'],
        'Costing' => ['carton_costing', 'costing_report'],
        'Reports' => ['outstanding_payments', 'sales', 'customer_statement', 'supplier_statement', 'cash_statement', 'cash_ledger', 'bank_ledger', 'writeoff_ledger', 'payment_parties_list', 'inventory', 'purchase_report', 'agent_commission_report', 'principal_payout_report', 'trial_balance', 'profit_loss', 'balance_sheet'],


        'Settings' => ['users', 'user_rights', 'settings', 'audit_logs', 'banks', 'financial_years'],
    ],
];
