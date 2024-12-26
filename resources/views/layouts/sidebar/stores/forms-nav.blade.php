
    <li class="sidebar-store-clinical-nav">
        <a class="sidebar-store-clinical-nav-a has-arrow" href="javascript:;">
            @if($numberOfStorePermissions == 1)
                <div class="parent-icon"><i class='fa-solid fa-file-contract fa-xs' ></i></div>
                <div class="menu-title">Forms</div>
            @else
                <i class="fa-solid fa-file-contract me-3"></i>Forms
            @endif
        </a>

        <ul>
            @canany(['menu_store.jot_form.patient_intakes.index', 'menu_store.jot_form.patient_intakes.create', 'menu_store.jot_form.patient_intakes.update', 'menu_store.jot_form.patient_intakes.delete'])
                <li>
                    <a href="/store/jot-form/{{$menu->id}}/patient-intakes"><i class="fa-solid fa-hospital-user ms-2 me-3"></i>New Patients</a>
                </li>
            @endcanany
            {{-- @canany(['menu_store.jot_form.release_of_information.index', 'menu_store.jot_form.release_of_information.create', 'menu_store.jot_form.release_of_information.update', 'menu_store.jot_form.release_of_information.delete'])
                <li>
                    <a href="/store/jot-form/{{$menu->id}}/release-of-information"><i class="fa-solid fa-clipboard ms-2 me-4"></i>Records Release</a>
                </li>
            @endcanany --}}
            @canany(['menu_store.jot_form.patient_prescription_transfer.index', 'menu_store.jot_form.patient_prescription_transfer.create', 'menu_store.jot_form.patient_prescription_transfer.update', 'menu_store.jot_form.patient_prescription_transfer.delete'])
                <li>
                    <a href="/store/jot-form/{{$menu->id}}/patient-prescription-transfers"><i class="fa-solid fa-file-prescription ms-2 me-3"></i>Patient Prescription Transfer</a>
                </li>
            @endcanany
        </ul>
    </li>
