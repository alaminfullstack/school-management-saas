@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
        }

        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: .75rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        .table-card {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            width: 100%;
            overflow: hidden;
            border-left: none;
            border-right: none;
        }

        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 15px !important;
        }

        /* ================= Custom Scrollbar ================= */
        .table-responsive::-webkit-scrollbar {
            height: 6px !important;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 0px !important;
        }

        /* ================= Table Core ================= */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            border: 1px solid #d1d5db !important;
            font-size: 11px !important;
        }

        th {
            padding: 0 12px !important;
            height: 34px !important;
            line-height: 34px !important;
            white-space: nowrap !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            color: #374151 !important;
            font-weight: 800 !important;
            vertical-align: middle !important;
            text-align: left !important;
            text-transform: capitalize !important;
            letter-spacing: 0.01em !important;
        }

        th:last-child {
            border-right: none !important;
        }

        tr {
            height: 32px !important;
        }

        td {
            padding: 0 12px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            font-size: 11px !important;
            color: #4b5563 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
        }

        td:last-child {
            border-right: none !important;
        }

        tbody tr:hover {
            background: #f9fafb !important;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            background: #fff;
            outline: none;
        }

        .modal-content-sharp {
            border-radius: 0 !important;
        }

        .search-tab {
            cursor: pointer;
            padding: 12px 16px;
            font-size: 10px;
            font-weight: 800;
            text-transform: capitalize;
            border-bottom: 2px solid transparent;
            color: #94a3b8;
            transition: all 0.2s;
        }

        .search-tab.active {
            color: #2563eb;
            border-bottom-color: #2563eb;
            background: #f8fafc;
        }

        .hidden {
            display: none !important;
        }

        /* ================= Transcript Specific Styling ================= */
        .a4-report {
            width: 210mm;
            min-height: 280mm;
            padding: 12mm;
            margin: 10px auto;
            background: white;
            border: 1px solid #d1d5db;
            color: #000;
            box-sizing: border-box;
        }

        .info-label {
            font-weight: 700;
            width: 110px;
            display: inline-block;
            font-size: 10px;
            text-transform: capitalize;
        }

        .info-value {
            font-weight: 400;
            font-size: 10px;
            text-transform: capitalize;
        }

        .equal-height-container {
            display: flex;
            align-items: stretch;
            gap: 1rem;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            /* Removes browser headers and footers (date, title, URL) */
            @page {
                margin: 0;
            }

            body {
                margin: 1.6cm;
            }

            #resultContainer,
            #resultContainer * {
                visibility: visible;
            }

            #resultContainer {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                margin: 0;
                border: none;
            }

            .a4-report {
                border: none;
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Academic
                            Result Management</h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span style="text-transform: capitalize;">School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500" style="text-transform: capitalize;">Search &
                                Transcripts</span>
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1">
                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                            style="text-transform: capitalize;">Export
                        </button>
                        <button onclick="openSearchModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap"
                            style="text-transform: capitalize;">Find
                            Result</button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive" id="resultContainer">
                    <table class="w-full" id="mainResultTable">
                        <thead id="resultHeader">
                            <tr>
                                <th class="text-center py-10 text-gray-400 font-medium" style="text-transform: capitalize;">
                                    Click "Find Result" to generate
                                    academic reports or tabular sheets</th>
                            </tr>
                        </thead>
                        <tbody id="resultBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Universal Search Modal --}}
    <div id="searchModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md modal-content-sharp shadow-2xl flex flex-col border border-gray-100">
            <div class="flex border-b">
                <div onclick="switchSearchTab('single')" id="tab-single" class="search-tab active flex-1 text-center">Single
                    Result</div>
                <div onclick="switchSearchTab('class')" id="tab-class" class="search-tab flex-1 text-center">Classwise
                    Result</div>
            </div>

            <div class="p-6 bg-gray-50/30">
                {{-- Single Search Fields --}}
                <div id="single-fields" class="space-y-4">
                    <div>
                        <label class="text-[10px] text-gray-500 capitalize font-bold block mb-1">Student ID Number</label>
                        <input type="text" id="s_student_id" class="form-input-fixed h-[36px]"
                            placeholder="Ex: 24012601">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 capitalize font-bold block mb-1">Admit Card Number</label>
                        <input type="text" id="s_admit_no" class="form-input-fixed h-[36px]" placeholder="Ex: 24951080">
                    </div>
                </div>

                {{-- Classwise Search Fields --}}
                <div id="class-fields" class="space-y-3 hidden">
                    <select id="c_class" onchange="handleClassChange()" class="form-input-fixed h-[36px]">
                        <option value="">Select Class</option>
                    </select>
                    <select id="c_group" onchange="handleGroupChange()" class="form-input-fixed h-[36px]">
                        <option value="">Select Group</option>
                    </select>
                    <select id="c_section" onchange="handleSectionChange()" class="form-input-fixed h-[36px]">
                        <option value="">Select Section</option>
                    </select>
                    <select id="c_session" onchange="handleSessionChange()" class="form-input-fixed h-[36px]">
                        <option value="">Select Session</option>
                    </select>
                    <select id="c_exam" class="form-input-fixed h-[36px]">
                        <option value="">Select Exam</option>
                    </select>
                </div>
            </div>

            <div class="p-4 border-t bg-white flex gap-2">
                <button onclick="closeSearchModal()"
                    class="flex-1 h-9 border text-[10px] font-bold capitalize">Cancel</button>
                <button onclick="executeFind()" class="flex-1 h-9 btn-outline-premium text-[10px] capitalize">Generate
                </button>
            </div>
        </div>
    </div>

    {{-- Export Modal --}}
    <div id="exportModal"
        class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
        onclick="this.classList.add('hidden')">
        <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl" onclick="event.stopPropagation()">
            <div class="flex flex-col gap-1.5">
                {{-- PDF Button --}}
                <button onclick="window.print()"
                    class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                    PDF
                </button>

                {{-- EXCEL Button --}}
                <button onclick="exportToExcel()"
                    class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                    EXCEL
                </button>

                {{-- PRINT Button --}}
                <button onclick="window.print()"
                    class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap hover:bg-gray-50 transition-all">
                    PRINT
                </button>

                {{-- CANCEL Button --}}
                <button id="closeExport" onclick="document.getElementById('exportModal').classList.add('hidden')"
                    class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all tracking-tighter">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        let searchMode = 'single';
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        document.addEventListener('DOMContentLoaded', () => {
            fetchClasses();
        });

        function toTitleCase(str) {
            if (!str) return 'N/A';
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        function fetchClasses() {
            axios.get('/api/get-school-classes').then(res => {
                const el = document.getElementById('c_class');
                el.innerHTML = '<option value="">Select Class</option>';
                res.data.data.forEach(item => {
                    el.innerHTML +=
                        `<option value="${item.class_name}" data-id="${item.id}">${item.class_name}</option>`;
                });
            });
        }

        function handleClassChange() {
            const classSelect = document.getElementById('c_class');
            const classId = classSelect.options[classSelect.selectedIndex]?.getAttribute('data-id');
            ['c_group', 'c_section', 'c_session', 'c_exam'].forEach(id => document.getElementById(id).innerHTML =
                `<option value="">Select ${id.split('_')[1]}</option>`);

            if (!classId) return;
            axios.get(`/api/get-school-groups?class_id=${classId}`).then(res => {
                const el = document.getElementById('c_group');
                res.data.data.forEach(item => el.innerHTML +=
                    `<option value="${item.group_name}" data-id="${item.id}">${item.group_name}</option>`);
                fetchSessions();
            });
        }

        function handleGroupChange() {
            const groupSelect = document.getElementById('c_group');
            const groupId = groupSelect.options[groupSelect.selectedIndex]?.getAttribute('data-id');
            ['c_section', 'c_session', 'c_exam'].forEach(id => document.getElementById(id).innerHTML =
                `<option value="">Select ${id.split('_')[1]}</option>`);
            if (!groupId) {
                fetchSessions();
                return;
            }
            axios.get(`/api/get-school-sections?group_id=${groupId}`).then(res => {
                const el = document.getElementById('c_section');
                res.data.data.forEach(item => el.innerHTML +=
                    `<option value="${item.section_name}" data-id="${item.id}">${item.section_name}</option>`);
                fetchSessions();
            });
        }

        function handleSectionChange() {
            fetchSessions();
        }

        function fetchSessions() {
            const c = document.getElementById('c_class');
            const g = document.getElementById('c_group');
            const s = document.getElementById('c_section');
            const cId = c.options[c.selectedIndex]?.getAttribute('data-id') || '';
            const gId = g.options[g.selectedIndex]?.getAttribute('data-id') || '';
            const sId = s.options[s.selectedIndex]?.getAttribute('data-id') || '';

            if (!cId) return;
            axios.get(`/api/get-school-sessions?class_id=${cId}&group_id=${gId}&section_id=${sId}`).then(res => {
                const el = document.getElementById('c_session');
                el.innerHTML = '<option value="">Select Session</option>';
                res.data.data.forEach(item => {
                    const val = item.session_year || item.session_name;
                    el.innerHTML += `<option value="${val}">${val}</option>`;
                });
            });
        }

        function handleSessionChange() {
            const sess = document.getElementById('c_session').value;
            const cls = document.getElementById('c_class').value;
            const grp = document.getElementById('c_group').value;
            const sec = document.getElementById('c_section').value;
            if (!sess || !cls) return;
            
            const params = {
                session_name: sess,
                class_name: cls
            };
            if (grp) params.group_name = grp;
            if (sec) params.section_name = sec;
            
            axios.get('/api/get-school-exams', { params }).then(res => {
                const el = document.getElementById('c_exam');
                el.innerHTML = '<option value="">Select Exam</option>';
                res.data.data.forEach(item => el.innerHTML +=
                    `<option value="${item.exam_name}">${item.exam_name}</option>`);
            });
        }

        function openSearchModal() {
            document.getElementById('searchModal').classList.remove('hidden');
        }

        function closeSearchModal() {
            document.getElementById('searchModal').classList.add('hidden');
        }

        function switchSearchTab(mode) {
            searchMode = mode;
            document.getElementById('tab-single').classList.toggle('active', mode === 'single');
            document.getElementById('tab-class').classList.toggle('active', mode === 'class');
            document.getElementById('single-fields').classList.toggle('hidden', mode !== 'single');
            document.getElementById('class-fields').classList.toggle('hidden', mode !== 'class');
        }

        function executeFind() {
            const payload = searchMode === 'single' ? {
                mode: 'single',
                student_id: document.getElementById('s_student_id').value,
                admit_no: document.getElementById('s_admit_no').value
            } : {
                mode: 'classwise',
                class: document.getElementById('c_class').value,
                group: document.getElementById('c_group').value,
                section: document.getElementById('c_section').value,
                session: document.getElementById('c_session').value,
                exam: document.getElementById('c_exam').value
            };

            axios.post('/api/school-find-results', payload).then(res => {
                if (searchMode === 'single') renderSingleResult(res.data);
                else renderClasswiseTable(res.data);
                closeSearchModal();
            }).catch(err => {
                Swal.fire('Error', err.response?.data?.message || 'Data Fetch Failed', 'error');
            });
        }

        function renderSingleResult(data) {
            const container = document.getElementById('resultContainer');
            const gradingScale = data.grading_scale || [];
            const formattedDate = new Date().toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const address = `${data.school_info.village}, ${data.school_info.upazila}, ${data.school_info.district}`;

            container.innerHTML = `
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

            @media print { 
                @page { size: A4 portrait; margin: 0; } 
                html, body { height: 100%; margin: 0 !important; padding: 0 !important; overflow: hidden; }
                body * { visibility: hidden; }
                #resultContainer, #resultContainer * { visibility: visible; }
                #resultContainer { position: absolute; left: 0; top: 0; width: 100%; padding: 8mm; box-sizing: border-box; }
                .no-print { display: none !important; }
            }
            
            .a4-report { 
                background: white; 
                font-family: 'Roboto', sans-serif; 
                page-break-inside: avoid; 
                height: auto; 
            }
            
            /* Unified Subtle Grey Border Design */
            .info-table, .grading-table, .marks-table, .table-header-box, .marks-table th, .marks-table td, .info-table td, .grading-table td, .grading-table th { 
                border: 0.5px solid #d1d5db !important; 
            }

            .split-table-container { display: flex; gap: 15px; align-items: stretch; margin-top: 8px; }
            .split-table-container > div { width: 50%; display: flex; flex-direction: column; }
            
            .info-table, .grading-table { width: 100%; border-collapse: collapse; flex-grow: 1; }
            .info-table td, .grading-table td, .grading-table th { padding: 3px 6px; font-size: 10px; vertical-align: middle; }
            
            .table-header-box { background: #f9fafb; border-bottom: none !important; padding: 4px; text-align: center; font-weight: 800; font-size: 10px; text-transform: capitalize; }
            
            .marks-table { width: 100%; border-collapse: collapse; margin-top: -1px; }
            .marks-table th, .marks-table td { padding: 4px 6px; font-size: 11px; }
            
            .text-center-header { text-align: center !important; }
            .capitalize-all { text-transform: capitalize !important; }
        </style>

        <div class="a4-report">
            <div class="text-center mb-2">
                <h1 class="text-xl font-black mb-0 capitalize-all">${data.school_info.school_name.toLowerCase()}</h1>
                <p class="text-[10px] font-bold text-gray-500 mb-0.5 capitalize-all">${address.toLowerCase()}</p>
                <p class="text-[11px] font-black text-gray-800 capitalize-all">${data.exam_name.toLowerCase()} - ${data.session_name}</p>
                <div class="mt-2 mb-3">
                    <span class="border-[1.5px] border-black px-6 py-1 text-xs font-black tracking-widest capitalize-all">Academic Transcript</span>
                </div>
            </div>

            <div class="split-table-container">
                <div>
                    <div class="table-header-box capitalize-all">Student Information</div>
                    <table class="info-table">
                        <tr><td width="90">Student ID</td><td class="font-bold">${data.student_id_number}</td></tr>
                        <tr><td>Student Name</td><td class="font-bold capitalize-all">${data.student_name.toLowerCase()}</td></tr>
                        <tr><td>Father's Name</td><td class="capitalize-all">${data.father_name.toLowerCase()}</td></tr>
                        <tr><td>Class</td><td class="capitalize-all">${data.class_name.toLowerCase()}</td></tr>
                        <tr><td>Section</td><td class="capitalize-all">${(data.section_name || 'n/a').toLowerCase()}</td></tr>
                        <tr><td>Group</td><td class="capitalize-all">${(data.group_name || 'general').toLowerCase()}</td></tr>
                        <tr><td>Session</td><td>${data.session_name}</td></tr>
                    </table>
                </div>
                <div>
                    <div class="table-header-box capitalize-all">Grading Scale</div>
                    <table class="grading-table text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-center-header capitalize-all">Marks</th>
                                <th class="text-center-header capitalize-all">Grade</th>
                                <th class="text-center-header capitalize-all">Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${gradingScale.map(g => `
                                            <tr>
                                                <td>${Math.round(g.min_mark)}-${Math.round(g.max_mark)}</td>
                                                <td class="font-bold">${g.letter_name}</td>
                                                <td>${parseFloat(g.number_point).toFixed(2)}</td>
                                            </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <div class="table-header-box w-full capitalize-all">Subject-Wise Mark Sheet</div>
                <table class="marks-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th width="40" class="text-center-header capitalize-all">Sl</th>
                            <th class="text-left capitalize-all">Subject</th>
                            <th width="80" class="text-center-header capitalize-all">Marks</th>
                            <th width="80" class="text-center-header capitalize-all">Grade</th>
                            <th width="80" class="text-center-header capitalize-all">Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.subjects.map((s, i) => `
                                        <tr>
                                            <td class="text-center">${i + 1}</td>
                                            <td class="font-bold capitalize-all">${s.name.toLowerCase()}</td>
                                            <td class="text-center font-bold">${s.mark ?? '0'}</td>
                                            <td class="text-center font-bold">${s.grade}</td>
                                            <td class="text-center">${parseFloat(s.point).toFixed(2)}</td>
                                        </tr>`).join('')}
                        <tr class="bg-gray-50 font-black">
                            <td colspan="3" class="px-4 py-1.5 text-right capitalize-all text-[10px]">Grade Point Average (GPA)</td>
                            <td colspan="2" class="text-center text-blue-900 text-base">${data.gpa}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-8 px-4">
                <div class="text-center">
                    <p class="text-[10px] font-bold capitalize-all">Publish Date: ${formattedDate}</p>
                </div>
                <div class="text-center">
                    <div class="w-36 border-t-[1px] border-black mb-1"></div>
                    <p class="text-[10px] font-bold capitalize-all">Principal's Signature</p>
                </div>
            </div>
        </div>`;
        }

        function renderClasswiseTable(data) {
            const container = document.getElementById('resultContainer');
            const gradingScale = data.grading_scale || [];
            const className = document.getElementById('c_class').value;
            const groupName = document.getElementById('c_group').value || 'N/A';
            const sectionName = document.getElementById('c_section').value || 'N/A';
            const sessionName = document.getElementById('c_session').value;
            const examName = document.getElementById('c_exam').value;

            container.innerHTML = `
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

            /* Clear browser artifacts and hide non-target elements */
            @media print { 
                @page { size: A4 landscape; margin: 0; } 
                html, body { height: 100%; margin: 0 !important; padding: 0 !important; overflow: hidden; background: white; }
                
                /* Strict Visibility Toggle: Hides everything except target */
                body * { visibility: hidden !important; }
                #resultContainer, #resultContainer * { visibility: visible !important; }
                
                #resultContainer { 
                    position: absolute; 
                    left: 0; 
                    top: 0; 
                    width: 100%; 
                    padding: 8mm; 
                    box-sizing: border-box; 
                    display: block !important;
                }
                
                .no-print { display: none !important; }
                .a4-landscape-print { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            }
            
            .a4-landscape-print { 
                background: white; 
                width: 100%; 
                font-family: 'Roboto', sans-serif; 
                color: #000;
            }
            
            /* Elite Subtle Grey Border Design - No Border Radius */
            .info-table, .grading-table, #mainResultTable, 
            #mainResultTable th, #mainResultTable td, 
            .info-table td, .grading-table td, 
            .grading-table th, .table-header-box { 
                border: 0.5px solid #d1d5db !important; 
                border-radius: 0 !important; 
            }

            .split-table-container { 
                display: flex; 
                justify-content: space-between; 
                align-items: stretch; 
                margin-bottom: 15px; 
                gap: 20px;
            }
            .info-container, .grading-container { flex: 1; display: flex; flex-direction: column; max-width: 350px; }
            
            .info-table, .grading-table { width: 100%; border-collapse: collapse; flex-grow: 1; }
            .info-table td, .grading-table td, .grading-table th { padding: 3px 6px; font-size: 10px; vertical-align: middle; }
            
            .table-header-box { 
                background: #f9fafb; 
                border-bottom: none !important; 
                padding: 4px; 
                text-align: center; 
                font-weight: 800; 
                font-size: 10px; 
                text-transform: capitalize; 
            }
            
            .vertical-header { padding: 8px 2px !important; vertical-align: bottom; text-align: center; background: #f9fafb; width: 30px; }
            .vertical-header div { 
                writing-mode: vertical-rl; 
                transform: rotate(180deg); 
                text-align: left; 
                max-height: 100px; 
                display: inline-block; 
                font-weight: 700; 
                font-size: 9px; 
                white-space: nowrap; 
            }
            
            #mainResultTable { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
            #mainResultTable td { font-size: 9px; padding: 4px 2px; text-align: center; overflow: hidden; }
            #mainResultTable th { font-size: 9px; font-weight: 700; padding: 4px 2px; background: #f9fafb; }
            
            .student-name-cell { text-align: left !important; padding-left: 8px !important; text-transform: capitalize; }
            .capitalize-all { text-transform: capitalize !important; }
        </style>
        
        <div class="a4-landscape-print">
            <div class="text-center mb-4">
                <h1 class="text-xl font-black mb-0 capitalize-all">${toTitleCase(data.school_name)}</h1>
                <p class="text-[10px] font-bold text-gray-500 mb-0.5 capitalize-all">${toTitleCase(data.location || '')}</p>
                <p class="text-[11px] font-black text-gray-800 capitalize-all tracking-widest">${toTitleCase(examName)} Result Sheet</p>
            </div>
            
            <div class="split-table-container">
                <div class="info-container">
                    <div class="table-header-box">Class Information</div>
                    <table class="info-table">
                        <tr><td width="90">Class</td><td class="font-bold capitalize-all">${toTitleCase(className)}</td></tr>
                        <tr><td>Group</td><td class="font-bold capitalize-all">${toTitleCase(groupName)}</td></tr>
                        <tr><td>Section</td><td class="font-bold capitalize-all">${toTitleCase(sectionName)}</td></tr>
                        <tr><td>Session</td><td class="font-bold">${sessionName}</td></tr>
                    </table>
                </div>
                <div class="grading-container">
                    <div class="table-header-box">Grading Scale</div>
                    <table class="grading-table text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-center">Marks</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">Point</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${gradingScale.map(g => `
                                        <tr>
                                            <td>${Math.round(g.min_mark)}-${Math.round(g.max_mark)}</td>
                                            <td class="font-bold">${g.letter_name}</td>
                                            <td>${parseFloat(g.number_point).toFixed(2)}</td>
                                        </tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            </div>

            <table id="mainResultTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th width="25">Sl</th>
                        <th width="55">ID</th>
                        <th class="student-name-cell" width="140">Student Name</th>
                        ${data.subjects_list.map(sub => `<th class="vertical-header"><div>${toTitleCase(sub)}</div></th>`).join('')}
                        <th width="40">Total</th>
                        <th width="40">GPA</th>
                        <th width="40">Grade</th>
                        <th width="35" class="no-print">Del</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.students.map((std, i) => `
                                <tr>
                                    <td>${i + 1}</td>
                                    <td>${std.student_id}</td>
                                    <td class="student-name-cell font-bold truncate">${toTitleCase(std.name)}</td>
                                    ${data.subjects_list.map(sub => `<td>${std.marks[sub] || '0'}</td>`).join('')}
                                    <td class="font-bold">${std.total}</td>
                                    <td class="font-black text-blue-800">${std.gpa}</td>
                                    <td class="font-bold">${std.grade}</td>
                                    <td class="no-print">
                                        <button onclick="confirmDelete(${std.id})" class="text-red-500">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>`).join('')}
                </tbody>
            </table>
        </div>`;
        }

        function exportToExcel() {
            const table = document.getElementById('mainResultTable');
            if (!table || table.rows.length <= 1) return;
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "Results"
            });
            XLSX.writeFile(wb, "Exam_Results.xlsx");
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete'
            }).then((res) => {
                if (res.isConfirmed) axios.delete(`/api/school-results/${id}`).then(() => executeFind());
            });
        }
    </script>
@endsection