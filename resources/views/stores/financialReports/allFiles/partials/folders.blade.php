<div class="row mt-3">

    @if(in_array(request()->page_id, $pageIds['all_eod_reports']))
        @include('stores/financialReports/eodReports/folders')
    @endif

    @if(in_array(request()->page_id, $pageIds['all_transaction_receipts']))
        @include('stores/financialReports/transactionReceipts/folders')
    @endif

    @if(in_array(request()->page_id, $pageIds['all']))
        @canany(['menu_store.financial_reports.pharmacy_gross_revenue.index', 'menu_store.financial_reports.pharmacy_gross_revenue.create', 'menu_store.financial_reports.pharmacy_gross_revenue.update', 'menu_store.financial_reports.pharmacy_gross_revenue.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-sop-folder-card" onclick="clickPageFolder(55, 'sop')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Pharmacy Gross Revenue</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['pharmacy_gross_revenue']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-custom-sop text-custom-sop ms-auto"><i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.payments_overview.index', 'menu_store.financial_reports.payments_overview.create', 'menu_store.financial_reports.payments_overview.update', 'menu_store.financial_reports.payments_overview.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pnp-folder-card" onclick="clickPageFolder(56, 'pnp')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Payments Overview</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['payments_overview']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa-solid fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.collected_payments.index', 'menu_store.financial_reports.collected_payments.create', 'menu_store.financial_reports.collected_payments.update', 'menu_store.financial_reports.collected_payments.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-process-documents-folder-card" onclick="clickPageFolder(57, 'process-documents')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Collected Payments</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['collected_payments']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.gross_revenue_and_cogs.index', 'menu_store.financial_reports.gross_revenue_and_cogs.create', 'menu_store.financial_reports.gross_revenue_and_cogs.update', 'menu_store.financial_reports.gross_revenue_and_cogs.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-how-to-guide-folder-card" onclick="clickPageFolder(58, 'how-to-guide')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Gross Revenue and Cogs</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['gross_revenue_and_cogs']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="fa-solid fa-filter-circle-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.account_receivables.index', 'menu_store.financial_reports.account_receivables.create', 'menu_store.financial_reports.account_receivables.update', 'menu_store.financial_reports.account_receivables.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-board-of-pharmacy-folder-card" onclick="clickPageFolder(59, 'board-of-pharmacy')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Account Receivables</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['account_receivables']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-success text-success ms-auto"><i class="fa-solid fa-money-bill-1-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.remittance_advice.index', 'menu_store.financial_reports.remittance_advice.create', 'menu_store.financial_reports.remittance_advice.update', 'menu_store.financial_reports.remittance_advice.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pharmacy-forms-folder-card" onclick="clickPageFolder(72, 'pharmacy-forms')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Remittance Advice</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['remittance_advice']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa-solid fa-money-check-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.scalable_exp_analyzer.index', 'menu_store.financial_reports.scalable_exp_analyzer.create', 'menu_store.financial_reports.scalable_exp_analyzer.update', 'menu_store.financial_reports.scalable_exp_analyzer.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-sea-folder-card" onclick="clickPageFolder(61, 'sea')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Scalable Exp. Analyzer</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['scalable_exp_analyzer']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-custom-sop text-custom-sop ms-auto"><i class="fa-solid fa-scale-unbalanced"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.cash_flow.index', 'menu_store.financial_reports.cash_flow.create', 'menu_store.financial_reports.cash_flow.update', 'menu_store.financial_reports.cash_flow.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-cf-folder-card" onclick="clickPageFolder(62, 'cf')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Cash Flow</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['cash_flow']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.xero_pl.index', 'menu_store.financial_reports.xero_pl.create', 'menu_store.financial_reports.xero_pl.update', 'menu_store.financial_reports.xero_pl.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-xpl-folder-card" onclick="clickPageFolder(63, 'xpl')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Profit and Loss</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['xero_pl']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa-solid fa-calculator"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['menu_store.financial_reports.payroll_percentage.index', 'menu_store.financial_reports.payroll_percentage.create', 'menu_store.financial_reports.payroll_percentage.update', 'menu_store.financial_reports.payroll_percentage.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pp-folder-card" onclick="clickPageFolder(64, 'pp')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Payroll Percentage</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['payroll_percentage']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="fa-solid fa-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany
    @endif

</div>