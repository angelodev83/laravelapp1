<div class="row mt-3">

    @canany(['menu_store.financial_reports.transaction_receipts_nuc01.index', 'menu_store.financial_reports.transaction_receipts_nuc01.create', 'menu_store.financial_reports.transaction_receipts_nuc01.update', 'menu_store.financial_reports.transaction_receipts_nuc01.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-how-to-guide-folder-card" onclick="clickPageFolder(69, 'how-to-guide')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1 folder-widget-text">TRP-NUC01</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['transaction_receipts_nuc01']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

    @canany(['menu_store.financial_reports.transaction_receipts_nuc05.index', 'menu_store.financial_reports.transaction_receipts_nuc05.create', 'menu_store.financial_reports.transaction_receipts_nuc05.update', 'menu_store.financial_reports.transaction_receipts_nuc05.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-board-of-pharmacy-folder-card" onclick="clickPageFolder(70, 'board-of-pharmacy')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1 folder-widget-text">TRP-NUC05</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['transaction_receipts_nuc05']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-success text-success ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

    @canany(['menu_store.financial_reports.transaction_receipts_nuc06.index', 'menu_store.financial_reports.transaction_receipts_nuc06.create', 'menu_store.financial_reports.transaction_receipts_nuc06.update', 'menu_store.financial_reports.transaction_receipts_nuc06.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pharmacy-forms-folder-card" onclick="clickPageFolder(71, 'pharmacy-forms')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1  folder-widget-text">TRP-NUC06</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['transaction_receipts_nuc06']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

</div>