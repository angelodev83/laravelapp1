<div class="container">
    <div class="mt-4 row">
        {{-- <div class="col">
            <div class="text-center rounded-5 bg-soft-orange">
                <div>
                    <img width="230" src="/source-images/knowledge-base/All Files.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">All</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['sop']['count'] }} file(s)</small>
                </div>
            </div>
        </div> --}}
        <a href="/store/knowledge-base/{{request()->id}}/sops" class="col">
            <div class="text-center rounded-5 bg-soft-magenta" onclick="clickPageFolder(35, 'sop')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/SOP.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">SOP</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['sop']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
        <a href="/store/knowledge-base/{{request()->id}}/pnps" class="col">
            <div class="text-center rounded-5 bg-soft-yellow-green" onclick="clickPageFolder(36, 'pnp')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/P&P.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">P&P</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pnp']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
        <a href="/store/knowledge-base/{{request()->id}}/process-documents" class="col">
            <div class="text-center rounded-5 bg-sky-blue" onclick="clickPageFolder(37, 'proccess-documents')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/Process Documents (Scribe).png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Process Documents (Scribe)</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pd']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
    </div>
    <div class="mt-4 row">
        <a href="/store/knowledge-base/{{request()->id}}/how-to-guide" class="col">
            <div class="text-center rounded-5 bg-pale-cyan" onclick="clickPageFolder(38, 'how-to-guide')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/How To Guide.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">How To Guide</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['htg']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
        <a href="/store/knowledge-base/{{request()->id}}/board-of-pharmacy" class="col">
            <div class="text-center rounded-5 bg-banana-mania" onclick="clickPageFolder(39, 'board-of-pharmacy')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/Board of Pharmacy.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Board Of Pharmacy</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['bop']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
        <a href="/store/knowledge-base/{{request()->id}}/pharmacy-forms" class="col">
            <div class="text-center rounded-5 bg-soft-pink" onclick="clickPageFolder(40, 'pharmacy-forms')">
                <div>
                    <img width="230" src="/source-images/knowledge-base/Pharmacy Forms.png" alt="">
                </div>
                <div class="py-3 bg-white rounded-bottom-5">
                    <p class="my-0 me-1 folder-widget-text text-body-secondary">Pharmacy Forms</p>
                    <small class="p-0 m-0 text-secondary">{{ $filesCounting['pf']['count'] }} file(s)</small>
                </div>
            </div>
        </a>
    </div>
</div>