@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
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

        /* ================= Table Container ================= */
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

        /* ================= Table Header ================= */
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
            /* Only first letter capitalized */
            text-transform: capitalize !important;
            letter-spacing: 0.01em !important;
        }

        th:last-child {
            border-right: none !important;
        }

        /* ================= Table Body ================= */
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

        /* ================= Pagination Bar (Balanced Height) ================= */
        .pagination-bar {
            padding: 0.6rem 1rem !important;
            border: 1px solid #d1d5db !important;
            border-top: none !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            background: #ffffff !important;
            min-height: 44px !important;
        }

        .pagination-btn {
            height: 26px !important;
            min-width: 26px !important;
            padding: 0 8px !important;
            border: 1px solid #e2e8f0 !important;
            background: white !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            color: #64748b !important;
            border-radius: 0 !important;
            /* Sharp Brutalism Corners */
            text-transform: uppercase !important;
            transition: all 0.1s ease !important;
        }

        .pagination-btn.active {
            background: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }

        .pagination-btn:hover:not(:disabled):not(.active) {
            border-color: #2563eb !important;
            color: #2563eb !important;
        }

        .pagination-btn:disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
            background: #f1f5f9 !important;
        }

        #paginationInfo {
            font-size: 9px !important;
            font-weight: 800 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        /* ================= Mobile Adjustments ================= */
        @media (max-width: 768px) {

            th,
            td {
                padding: 0 8px !important;
                height: 30px !important;
            }

            .pagination-bar {
                min-height: 38px !important;
                padding: 0.4rem 0.75rem !important;
            }

            .pagination-btn {
                height: 24px !important;
                min-width: 24px !important;
            }
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .btn-outline-premium {
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .pagination-btn {
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: bold;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }

        .pagination-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .action-icon {
            font-size: 1.25rem;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-container input {
            padding-left: 32px !important;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="header_search" placeholder="Search ID or Name..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" onkeyup="if(event.key === 'Enter') fetchTable(1)" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="toggleFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openAdmitModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Admit Card
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="header_search_mobile" placeholder="Search ID or Name..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;"
                        onkeyup="if(event.key === 'Enter') { document.getElementById('header_search').value = this.value; fetchTable(1); }" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="toggleFilterModal()">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Admit Card filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="filter_class_name" onchange="handleCascade(this, 'filter_group')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="filter_group_name" onchange="handleCascade(this, 'filter_section')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="filter_section_name" onchange="handleCascade(this, 'filter_session')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="filter_session_name"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Exam Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Exam Name</label>
                            <div class="relative">
                                <select id="filter_exam_name"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="resetFilters()"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button onclick="applyFilters()"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="this.classList.add('hidden')">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl"
                    onclick="event.stopPropagation()">
                    <div class="flex flex-col gap-1.5">
                        <button onclick="exportData('pdf-mobile')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button onclick="exportData('excel')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button onclick="exportData('pdf')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button onclick="document.getElementById('exportModal').classList.add('hidden')"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1000px]">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Exam Name</th>
                                <th>Student Id</th>
                                <th>Student Name</th>
                                <th>Admit Number</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="admitTableBody"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase" id="paginationInfo"></div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admit Card Modal --}}
    <div id="admitModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Bulk Admit Card Generator
                </h3>
            </div>

            <form id="admitForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="edit_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_name" onchange="handleCascade(this, 'group')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_name" onchange="handleCascade(this, 'section')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_name" onchange="handleCascade(this, 'session')"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_name" onchange="fetchStudentCount()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Exam
                                Name</label>
                            <select id="exam_name" onchange="generateRange()"
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
                        </div>

                        <div id="studentStatusBox"
                            class="col-span-1 sm:col-span-2 bg-white p-3 border border-dashed border-slate-300 my-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-400 mb-1">Student
                                Status</label>
                            <div id="studentCountDisplay"
                                class="text-[11px] font-mono font-bold text-blue-600 tracking-tighter">
                                0 Students Identified
                            </div>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Start
                                Number</label>
                            <input type="text" id="admit_card_start_number"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] bg-gray-50 font-mono"
                                readonly style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">End
                                Number</label>
                            <input type="text" id="admit_card_end_number"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] bg-gray-50 font-mono"
                                readonly style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeAdmitModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" disabled
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap disabled:opacity-50"
                        style="border-radius: 0;">
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let studentsList = [];

        function loadInitial() {
            axios.get('/api/get-school-classes').then(res => {
                let opts = '<option value="">Select Class</option>';
                res.data.data.forEach(c => opts +=
                    `<option value="${c.class_name}" data-id="${c.id}">${c.class_name}</option>`);
                document.getElementById('class_name').innerHTML = opts;
                document.getElementById('filter_class_name').innerHTML = opts;
            });
            fetchFilteredExams();
        }

        function fetchFilteredExams(isFilter = false) {
            const prefix = isFilter ? 'filter_' : '';
            const classEl = document.getElementById(`${prefix}class_name`);
            const groupEl = document.getElementById(`${prefix}group_name`);
            const sectionEl = document.getElementById(`${prefix}section_name`);
            const sessionEl = document.getElementById(`${prefix}session_name`);
            
            const params = {
                class_name: classEl.value,
                group_name: groupEl.value,
                section_name: sectionEl.value,
                session_name: sessionEl.value,
            };

            axios.get('/api/get-school-exams', {
                params
            }).then(res => {
                let opts = '<option value="">Select Exam</option>';
                res.data.data.forEach(e => opts +=
                    `<option value="${e.exam_name}" data-id="${e.id}">${e.exam_name}</option>`);
                document.getElementById(`${prefix}exam_name`).innerHTML = opts;
            });
        }

        async function handleCascade(el, next, callback = null) {
            const id = el.options[el.selectedIndex]?.getAttribute('data-id');
            const isFilter = el.id.startsWith('filter_');
            if (!id) return;

            if (next.includes('group')) {
                await fetchFill(`/api/get-school-groups?class_id=${id}`, isFilter ? 'filter_group_name' : 'group_name',
                    'Group', 'group_name');
            } else if (next.includes('section')) {
                await fetchFill(`/api/get-school-sections?group_id=${id}`, isFilter ? 'filter_section_name' :
                    'section_name', 'Section', 'section_name');
            } else if (next.includes('session')) {
                const classId = document.getElementById(isFilter ? 'filter_class_name' : 'class_name').options[document
                    .getElementById(isFilter ? 'filter_class_name' : 'class_name').selectedIndex]?.getAttribute(
                    'data-id');
                await fetchFill(`/api/get-school-sessions?section_id=${id}&class_id=${classId}`, isFilter ?
                    'filter_session_name' : 'session_name', 'Session', 'session_year');
            }

            fetchFilteredExams(isFilter);
            if (!isFilter) fetchStudentCount();
            if (callback) callback();
        }

        function fetchFill(url, tid, lbl, fld) {
            return axios.get(url).then(res => {
                let o = `<option value="">Select ${lbl}</option>`;
                res.data.data.forEach(i => o += `<option value="${i[fld]}" data-id="${i.id}">${i[fld]}</option>`);
                document.getElementById(tid).innerHTML = o;
            });
        }

        function fetchStudentCount() {
            const getVal = (id) => document.getElementById(id).options[document.getElementById(id).selectedIndex]
                ?.getAttribute('data-id') || '';
            const params = {
                class_id: getVal('class_name'),
                group_id: getVal('group_name'),
                section_id: getVal('section_name'),
                session_id: getVal('session_name')
            };

            if (!params.class_id || !params.session_id) {
                document.getElementById('studentCountDisplay').innerText = `0 Students Identified`;
                document.getElementById('submitBtn').disabled = true;
                return;
            }

            axios.get('/api/get-school-students', {
                params
            }).then(res => {
                studentsList = res.data.data;
                document.getElementById('studentCountDisplay').innerText =
                    `${studentsList.length} Students Identified`;
                document.getElementById('submitBtn').disabled = studentsList.length === 0;
                generateRange();
            });
        }

        function generateRange() {
            const exm = document.getElementById('exam_name').value;
            if (exm && studentsList.length > 0) {
                const startBase = 24951080;
                document.getElementById('admit_card_start_number').value = startBase;
                document.getElementById('admit_card_end_number').value = startBase + (studentsList.length - 1);
            }
        }

        function fetchTable(page = 1) {
            const params = {
                page,
                class_name: document.getElementById('filter_class_name').value,
                group_name: document.getElementById('filter_group_name').value,
                section_name: document.getElementById('filter_section_name').value,
                session_name: document.getElementById('filter_session_name').value,
                exam_name: document.getElementById('filter_exam_name').value,
                search: document.getElementById('header_search').value
            };

            axios.get('/api/school-exam-admit-cards', {
                params
            }).then(res => {
                const meta = res.data;
                const body = document.getElementById('admitTableBody');
                body.innerHTML = '';
                meta.data.forEach((item, i) => {
                    body.innerHTML += `<tr>
                    <td>${meta.from + i}</td>
                    <td>${item.class_name}</td>
                    <td>${item.group_name || '-'}</td>
                    <td>${item.section_name || '-'}</td>
                    <td>${item.session_name}</td>
                    <td>${item.exam_name}</td>
                    <td class="font-mono">${item.student_id_number}</td>
                    <td class="capitalize">${item.student_name}</td>
                    <td>${item.admit_card_number}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <button onclick='editAdmit(${JSON.stringify(item)})' class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteAdmit(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
                });
                renderPagination(meta);
            });
        }

        function exportData(type) {
            const params = {
                class_name: document.getElementById('filter_class_name').value,
                group_name: document.getElementById('filter_group_name').value,
                section_name: document.getElementById('filter_section_name').value,
                session_name: document.getElementById('filter_session_name').value,
                exam_name: document.getElementById('filter_exam_name').value,
                search: document.getElementById('header_search').value,
                export: type
            };

            if (type === 'pdf' || type === 'pdf-mobile') {
                axios.get('/api/school-exam-admit-cards', {
                    params: {
                        ...params,
                        per_page: 500
                    }
                }).then(res => {
                    if (type === 'pdf-mobile') {
                        generateMobilePreview(res.data.data, res.data.school_info, params);
                    } else {
                        generatePrintLayout(res.data.data, res.data.school_info);
                    }
                });
            } else {
                window.location.href = `/api/export-admit-cards?${new URLSearchParams(params).toString()}`;
            }
        }

        function generateMobilePreview(admitCards, school, params) {
            let previewWindow = window.open('', '_blank');
            const address = [school?.village, school?.upazila, school?.district].filter(x => x).join(', ') ||
                'Dhaka, Bangladesh';

            let html = `<!DOCTYPE html><html><head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
                <title>Admit Card Mobile Preview</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
                <style>
                    * { box-sizing: border-box; border-radius: 0 !important; }
                    body { background: #f3f4f6; font-family: 'Inter', sans-serif; margin: 0; padding: 0; width: 100vw; overflow-x: hidden; }
                    .mobile-screen { height: 100vh; width: 100vw; display: flex; flex-direction: column; padding: 15px; border-bottom: 2px dashed #ccc; scroll-snap-align: start; page-break-after: always; position: relative; }
                    .card-border { border: 2px solid #000; padding: 20px; height: 85%; display: flex; flex-direction: column; justify-content: space-between; background: white; position: relative; }
                    .capitalize { text-transform: capitalize; }
                    .no-wrap { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
                    html { scroll-snap-type: y mandatory; }
                    @media print { .no-print { display: none; } .mobile-screen { border: none; height: 100vh; padding: 0; } .card-border { height: 100%; border-width: 1px; } }
                </style></head><body>`;

            admitCards.forEach(card => {
                html += `
                <div class="mobile-screen">
                    <div class="card-border">
                        <div class="text-center">
                            <div class="flex justify-center mb-3">
                                 ${school?.logo ? `<img src="${school.logo}" class="h-14 w-14 object-contain">` : '<div class="h-14"></div>'}
                            </div>
                            <h1 class="text-lg font-black capitalize no-wrap">${school?.school_name || 'School Name'}</h1>
                            <p class="text-[9px] font-bold text-gray-500 capitalize no-wrap">${address}</p>
                            <div class="mt-4">
                                <span class="border-2 border-black px-5 py-1 text-[10px] font-black tracking-tighter uppercase">Admit Card</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-6 text-[12px]">
                            <div class="space-y-3">
                                <p class="no-wrap"><strong>Class:</strong> <span class="capitalize">${card.class_name}</span></p>
                                <p class="no-wrap"><strong>Group:</strong> <span class="capitalize">${card.group_name || '-'}</span></p>
                                <p class="no-wrap"><strong>Section:</strong> <span class="capitalize">${card.section_name || '-'}</span></p>
                                <p class="no-wrap"><strong>Session:</strong> ${card.session_name}</p>
                            </div>
                            <div class="space-y-3">
                                <p class="no-wrap"><strong>Student ID:</strong> ${card.student_id_number}</p>
                                <p class="no-wrap"><strong>Name:</strong> <span class="capitalize">${card.student_name}</span></p>
                                <p class="no-wrap"><strong>Exam:</strong> <span class="capitalize">${card.exam_name}</span></p>
                                <p class="no-wrap"><strong>Admit No:</strong> <span class="font-bold">${card.admit_card_number}</span></p>
                            </div>
                        </div>

                        <div class="flex justify-center mt-8 pb-4">
                            <div class="text-center">
                                <div class="border-t border-black w-32 pt-1 text-[9px] font-bold capitalize">Principal Signature</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center py-4 no-print">
                        <button onclick="window.print()" class="border-2 border-black bg-white text-black px-4 py-2 text-[10px] font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all active:scale-95">
                            Download PDF
                        </button>
                    </div>
                </div>`;
            });

            html += `</body></html>`;
            previewWindow.document.write(html);
            previewWindow.document.close();
        }

        function generatePrintLayout(admitCards, school) {
            let printWindow = window.open('', '_blank');
            const address = [school?.village, school?.upazila, school?.district].filter(x => x).join(', ') ||
                'Dhaka, Bangladesh';

            let html = `<html><head><title>Print Admit Cards</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
                <style>
                    @page { size: A4; margin: 0; }
                    * { border-radius: 0 !important; }
                    body { margin: 0; padding: 0; background: #fff; font-family: 'Inter', sans-serif; }
                    .page { width: 210mm; min-height: 297mm; padding: 10mm; margin: auto; }
                    .admit-card-container { height: 130mm; width: 100%; border: 2px solid #000; padding: 30px; position: relative; box-sizing: border-box; margin-bottom: 10mm; }
                    .admit-card-container:nth-child(2n) { margin-bottom: 0; }
                    .page-break { page-break-after: always; }
                    .capitalize { text-transform: capitalize; }
                    @media print { body { -webkit-print-color-adjust: exact; } .page { border: none; padding: 10mm; } }
                </style></head><body>`;

            admitCards.forEach((card, index) => {
                if (index % 2 === 0) html += '<div class="page">';
                html += `
                <div class="admit-card-container">
                    <div class="text-center mb-4">
                        <div class="flex justify-center mb-2">
                             ${school?.logo ? `<img src="${school.logo}" class="h-16 w-16 object-contain">` : ''}
                        </div>
                        <h1 class="text-2xl font-black mb-0 capitalize">${school?.school_name || 'School Name'}</h1>
                        <p class="text-sm font-bold text-gray-600 mb-1 capitalize">${address}</p>
                        <div class="mt-4">
                            <span class="border-2 border-black px-10 py-2 text-md font-black tracking-widest uppercase">Admit Card</span>
                        </div>
                        <div class="border-b-2 border-black w-full mt-6"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-12 gap-y-6 mt-10 text-base">
                        <div class="flex flex-col gap-4">
                            <p><strong>Class:</strong> <span class="capitalize">${card.class_name}</span></p>
                            <p><strong>Group:</strong> <span class="capitalize">${card.group_name || '-'}</span></p>
                            <p><strong>Section:</strong> <span class="capitalize">${card.section_name || '-'}</span></p>
                            <p><strong>Session:</strong> ${card.session_name}</p>
                        </div>
                        <div class="flex flex-col gap-4">
                            <p><strong>Student ID:</strong> ${card.student_id_number}</p>
                            <p><strong>Student Name:</strong> <span class="capitalize">${card.student_name}</span></p>
                            <p><strong>Exam Name:</strong> <span class="capitalize">${card.exam_name}</span></p>
                            <p><strong>Admit No:</strong> <span class="font-mono font-bold text-xl">${card.admit_card_number}</span></p>
                        </div>
                    </div>
                    <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="text-center">
                            <div class="border-t border-black w-48 pt-2 text-xs font-bold capitalize">Principal Signature</div>
                        </div>
                    </div>
                </div>`;

                if ((index + 1) % 2 === 0 || index === admitCards.length - 1) {
                    html += '</div>';
                    if ((index + 1) % 2 === 0 && index !== admitCards.length - 1) html +=
                        '<div class="page-break"></div>';
                }
            });

            html += `<script>window.onload = function() { window.print(); window.close(); };<\/script></body></html>`;
            printWindow.document.write(html);
            printWindow.document.close();
        }

        async function editAdmit(item) {
            openAdmitModal();
            document.getElementById('modalTitle').innerText = 'Edit Individual Admit Card';
            document.getElementById('edit_id').value = item.id;
            document.getElementById('submitBtn').innerText = 'Update Admit Card';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('studentStatusBox').classList.add('hidden');

            const classEl = document.getElementById('class_name');
            classEl.value = item.class_name;
            await handleCascade(classEl, 'group');
            const groupEl = document.getElementById('group_name');
            groupEl.value = item.group_name || '';
            await handleCascade(groupEl, 'section');
            const secEl = document.getElementById('section_name');
            secEl.value = item.section_name || '';
            await handleCascade(secEl, 'session');
            document.getElementById('session_name').value = item.session_name;

            fetchFilteredExams(false);
            document.getElementById('exam_name').value = item.exam_name;
            document.getElementById('admit_card_start_number').value = item.admit_card_start_number;
            document.getElementById('admit_card_end_number').value = item.admit_card_end_number;
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (info) info.innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            controls.innerHTML +=
                `<button class="pagination-btn" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchTable(${meta.current_page - 1})"><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    controls.innerHTML +=
                        `<button class="pagination-btn ${meta.current_page === i ? 'active' : ''}" onclick="fetchTable(${i})">${i}</button>`;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    controls.innerHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }
            controls.innerHTML +=
                `<button class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTable(${meta.current_page + 1})"><i class="mdi mdi-chevron-right"></i></button>`;
        }

        document.getElementById('admitForm').onsubmit = function(e) {
            e.preventDefault();
            const editId = document.getElementById('edit_id').value;
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;

            const payload = {
                class_name: document.getElementById('class_name').value,
                group_name: document.getElementById('group_name').value,
                section_name: document.getElementById('section_name').value,
                session_name: document.getElementById('session_name').value,
                exam_name: document.getElementById('exam_name').value,
                admit_card_start_number: document.getElementById('admit_card_start_number').value,
                admit_card_end_number: document.getElementById('admit_card_end_number').value,
                students: studentsList
            };

            const request = editId ? axios.put(`/api/school-exam-admit-cards/${editId}`, payload) : axios.post(
                '/api/school-exam-admit-cards', payload);

            request.then(res => {
                Toastify({
                    text: res.data.message,
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeAdmitModal();
                fetchTable(1);
            }).catch(err => {
                const msg = err.response?.data?.status === 'exists' ? err.response.data.message :
                    'Operation failed';
                Swal.fire('Warning', msg, 'warning');
            }).finally(() => btn.disabled = false);
        };

        function deleteAdmit(id) {
            Swal.fire({
                title: 'Remove Admit Card?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444'
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-exam-admit-cards/${id}`).then(() => fetchTable());
            });
        }

        function toggleFilterModal() {
            document.getElementById('filterModal').classList.toggle('hidden');
        }

        function applyFilters() {
            fetchTable(1);
            toggleFilterModal();
        }

        function resetFilters() {
            ['filter_class_name', 'filter_group_name', 'filter_section_name', 'filter_session_name', 'filter_exam_name',
                'header_search'
            ].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            fetchTable(1);
            toggleFilterModal();
        }

        function openAdmitModal() {
            document.getElementById('admitForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = 'Bulk Admit Card Generator';
            document.getElementById('studentStatusBox').classList.remove('hidden');
            document.getElementById('studentCountDisplay').innerText = '0 Students Identified';
            document.getElementById('submitBtn').innerText = 'Generate All Cards';
            document.getElementById('admitModal').classList.remove('hidden');
        }

        function closeAdmitModal() {
            document.getElementById('admitModal').classList.add('hidden');
        }

        loadInitial();
        fetchTable();
    </script>
@endsection
