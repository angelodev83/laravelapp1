<div class="row mt-3">

    @if(in_array(request()->page_id, $pageIds['all']))
        @canany(['accounting_and_finance.proforma_and_budget.index', 'accounting_and_finance.proforma_and_budget.create', 'accounting_and_finance.proforma_and_budget.update', 'accounting_and_finance.proforma_and_budget.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-sop-folder-card" onclick="clickPageFolder(74, 'sop')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Proforma and Budget</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['proforma_and_budget']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-custom-sop text-custom-sop ms-auto"><i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.weekly_financial_snapshots.index', 'accounting_and_finance.weekly_financial_snapshots.create', 'accounting_and_finance.weekly_financial_snapshots.update', 'accounting_and_finance.weekly_financial_snapshots.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pnp-folder-card" onclick="clickPageFolder(75, 'pnp')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Weekly Financial Snapshots</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['weekly_financial_snapshots']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa-solid fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.monthly_income_statement.index', 'accounting_and_finance.monthly_income_statement.create', 'accounting_and_finance.monthly_income_statement.update', 'accounting_and_finance.monthly_income_statement.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-process-documents-folder-card" onclick="clickPageFolder(76, 'process-documents')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1  folder-widget-text">Monthly Income Statement</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['monthly_income_statement']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.payroll_percentage.index', 'accounting_and_finance.payroll_percentage.create', 'accounting_and_finance.payroll_percentage.update', 'accounting_and_finance.payroll_percentage.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-how-to-guide-folder-card" onclick="clickPageFolder(77, 'how-to-guide')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Payroll Percentage</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['payroll_percentage']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="fa-solid fa-filter-circle-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.scalable_analyzer.index', 'accounting_and_finance.scalable_analyzer.create', 'accounting_and_finance.scalable_analyzer.update', 'accounting_and_finance.scalable_analyzer.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-board-of-pharmacy-folder-card" onclick="clickPageFolder(82, 'board-of-pharmacy')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Scalable Analyzer</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['scalable_analyzer']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-success text-success ms-auto"><i class="fa-solid fa-scale-unbalanced"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.cash_flow_statement.index', 'accounting_and_finance.cash_flow_statement.create', 'accounting_and_finance.cash_flow_statement.update', 'accounting_and_finance.cash_flow_statement.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pharmacy-forms-folder-card" onclick="clickPageFolder(83, 'pharmacy-forms')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Cash Flow Statement</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['cash_flow_statement']['count'] }} file(s)</small>
                            </div>
                            <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

        @canany(['accounting_and_finance.process_document.index', 'accounting_and_finance.process_document.create', 'accounting_and_finance.process_document.update', 'accounting_and_finance.process_document.delete'])
            <div class="col-12 col-md-4">
                <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pharmacy-forms-folder-card" onclick="clickPageFolder(84, 'pharmacy-forms')">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="my-0 me-1 folder-widget-text">Process Documents (Scribe)</p>
                                <small class="m-0 p-0 text-secondary">{{ $filesCounting['process_document']['count'] }} file(sss)</small>
                            </div>
                            <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany

    @endif

</div>