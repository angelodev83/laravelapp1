{{-- @canany(array_keys($menuGeneralGroupPermissions, 'accounting'), array_keys($menuGeneralGroupPermissions, 'hr')) --}}
    <li class="menu-label">MGMT88</li>
{{-- @endcanany	 --}}

<!-- AI CHATBOX -->
<li>
    <a href="/admin/chatbox">
        <div class="parent-icon"><i class='fa-solid fa-robot fa-sm'></i>
        </div>
        <div class="menu-title">Pilli Boy AI</div>
    </a>
</li>
<!-- -->

    <!-- Accounting -->
    {{-- @canany(array_keys($menuGeneralGroupPermissions, 'accounting'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-money' ></i>
                </div>
                <div class="menu-title">Accounting</div>
            </a>
            <ul>
                @can('accounting.sales_monitoring.index')
                    <li>
                        <a href="/admin/accounting/sales_monitoring"><i class="bx bx-right-arrow-alt"></i>Sales Monitoring</a>
                    </li>
                @endcan
                @can('accounting.payroll_percentage.index')
                    <li>
                        <a href="/admin/accounting/payroll_percentage"><i class="bx bx-right-arrow-alt"></i>Payroll Percentage</a>
                    </li>
                @endcan
                @can('accounting.ar_aging.index')
                    <li>
                        <a href="/admin/accounting/ar_aging"><i class="bx bx-right-arrow-alt"></i>AR Aging</a>
                    </li>
                @endcan
                @can('accounting.profitability.index')
                    <li>
                        <a href="/admin/accounting/profitability"><i class="bx bx-right-arrow-alt"></i>Profitability</a>
                    </li>
                @endcan
                @can('accounting.partnership_reconcillation.index')
                    <li>
                        <a href="/admin/accounting/partnership_reconcillation"><i class="bx bx-right-arrow-alt"></i>Partnership Reconcillation</a>
                    </li>
                @endcan
                @can('accounting.accounts_payable.index')
                    <li>
                        <a href="/admin/accounting/accounts_payable"><i class="bx bx-right-arrow-alt"></i>Accounts Payable</a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany --}}
    <!-- -->

        
    <!-- Human Resource -->
    @canany(array_keys($menuGeneralGroupPermissions, 'hr'))
        <li class="sidebar-store-hr-nav">
            <a href="javascript:;" class="sidebar-store-hr-nav-a has-arrow">
                <div class="parent-icon"><i class='bx bx-user-circle' ></i>
                </div>
                <div class="menu-title">Human Resource</div>
            </a>
            <ul>
                @canany(['hr.hub.index', 'hr.hub.create', 'hr.hub.update', 'hr.hub.delete'])
                    <li>
                        <a href="/admin/human_resources/hub"><i class="fa fa-users fa-sm me-3"></i>HR Hub</a>
                    </li>
                @endcanany
                @canany(['hr.employees.index', 'hr.employees.create', 'hr.employees.update', 'hr.employees.delete'])
                    <li>
                        <a href="/admin/human_resources/employees_relations"><i class="fa fa-user me-3"></i>Employees</a>
                    </li>
                @endcanany
                {{-- @canany(['hr.recruitment_and_hiring.index', 'hr.recruitment_and_hiring.create', 'hr.recruitment_and_hiring.update', 'hr.recruitment_and_hiring.delete'])
                    <li>
                        <a href="/admin/human_resources/recruitment_and_hiring"><i class="bx bx-right-arrow-alt"></i>Recruitment & Hiring</a>
                    </li>
                @endcanany --}}	
                @canany(['hr.employee_reviews.index', 'hr.employee_reviews.create', 'hr.employee_reviews.update', 'hr.employee_reviews.delete'])
                    <li>
                        <a href="/admin/human_resources/employee-reviews/{{date('Y')}}/{{date('n')}}"><i class="fa fa-paperclip fa-sm me-3"></i>Employee Reviews</a>
                    </li>
                @endcanany
                @canany(['hr.file_manager.index', 'hr.file_manager.create', 'hr.file_manager.update', 'hr.file_manager.delete'])
                    <li>
                        <a href="/admin/human_resources/file-manager"><i class="fa fa-folder-closed fa-sm me-3"></i>File Manager</a>
                    </li>
                @endcanany
            </ul>
        </li>
    @endcanany
    <!-- -->

    <!-- Marketing -->
    {{-- @canany(array_keys($menuGeneralGroupPermissions, 'marketing'))
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='fa-regular fa-heart fa-sm'></i>
                </div>
                <div class="menu-title">Marketing</div>
            </a>
            <ul>
                @canany(['hr.announcements.index', 'hr.announcements.create', 'hr.announcements.update', 'hr.announcements.delete'])
                    <li>
                        <a href="/admin/human_resources/announcements"><i class="bx bx-right-arrow-alt"></i>Announcements</a>
                    </li>
                @endcanany			
            </ul>
        </li>
    @endcanany --}}
    <!-- -->
    
    <!-- Compliance & Regulatory -->
    @canany(array_keys($menuGeneralGroupPermissions, 'cnr'))
        <!-- Compliance & Regulatory -->
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-check-shield' ></i>
                </div>
                <div class="menu-title">Compliance & Regulatory</div>
            </a>
            <ul>
                @canany(['cnr.oig_check.index'])
                    <li> <a href="/admin/oig_check"><i class="bx bx-right-arrow-alt"></i>OIG Check</a> </li>
                @endcanany
                @canany(['cnr.oig_list.index'])
                    <li> <a href="/admin/oig_list"><i class="bx bx-right-arrow-alt"></i>OIG List</a> </li>
                @endcanany
                {{-- @canany(['cnr.licensure.index'])
                    <li> <a href="/admin/compliance_and_regulatory/licensure"><i class="bx bx-right-arrow-alt"></i>Licensure</a></li>
                @endcanany
                @canany(['cnr.audits.index'])
                    <li> <a href="/admin/compliance_and_regulatory/audits"><i class="bx bx-right-arrow-alt"></i>Audits</a></li>
                @endcanany
                @canany(['cnr.provider_manuals.index'])
                    <li> <a href="/admin/compliance_and_regulatory/provider_manuals"><i class="bx bx-right-arrow-alt"></i>Provider Manuals</a></li>
                @endcanany
                @canany(['cnr.bop.index'])
                    <li> <a href="/admin/compliance_and_regulatory/bop"><i class="bx bx-right-arrow-alt"></i>BOP</a></li>   
                @endcanany    --}}
            </ul>
        </li>
        <!-- -->
    @endcanany	
    <!-- -->

    <!-- accounting_and_finance -->
    @canany(array_keys($menuGeneralGroupPermissions, 'accounting_and_finance'))
    <li>
        <a href="javascript:;" class="has-arrow">
            <div class="parent-icon"><i class="fa-solid fa-calculator fa-sm me-1 ms-1"></i>
            </div>
            <div class="menu-title">Accounting and Finance</div>
        </a>
        <ul>
            @canany(['accounting_and_finance.proforma_and_budget.index', 'accounting_and_finance.proforma_and_budget.create', 'accounting_and_finance.proforma_and_budget.update', 'accounting_and_finance.proforma_and_budget.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/74"><i class="fa-solid fa-sack-dollar ms-2 me-3"></i>Proforma and Budget</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.weekly_financial_snapshots.index', 'accounting_and_finance.weekly_financial_snapshots.create', 'accounting_and_finance.weekly_financial_snapshots.update', 'accounting_and_finance.weekly_financial_snapshots.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/75"><i class="fa-solid fa-calendar-week ms-2 me-3"></i>Weekly Financial Snapshots</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.monthly_income_statement.index', 'accounting_and_finance.monthly_income_statement.create', 'accounting_and_finance.monthly_income_statement.update', 'accounting_and_finance.monthly_income_statement.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/76"><i class="fa-solid fa-calendar-check ms-2 me-3"></i>Monthly Income Statement</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.payroll_percentage.index', 'accounting_and_finance.payroll_percentage.create', 'accounting_and_finance.payroll_percentage.update', 'accounting_and_finance.payroll_percentage.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/77"><i class="fa-solid fa-percent ms-2 me-3"></i>Payroll Percentage</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.scalable_analyzer.index', 'accounting_and_finance.scalable_analyzer.create', 'accounting_and_finance.scalable_analyzer.update', 'accounting_and_finance.scalable_analyzer.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/82"><i class="fa-solid fa-scale-unbalanced ms-2 me-3"></i>Scalable Analyzer</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.cash_flow_statement.index', 'accounting_and_finance.cash_flow_statement.create', 'accounting_and_finance.cash_flow_statement.update', 'accounting_and_finance.cash_flow_statement.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/83"><i class="fa-solid fa-money-bill-transfer ms-2 me-3"></i>Cash Flow Statement</a>
                </li>
            @endcanany
            @canany(['accounting_and_finance.process_document.index', 'accounting_and_finance.process_document.create', 'accounting_and_finance.process_document.update', 'accounting_and_finance.process_document.delete'])
                <li>
                    <a href="/admin/accounting-and-finance/documents/84"><i class="fa-solid fa-file-signature ms-2 me-3"></i>Process Document (Scribe)</a>
                </li>
            @endcanany
        </ul>
    </li>
    @endcanany	
    <!-- -->