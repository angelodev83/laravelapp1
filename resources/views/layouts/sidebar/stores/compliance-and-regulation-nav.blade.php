
    <li class="sidebar-store-cnr-nav">
        <a class="sidebar-store-cnr-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-scale-balanced fa-2xs' ></i></div>
                <div class="menu-title">Compliance & Regulatory</div>
            @else
                <i class="fa-solid fa-scale-balanced me-2"></i>Compliance & Regulatory
            @endif
        </a>
        <ul>
            @canany(['menu_store.cnr.audit.index', 'menu_store.cnr.audit.create', 'menu_store.cnr.audit.delete'])
                <li>
                    <a href="/store/compliance/{{$menu->id}}/audit"><i class="fa-solid fa-calendar-check ms-2 me-3"></i>PBM Audit</a>
                </li>
            @endcanany

            @canany(['menu_store.cnr.oig_check.index','menu_store.cnr.oig_documents.index','menu_store.cnr.oig_documents.create','menu_store.cnr.oig_documents.delete'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-check-to-slot ms-2 me-3"></i>OIG</a>
                <ul>
                    @canany(['menu_store.cnr.oig_check.index'])
                        <li>
                            <a href="/store/compliance/{{$menu->id}}/oig-check"><i class="bx bx-menu ms-2"></i>OIG Check</a>
                        </li>
                    @endcanany

                    @canany(['menu_store.cnr.oig_documents.index','menu_store.cnr.oig_documents.create','menu_store.cnr.oig_documents.delete'])
                        <li>
                            <a href="/store/compliance/{{$menu->id}}/oig-documents"><i class="bx bx-menu ms-2"></i>OIG Documents</a>
                        </li>
                    @endcanany

                </ul>
            </li>
            @endcanany

            {{-- @canany(['menu_store.cnr.self_audit_documents.m_p_dfiqa.index', 'menu_store.cnr.self_audit_documents.m_p_dfiqa.create', 'menu_store.cnr.self_audit_documents.m_p_dfiqa.delete', 'menu_store.cnr.self_audit_documents.m_ihs_a_c.index', 'menu_store.cnr.self_audit_documents.m_ihs_a_c.create', 'menu_store.cnr.self_audit_documents.m_ihs_a_c.delete', 'menu_store.cnr.self_audit_documents.m_s_a_qa.index', 'menu_store.cnr.self_audit_documents.m_s_a_qa.create', 'menu_store.cnr.self_audit_documents.m_s_a_qa.delete'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-regular fa-folder-open ms-2 me-3"></i>Self-Audit Documents</a>
                <ul>
                    @canany(['menu_store.cnr.self_audit_documents.m_p_dfiqa.index', 'menu_store.cnr.self_audit_documents.m_p_dfiqa.create', 'menu_store.cnr.self_audit_documents.m_p_dfiqa.delete'])
                        <li>
                            <a href="/store/compliance/{{$menu->id}}/self-audit-documents/monthly-pharmacy-dfiqa"><i class="bx bx-menu ms-2"></i>Monthly Pharmacy DFI/QA</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.cnr.self_audit_documents.m_ihs_a_c.index', 'menu_store.cnr.self_audit_documents.m_ihs_a_c.create', 'menu_store.cnr.self_audit_documents.m_ihs_a_c.delete'])
                        <li>
                            <a href="/store/compliance/{{$menu->id}}/self-audit-documents/monthly-ihs-audit-checklist"><i class="bx bx-menu ms-2"></i>Monthly IHS Audit Checklist</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.cnr.self_audit_documents.m_s_a_qa.index', 'menu_store.cnr.self_audit_documents.m_s_a_qa.create', 'menu_store.cnr.self_audit_documents.m_s_a_qa.delete'])
                        <li>
                            <a href="/store/compliance/{{$menu->id}}/self-audit-documents/monthly-self-assessment-qa"><i class="bx bx-menu ms-2"></i>Monthly Self Assessment QA</a>
                        </li>
                    @endcanany
                </ul>
            </li>
            @endcanany --}}

            @canany(['menu_store.cnr.self_audit_documents.index', 'menu_store.cnr.self_audit_documents.create', 'menu_store.cnr.self_audit_documents.delete'])
            <li> 
                <a class="" href="/store/compliance/{{$menu->id}}/self-audit-documents/{{date('Y')}}/{{date('n')}}"><i class="fa-regular fa-folder-open ms-2 me-3"></i>Self-Audit Documents</a>
            </li>
            @endcanany
            
            {{-- @canany(['menu_store.inventory_reconciliation.monthly.c2.index', 'menu_store.inventory_reconciliation.monthly.c2.create', 'menu_store.inventory_reconciliation.monthly.c2.delete'])
            <li> 
                <a class="has-arrow" href="javascript:;"><i class="fa-solid fa-pills ms-2 me-3"></i>Control Counts</a>
                <ul>
                    @canany(['menu_store.inventory_reconciliation.monthly.c2.index', 'menu_store.inventory_reconciliation.monthly.c2.create', 'menu_store.inventory_reconciliation.monthly.c2.delete'])
                        <li>
                            <a href="/store/inventory-reconciliation/{{$menu->id}}/monthly-control-counts/c2"><i class="bx bx-right-arrow-alt ms-2"></i>C2</a>
                        </li>
                    @endcanany
                    @canany(['menu_store.inventory_reconciliation.monthly.c3_5.index', 'menu_store.inventory_reconciliation.monthly.c3_5.create', 'menu_store.inventory_reconciliation.monthly.c3_5.delete'])
                        <li>
                            <a href="/store/inventory-reconciliation/{{$menu->id}}/monthly-control-counts/c3-5"><i class="bx bx-right-arrow-alt ms-2"></i>C3 - 5</a>
                        </li>
                    @endcanany
                </ul>
            </li>
            @endcanany --}}

            @canany(['menu_store.compliance.monthly_control_counts.index', 'menu_store.compliance.monthly_control_counts.create', 'menu_store.compliance.monthly_control_counts.delete'])
            <li> 
                <a class="" href="/store/inventory-reconciliation/{{$menu->id}}/monthly-control-counts/{{date('Y')}}/{{date('n')}}"><i class="fa-solid fa-pills ms-2 me-3"></i>Control Counts</a>
            </li>
            @endcanany
        </ul>
    </li>
