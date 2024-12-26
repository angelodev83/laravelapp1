<div class="row mt-3">

    @canany(['menu_store.financial_reports.eod_reports_nuc01.index', 'menu_store.financial_reports.eod_reports_nuc01.create', 'menu_store.financial_reports.eod_reports_nuc01.update', 'menu_store.financial_reports.eod_reports_nuc01.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-sop-folder-card" onclick="clickPageFolder(65, 'sop')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1  folder-widget-text">TRP-NUC01</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['eod_reports_nuc01']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-custom-sop text-custom-sop ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

    @canany(['menu_store.financial_reports.eod_reports_nuc05.index', 'menu_store.financial_reports.eod_reports_nuc05.create', 'menu_store.financial_reports.eod_reports_nuc05.update', 'menu_store.financial_reports.eod_reports_nuc05.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-pnp-folder-card" onclick="clickPageFolder(66, 'pnp')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1  folder-widget-text">TRP-NUC05</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['eod_reports_nuc05']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

    @canany(['menu_store.financial_reports.eod_reports_nuc06.index', 'menu_store.financial_reports.eod_reports_nuc06.create', 'menu_store.financial_reports.eod_reports_nuc06.update', 'menu_store.financial_reports.eod_reports_nuc06.delete'])
        <div class="col-12 col-md-4">
            <div class="card shadow-none border radius-10 knowledge-base-all-folder-card knowledge-base-process-documents-folder-card" onclick="clickPageFolder(67, 'process-documents')">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="my-0 me-1  folder-widget-text">TRP-NUC06</p>
                            <small class="m-0 p-0 text-secondary">{{ $filesCounting['eod_reports_nuc06']['count'] }} file(s)</small>
                        </div>
                        <div class="widgets-icons bg-light-info text-info ms-auto"><i class="fa-solid fa-cash-register"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany

    

</div>