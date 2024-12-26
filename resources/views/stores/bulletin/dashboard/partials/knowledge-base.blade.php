<div class="col-lg-4 d-flex">
    <div class="bg-white shadow-sm radius-10 w-100" style="max-height: 34rem;">
        <div class="card-body">
            <div class="d-flex align-items-center p-3">
                <h6 class="mb-1" style="color: #b432af;">
                    <i class="fa-solid fa-book-open"></i>
                    Knowledge Base
                </h6>
            </div>

            <div id="knowledge-base" class="mt-3 container-lists px-2">
                <div class="position-relative mx-3" style="height: 450px;">
                    <div class="row g-0 d-flex justify-content-between flex-wrap gap-4 mb-4">
                        <a href="/store/knowledge-base/{{request()->id}}/sops" class="col">
                            <div class="text-center rounded-5 bg-soft-magenta">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/SOP.png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">SOP</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['sop']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                        <a href="/store/knowledge-base/{{request()->id}}/pnps" class="col flex-1">
                            <div class="text-center rounded-5 bg-soft-yellow-green">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/P&P.png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">P&P</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pnp']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="row g-0 d-flex justify-content-between flex-wrap gap-4 mb-4">
                        <a href="/store/knowledge-base/{{request()->id}}/how-to-guide" class="col mb-2 flex-1">
                            <div class="text-center rounded-5 bg-pale-cyan" onclick="clickPageFolder(38, 'how-to-guide')">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/How To Guide.png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">How To Guide</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['htg']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                        <a href="/store/knowledge-base/{{request()->id}}/process-documents" class="col mb-2 flex-1">
                            <div class="text-center rounded-5 bg-sky-blue" onclick="clickPageFolder(37, 'proccess-documents')">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/Process Documents (Scribe).png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Process Documents</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pd']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="row g-0 d-flex justify-content-between flex-wrap gap-4">
                        <a href="/store/knowledge-base/{{request()->id}}/board-of-pharmacy" class="col mb-2 flex-1">
                            <div class="text-center rounded-5 bg-banana-mania" onclick="clickPageFolder(39, 'board-of-pharmacy')">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/Board of Pharmacy.png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Board Of Pharmacy</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['bop']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                        <a href="/store/knowledge-base/{{request()->id}}/pharmacy-forms" class="col mb-2 flex-1">
                            <div class="text-center rounded-5 bg-soft-pink" onclick="clickPageFolder(40, 'pharmacy-forms')">
                                <div>
                                    <img width="180" src="/source-images/knowledge-base/Pharmacy Forms.png" alt="">
                                </div>
                                <div class="py-3 bg-white rounded-bottom-5">
                                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Pharmacy Forms</p>
                                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pf']['count'] }} file(s)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>