@extends('layouts.school')

@section('title', 'Student Admission')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        /* Admission Form Design */
        .form-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            padding: 7px 14px;
            font-size: 14px;
            border-radius: 0;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            display: block;
            color: #1f2937;
            background: #ffffff;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        /* Enhanced focus state */
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        /* Hover state */
        .form-input:hover {
            border-color: #cbd5e1;
        }

        /* Readonly state */
        .form-input[readonly] {
            background: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }

        /* Placeholder styling */
        .form-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        /* Subtle Date Placeholder Appearance */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.4;
        }

        .error-text {
            font-size: 11px;
            color: #dc2626;
            margin-bottom: 5px;
            display: none;
        }

        /* Updated Step Indicators for Full Width */
        .step-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            /* Take full width of parent */
            margin: 0 auto 2rem auto;
            position: relative;
        }

        .step-item {
            z-index: 2;
            background: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Remove flexd: 1 to prevent items from forcing equal width blocks
                               that push the first/last circles inward */
        }

        /* Ensure the first circle touches the left edge */
        .step-item:first-child {
            align-items: flex-start;
        }

        /* Ensure the last circle touches the right edge */
        .step-item:last-child {
            align-items: flex-end;
        }

        /* Align labels based on their position to keep circles at edges */
        .step-item:first-child .step-label {
            text-align: left;
            margin-left: 2px;
        }

        .step-item:last-child .step-label {
            text-align: right;
            margin-right: 2px;
        }

        .step-separator {
            color: #d1d5db;
            font-size: 10px;
            /* Used flex-grow to fill the space between step-items */
            flex-grow: 1;
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
            /* Adjust based on circle height to center vertically */
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
            font-size: 12px;
            transition: 0.3s;
            background: white;
            /* Ensures separator line doesn't show behind circle */
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 600;
            font-size: 12px;
            margin: 0 auto;
            transition: 0.3s;
        }

        .step-active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .step-complete {
            background: #16a34a;
            color: #fff;
            border-color: #16a34a;
        }

        .step-label {
            font-size: 10px;
            margin-top: 4px;
            color: #6b7280;
        }

        /* Custom Select & Subtle Scrollbar */
        .custom-select-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 12px;
        }

        .custom-select-display {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            height: 36px;
            padding: 0 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-size: 14px;
            color: #1f2937;
            border-radius: 0;
            transition: all 0.3s ease;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        .custom-select-display:hover {
            border-color: #cbd5e1;
        }

        .custom-select-wrapper.active .custom-select-display {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .custom-select-options {
            position: absolute;
            top: 44px;
            left: 0;
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #e5e7eb;
            border-radius: 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 999;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .custom-select-options::-webkit-scrollbar {
            width: 6px;
        }

        .custom-select-options::-webkit-scrollbar-track {
            background: #f9fafb;
            border-radius: 3px;
        }

        .custom-select-options::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .custom-select-options::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .custom-option {
            padding: 10px 14px;
            font-size: 14px;
            cursor: pointer;
            color: #1f2937;
            transition: all 0.2s ease;
            font-weight: 400;
        }

        .custom-option:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .hidden-select {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            height: 0;
            width: 0;
        }

        /* Compact Buttons */
        .btn-compact {
            height: 42px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
        }

        .btn-compact::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-compact:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-compact:hover::before {
            opacity: 1;
        }

        .btn-compact:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Specific button hover effects */
        .btn-compact.bg-blue-600:hover {
            background: #2563eb;
        }

        .btn-compact.bg-green-600:hover {
            background: #16a34a;
        }

        .btn-compact.border:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-group-right {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 1.5rem;
        }

        /* Capitalize first letter logic */
        .capitalize-text {
            text-transform: capitalize;
        }

        /* Responsive Styles */
        @media (max-width: 640px) {
            .btn-group-right {
                justify-content: space-between;
                gap: 8px;
            }

            .btn-group-right .btn-compact {
                flex: 1;
                justify-content: center;
            }

            .step-circle {
                width: 24px;
                height: 24px;
                font-size: 11px;
            }

            .step-label {
                font-size: 9px;
            }

            .step-separator {
                font-size: 8px;
                margin-bottom: 16px;
            }

            .form-input {
                font-size: 16px; /* Prevents iOS zoom on focus */
                padding: 7px 12px;
            }

            .custom-select-display {
                font-size: 16px; /* Prevents iOS zoom on focus */
                height: 36px;
            }

            h2 {
                font-size: 1.125rem;
            }

            h4 {
                font-size: 0.875rem;
            }
        }
    </style>

    <div class="max-w-2xl mx-auto bg-white shadow-sm border border-gray-100 p-4 sm:p-6 mt-4 mb-10">

        <h2 class="text-lg font-bold text-center text-gray-800">Student Admission</h2>
        <p class="text-center text-xs text-gray-400 mb-6">{{ Auth::user()->school_name }}</p>

        <div class="step-container">
            <div class="step-item">
                <div id="step1mark" class="step-circle step-active">1</div>
                <div class="step-label">Basic</div>
            </div>
            <div class="step-separator"><i class="fa-solid fa-chevron-right"></i></div>
            <div class="step-item">
                <div id="step2mark" class="step-circle">2</div>
                <div class="step-label">School</div>
            </div>
            <div class="step-separator"><i class="fa-solid fa-chevron-right"></i></div>
            <div class="step-item">
                <div id="step3mark" class="step-circle">3</div>
                <div class="step-label">Student</div>
            </div>
        </div>

        <div id="step1">
            <h4 class="text-sm font-normal mb-4 text-gray-500 capitalize-text border-b pb-1">Admission Details</h4>

            <input id="a_school" value="{{ Auth::user()->school_name }}" class="form-input bg-gray-50 capitalize-text"
                readonly>
            <input type="hidden" id="school_id" value="{{ Auth::user()->school_id }}">

            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)">
                    <span class="selected-text capitalize-text">Select Class</span>
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="a_class" class="hidden-select required-field" onchange="loadGroups()">
                    <option value="">Select Class</option>
                </select>
            </div>

            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)">
                    <span class="selected-text capitalize-text">Select Group</span>
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="a_group" class="hidden-select" onchange="loadSections()">
                    <option value="">Select Group</option>
                </select>
            </div>

            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)">
                    <span class="selected-text capitalize-text">Select Section</span>
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="a_section" class="hidden-select required-field" onchange="loadSessions()">
                    <option value="">Select Section</option>
                </select>
            </div>

            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)">
                    <span class="selected-text capitalize-text">Select Session</span>
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="a_session" class="hidden-select required-field" onchange="loadFees()">
                    <option value="">Select Session</option>
                </select>
            </div>

            <label class="text-[11px] font-medium text-gray-400 capitalize-text">Admission Fee</label>
            <input type="text" id="a_fee" class="form-input bg-gray-50 required-field" placeholder="Select Session" readonly>

            <label class="text-[11px] font-medium text-gray-400 capitalize-text">Admission Date</label>
            <input type="date" id="a_date" class="form-input required-field">

            <div class="btn-group-right">
                <button onclick="window.history.back()" class="btn-compact border border-gray-300 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </button>
                <button onclick="nextStep(1)" class="btn-compact bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto">
                    Next <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <div id="step2" class="hidden">
            <h4 class="text-sm font-normal mb-4 text-gray-500 capitalize-text border-b pb-1">Previous School</h4>
            <input id="p_school" placeholder="School Name" class="form-input capitalize-text">
            <input id="p_class" placeholder="Class" class="form-input capitalize-text">
            <input id="p_group" placeholder="Group" class="form-input capitalize-text">
            <input id="p_section" placeholder="Section" class="form-input capitalize-text">
            <input id="p_session" placeholder="Session" class="form-input capitalize-text">
            <input id="last_exam_result" placeholder="Last Result" class="form-input capitalize-text">

            <div class="btn-group-right">
                <button onclick="showStep(1)"
                    class="btn-compact border border-gray-300 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </button>
                <button onclick="nextStep(2)" class="btn-compact bg-blue-600 text-white hover:bg-blue-700">Next <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>

        <div id="step3" class="hidden">
            <h4 class="text-sm font-normal mb-4 text-gray-500 capitalize-text border-b pb-1">Student Personal</h4>
            <input id="student_name" placeholder="Student Full Name" class="form-input required-field capitalize-text">
            <input id="father_name" placeholder="Father's Name" class="form-input required-field capitalize-text">
            <input id="mother_name" placeholder="Mother's Name" class="form-input required-field capitalize-text">
            <input id="student_mobile" placeholder="Student Mobile" class="form-input required-field">

            <h4 class="text-[12px] font-bold mt-4 text-gray-300 capitalize-text">Current Address</h4>
            <div class="custom-select-wrapper mt-1">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">Division</span><i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="current_division" class="hidden-select required-field">
                    <option value="">Division</option>
                </select>
            </div>
            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">District</span><i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="current_district" class="hidden-select required-field">
                    <option value="">District</option>
                </select>
            </div>
            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">Upazila</span><i class="fa-solid fa-chevron-down"></i></div>
                <div class="custom-select-options"></div>
                <select id="current_upazila" class="hidden-select required-field">
                    <option value="">Upazila</option>
                </select>
            </div>
            <input id="current_village" placeholder="Village" class="form-input required-field capitalize-text">

            <div class="flex items-center gap-2 mt-4 mb-1">
                <input type="checkbox" id="sameAsCurrentAddress" onchange="toggleSameAddress()"
                    class="w-4 h-4 accent-blue-600 cursor-pointer">
                <label for="sameAsCurrentAddress" class="text-xs text-gray-500 cursor-pointer select-none">Same as Current Address</label>
            </div>
            <h4 class="text-[12px] font-bold mt-1 text-gray-300 capitalize-text">Permanent Address</h4>
            <div class="custom-select-wrapper mt-1">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">Division</span><i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="permanent_division" class="hidden-select required-field">
                    <option value="">Division</option>
                </select>
            </div>
            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">District</span><i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-select-options"></div>
                <select id="permanent_district" class="hidden-select required-field">
                    <option value="">District</option>
                </select>
            </div>
            <div class="custom-select-wrapper">
                <div class="custom-select-display" onclick="toggleDropdown(this)"><span
                        class="selected-text capitalize-text">Upazila</span><i class="fa-solid fa-chevron-down"></i></div>
                <div class="custom-select-options"></div>
                <select id="permanent_upazila" class="hidden-select required-field">
                    <option value="">Upazila</option>
                </select>
            </div>
            <input id="permanent_village" placeholder="Village" class="form-input required-field capitalize-text">

            <div class="relative">
                <input id="studentPass" type="password" placeholder="Password" class="form-input required-field pr-10">
                <button type="button" onclick="togglePassword('studentPass', this)"
                    class="absolute right-3 top-2.5 text-gray-400"><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="relative">
                <input id="studentPass2" type="password" placeholder="Confirm Password"
                    class="form-input required-field pr-10">
                <button type="button" onclick="togglePassword('studentPass2', this)"
                    class="absolute right-3 top-2.5 text-gray-400"><i class="fa-regular fa-eye"></i></button>
            </div>

            <label class="text-[11px] text-gray-400 capitalize-text block mb-1">Student Photo</label>
            <input type="file" id="student_image"
                class="form-input file:mr-4 file:py-1 file:px-4 file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700"
                onchange="previewImage(event)">
            <img id="studentPreview" class="w-20 h-20 object-cover border mt-2 hidden grayscale">

            <div class="btn-group-right">
                <button onclick="showStep(2)"
                    class="btn-compact border border-gray-300 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </button>
                <button onclick="registerAdmission()" class="btn-compact bg-green-600 text-white hover:bg-green-700">
                    <i class="fa-solid fa-check"></i> Register
                </button>
            </div>
        </div>
    </div>

    <script>
        const localApi = axios.create({
            baseURL: '/'
        });
        localApi.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');
        const geoApi = axios.create({
            baseURL: 'https://bdapis.com/api/v1.2'
        });

        // --- CUSTOM DROPDOWN ENGINE ---
        function toggleDropdown(display) {
            const wrapper = display.closest('.custom-select-wrapper');
            const options = wrapper.querySelector('.custom-select-options');

            document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                if (w !== wrapper) {
                    w.classList.remove('active');
                    w.querySelector('.custom-select-options').style.display = 'none';
                }
            });

            wrapper.classList.toggle('active');
            options.style.display = wrapper.classList.contains('active') ? 'block' : 'none';
        }

        window.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-select-wrapper')) {
                document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                    w.classList.remove('active');
                    w.querySelector('.custom-select-options').style.display = 'none';
                });
            }
        });

        function updateCustomOptions(selectId) {
            const select = document.getElementById(selectId);
            const wrapper = select.closest('.custom-select-wrapper');
            if (!wrapper) return;
            const optionsContainer = wrapper.querySelector('.custom-select-options');
            const displayText = wrapper.querySelector('.selected-text');

            optionsContainer.innerHTML = '';

            Array.from(select.options).forEach((opt, index) => {
                const div = document.createElement('div');
                div.className = 'custom-option capitalize-text';
                div.textContent = opt.textContent;
                div.onclick = () => {
                    select.selectedIndex = index;
                    displayText.textContent = opt.textContent;
                    wrapper.classList.remove('active');
                    optionsContainer.style.display = 'none';
                    select.dispatchEvent(new Event('change'));
                };
                optionsContainer.appendChild(div);
            });
            displayText.textContent = select.options[select.selectedIndex]?.textContent || 'Select...';
        }

        // --- DATA LOADING ---
        async function loadInitialData() {
            try {
                const res = await localApi.get('/api/get-school-classes');
                const classSelect = document.getElementById('a_class');
                classSelect.innerHTML = '<option value="">Select Class</option>';
                res.data.data.forEach(item => {
                    let opt = new Option(item.class_name, item.id);
                    classSelect.add(opt);
                });
                updateCustomOptions('a_class');
            } catch (e) {
                console.error(e);
            }
        }

        async function loadGroups() {
            const classId = document.getElementById('a_class').value;
            const groupSelect = document.getElementById('a_group');
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            if (!classId) {
                updateCustomOptions('a_group');
                return;
            }
            try {
                const res = await localApi.get(`/api/get-school-groups?class_id=${classId}`);
                res.data.data.forEach(g => groupSelect.add(new Option(g.group_name, g.id)));
            } catch (e) {
                console.error(e);
            }
            updateCustomOptions('a_group');
            loadSections();
        }

        async function loadSections() {
            const classId = document.getElementById('a_class').value;
            const groupId = document.getElementById('a_group').value;
            const sectionSelect = document.getElementById('a_section');
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (!classId) {
                updateCustomOptions('a_section');
                return;
            }
            try {
                const res = await localApi.get(`/api/get-school-sections?group_id=${groupId}`);
                let data = res.data.data;
                if (!groupId) data = data.filter(s => s.class_id == classId);
                data.forEach(s => sectionSelect.add(new Option(s.section_name, s.id)));
            } catch (e) {
                console.error(e);
            }
            updateCustomOptions('a_section');
            loadSessions();
        }

        async function loadSessions() {
            const classId = document.getElementById('a_class').value;
            const groupId = document.getElementById('a_group').value;
            const sectionId = document.getElementById('a_section').value;

            const sessionSelect = document.getElementById('a_session');
            sessionSelect.innerHTML = '<option value="">Select Session</option>';

            if (!classId) {
                updateCustomOptions('a_session');
                return;
            }

            try {
                const res = await localApi.get('/api/get-school-sessions', {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });

                res.data.data.forEach(s => sessionSelect.add(new Option(s.session_year, s.id)));
            } catch (e) {
                console.error("Session load failed:", e);
            }
            updateCustomOptions('a_session');
        }

        async function loadFees() {
            const classId = document.getElementById('a_class').value;
            const sessionId = document.getElementById('a_session').value;
            const feeInput = document.getElementById('a_fee');

            if (!classId || !sessionId) {
                feeInput.value = '';
                return;
            }

            feeInput.value = 'Loading...';

            try {
                const res = await localApi.get('/api/fee-types', {
                    params: {
                        class_id: classId,
                        session_id: sessionId,
                        search: 'Admission',
                        all: 1
                    }
                });

                const fees = res.data.data;
                const admissionFee = fees && fees.length > 0 ? fees[0] : null;
                feeInput.value = admissionFee ? admissionFee.amount : 'No fee defined';

            } catch (e) {
                console.error("Fee Load Error:", e);
                feeInput.value = 'Error';
            }
        }

        // --- GEO LOADING ---
        async function loadLocations() {
            try {
                const res = await geoApi.get('/divisions');
                const divs = res.data.data;
                document.querySelectorAll('.hidden-select[id*="division"]').forEach(s => {
                    s.innerHTML = '<option value="">Division</option>';
                    divs.forEach(d => s.add(new Option(d.division, d.division)));
                    updateCustomOptions(s.id);
                });
            } catch (err) {
                console.error(err);
            }
        }

        async function populateDistrict(divId, distId, upaId) {
            const divisionName = document.getElementById(divId).value;
            const districtSelect = document.getElementById(distId);
            districtSelect.innerHTML = '<option value="">Loading...</option>';
            updateCustomOptions(distId);
            if (!divisionName) return;
            const res = await geoApi.get(`/division/${divisionName}`);
            districtSelect.innerHTML = '<option value="">District</option>';
            res.data.data.forEach(d => districtSelect.add(new Option(d.district, d.district)));
            updateCustomOptions(distId);
        }

        async function populateUpazila(distId, upaId) {
            const districtName = document.getElementById(distId).value;
            const upazilaSelect = document.getElementById(upaId);
            upazilaSelect.innerHTML = '<option value="">Loading...</option>';
            updateCustomOptions(upaId);
            if (!districtName) return;
            const res = await geoApi.get(`/district/${districtName}`);
            const upazillas = res.data.data[0].upazillas;
            upazilaSelect.innerHTML = '<option value="">Upazila</option>';
            upazillas.forEach(u => upazilaSelect.add(new Option(u, u)));
            updateCustomOptions(upaId);
        }

        // --- FORM FLOW ---
        function showStep(step) {
            for (let i = 1; i <= 3; i++) {
                document.getElementById('step' + i).classList.add('hidden');
                let mark = document.getElementById('step' + i + 'mark');
                mark.classList.remove('step-active');
            }
            document.getElementById('step' + step).classList.remove('hidden');
            document.getElementById('step' + step + 'mark').classList.add('step-active');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function validateStep(step) {
            let valid = true;
            document.querySelectorAll('#step' + step + ' .required-field').forEach(el => {
                let wrapper = el.closest('.custom-select-wrapper') || el;
                let display = el.classList.contains('hidden-select') ? wrapper.querySelector(
                    '.custom-select-display') : el;
                if (!el.value.trim() || el.value === 'No fee defined' || el.value === 'Loading...') {
                    display.style.borderColor = '#dc2626';
                    valid = false;
                } else {
                    display.style.borderColor = '#d1d5db';
                }
            });
            return valid;
        }

        function nextStep(step) {
            if (!validateStep(step)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required',
                    text: 'Please fill all marked fields.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }
            let mark = document.getElementById('step' + step + 'mark');
            mark.classList.remove('step-active');
            mark.classList.add('step-complete');
            mark.innerHTML = '<i class="fa fa-check"></i>';
            showStep(step + 1);
        }

        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            input.type = (input.type === 'password') ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        function previewImage(event) {
            const img = document.getElementById('studentPreview');
            const file = event.target.files[0];
            if (file) {
                img.src = URL.createObjectURL(file);
                img.classList.remove('hidden');
            }
        }

        function registerAdmission() {
            if (!validateStep(3)) return;

            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.form-input, .custom-select-display').forEach(el => {
                el.style.borderColor = '#d1d5db';
            });

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we secure your data.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            let formData = new FormData();
            formData.append('school_id', document.getElementById('school_id').value);

            document.querySelectorAll('input, select').forEach(el => {
                if (el.id && !['studentPass', 'studentPass2', 'student_image'].includes(el.id)) {
                    formData.append(el.id, el.value);
                }
            });

            formData.append('password', document.getElementById('studentPass').value);
            formData.append('password_confirmation', document.getElementById('studentPass2').value);

            let img = document.getElementById('student_image').files[0];
            if (img) formData.append('image', img);

            localApi.post('/api/school-admission', formData)
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful!',
                        text: res.data.message,
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        window.location.href = res.data.redirect;
                    });
                })
                .catch(err => {
                    Swal.close();

                    if (err.response && err.response.status === 422) {
                        const errors = err.response.data.errors;
                        let firstErrorStep = null;

                        Object.keys(errors).forEach((field, index) => {
                            let fieldId = field;
                            if (field === 'password') fieldId = 'studentPass';
                            if (field === 'image') fieldId = 'student_image';

                            const input = document.getElementById(fieldId);
                            if (input) {
                                const wrapper = input.closest('.custom-select-wrapper') || input;
                                const displayElement = input.classList.contains('hidden-select') ?
                                    wrapper.querySelector('.custom-select-display') :
                                    input;

                                displayElement.style.borderColor = '#dc2626';

                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'error-message text-[10px] text-red-600 mt-1 font-medium';
                                errorDiv.innerText = errors[field][0];
                                wrapper.parentNode.insertBefore(errorDiv, wrapper.nextSibling);

                                if (index === 0) {
                                    const stepContainer = input.closest('[id^="step"]');
                                    if (stepContainer) firstErrorStep = stepContainer.id.replace('step', '');
                                }
                            }
                        });

                        if (firstErrorStep) showStep(firstErrorStep);

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please check the highlighted fields.',
                            confirmButtonColor: '#2563eb'
                        });

                    } else {
                        // --- IMPROVED SYSTEM ERROR HANDLING ---
                        const errorTitle = err.response?.data?.message || 'System Error';
                        const errorDetail = err.response?.data?.error || err.message || 'An unknown error occurred.';

                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            html: `<div class="text-left bg-gray-100 p-3 rounded text-xs font-mono text-red-600 max-h-40 overflow-y-auto">${errorDetail}</div>`,
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Understood'
                        });
                    }
                });
        }

        async function syncPermanentFromCurrent() {
            if (!document.getElementById('sameAsCurrentAddress').checked) return;

            const currentDivision = document.getElementById('current_division');
            const currentDistrict = document.getElementById('current_district');
            const currentUpazila = document.getElementById('current_upazila');

            const permanentDivision = document.getElementById('permanent_division');
            permanentDivision.value = currentDivision.value;
            updateCustomOptions('permanent_division');

            document.getElementById('permanent_village').value = document.getElementById('current_village').value;

            if (!currentDivision.value) return;

            await populateDistrict('permanent_division', 'permanent_district', 'permanent_upazila');
            const permanentDistrict = document.getElementById('permanent_district');
            permanentDistrict.value = currentDistrict.value;
            updateCustomOptions('permanent_district');

            if (!currentDistrict.value) return;

            await populateUpazila('permanent_district', 'permanent_upazila');
            const permanentUpazila = document.getElementById('permanent_upazila');
            permanentUpazila.value = currentUpazila.value;
            updateCustomOptions('permanent_upazila');
        }

        function toggleSameAddress() {
            const checked = document.getElementById('sameAsCurrentAddress').checked;
            ['permanent_division', 'permanent_district', 'permanent_upazila'].forEach(id => {
                const wrapper = document.getElementById(id)?.closest('.custom-select-wrapper');
                if (!wrapper) return;
                const display = wrapper.querySelector('.custom-select-display');
                display.style.pointerEvents = checked ? 'none' : '';
                display.style.opacity = checked ? '0.6' : '';
                display.style.cursor = checked ? 'not-allowed' : '';
                display.style.background = checked ? '#f9fafb' : '';
            });
            const villageInput = document.getElementById('permanent_village');
            if (villageInput) {
                villageInput.readOnly = checked;
                villageInput.style.background = checked ? '#f9fafb' : '';
                villageInput.style.color = checked ? '#6b7280' : '';
                villageInput.style.cursor = checked ? 'not-allowed' : '';
            }
            if (checked) syncPermanentFromCurrent();
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadInitialData();
            loadLocations();

            document.getElementById('current_division')?.addEventListener('change', async () => {
                await populateDistrict('current_division', 'current_district', 'current_upazila');
                syncPermanentFromCurrent();
            });
            document.getElementById('current_district')?.addEventListener('change', async () => {
                await populateUpazila('current_district', 'current_upazila');
                syncPermanentFromCurrent();
            });
            document.getElementById('current_upazila')?.addEventListener('change', () => syncPermanentFromCurrent());
            document.getElementById('current_village')?.addEventListener('input', () => syncPermanentFromCurrent());

            document.getElementById('permanent_division')?.addEventListener('change', () =>
                populateDistrict('permanent_division', 'permanent_district', 'permanent_upazila'));
            document.getElementById('permanent_district')?.addEventListener('change', () =>
                populateUpazila('permanent_district', 'permanent_upazila'));
        });
    </script>
@endsection