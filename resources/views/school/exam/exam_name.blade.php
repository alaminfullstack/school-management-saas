@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
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

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
        }

        .modal-content-sharp {
            border-radius: 0 !important;
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
                            <input type="text" id="examSearch" placeholder="Search Exam..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="openFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openExamModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Create Exam
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="examSearchMobile" placeholder="Search Exam..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="closeOnOutsideClick(event, 'filterModal')">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Exam filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="filter_class" onchange="handleCascade(this, 'filter', 'group')"
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
                                <select id="filter_group" onchange="handleCascade(this, 'filter', 'section')"
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
                                <select id="filter_section" onchange="handleCascade(this, 'filter', 'session')"
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
                                <select id="filter_session"
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
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl">
                    <div class="flex flex-col gap-1.5">
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th width="60">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Exam Name</th>
                                <th width="120" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="examTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0</div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- Main Exam Modal --}}
    <div id="examModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'examModal')">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Add Exam Name
                </h3>
            </div>

            <form id="examForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="edit_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required onchange="handleCascade(this, 'modal', 'group')"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required onchange="handleCascade(this, 'modal', 'section')"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required onchange="handleCascade(this, 'modal', 'session')"
                                style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Exam
                                Name</label>
                            <input type="text" id="exam_name" placeholder="e.g. First Term"
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                required style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeExamModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        let currentPage = 1;

        // 1. Initial Load for Classes
        function loadInitialClasses() {
            axios.get('/api/get-school-classes').then(res => {
                let modalOpts = '<option value="">Select Class</option>';
                let filterOpts = '<option value="">All Classes</option>';
                res.data.data.forEach(c => {
                    const opt =
                        `<option value="${c.class_name}" data-id="${c.id}">${c.class_name}</option>`;
                    modalOpts += opt;
                    filterOpts += opt;
                });
                document.getElementById('class_name').innerHTML = modalOpts;
                document.getElementById('filter_class').innerHTML = filterOpts;
            });
        }

        // 2. Cascading Logic (Updated to always pass class_id to sessions)
        function handleCascade(element, context, nextLevel) {
            const selectedOption = element.options[element.selectedIndex];
            const parentId = selectedOption.getAttribute('data-id');

            // Identify the class_id from the specific context (modal or filter)
            const classElement = document.getElementById(context === 'modal' ? 'class_name' : 'filter_class');
            const classId = classElement.options[classElement.selectedIndex]?.getAttribute('data-id');

            const targets = {
                group: context === 'modal' ? 'group_name' : 'filter_group',
                section: context === 'modal' ? 'section_name' : 'filter_section',
                session: context === 'modal' ? 'session_name' : 'filter_session'
            };

            if (nextLevel === 'group') {
                resetSelect(targets.group, 'Group');
                resetSelect(targets.section, 'Section');
                resetSelect(targets.session, 'Session');
                if (parentId) fetchAndFill(`/api/get-school-groups?class_id=${parentId}`, targets.group, 'Group',
                    'group_name');
            } else if (nextLevel === 'section') {
                resetSelect(targets.section, 'Section');
                resetSelect(targets.session, 'Session');
                if (parentId) fetchAndFill(`/api/get-school-sections?group_id=${parentId}`, targets.section, 'Section',
                    'section_name');
            } else if (nextLevel === 'session') {
                resetSelect(targets.session, 'Session');
                // FIX: Must pass class_id along with section_id to satisfy the Controller mandate
                if (parentId && classId) {
                    fetchAndFill(`/api/get-school-sessions?class_id=${classId}&section_id=${parentId}`, targets.session,
                        'Session', 'session_year');
                }
            }
        }

        function fetchAndFill(url, elementId, label, textField) {
            axios.get(url).then(res => {
                let opts = `<option value="">Select ${label}</option>`;
                res.data.data.forEach(item => {
                    opts +=
                        `<option value="${item[textField]}" data-id="${item.id}">${item[textField]}</option>`;
                });
                document.getElementById(elementId).innerHTML = opts;
            });
        }

        function resetSelect(id, label) {
            const el = document.getElementById(id);
            if (el) el.innerHTML = `<option value="">Select ${label}</option>`;
        }

        // 3. Table Fetching
        function fetchExams(page = 1) {
            currentPage = page;
            const params = {
                page,
                search: document.getElementById('examSearch').value,
                class_name: document.getElementById('filter_class').value,
                group_name: document.getElementById('filter_group').value,
                section_name: document.getElementById('filter_section').value,
                session_name: document.getElementById('filter_session').value
            };

            axios.get('/api/school-exam-names', {
                params
            }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('examTableBody');
                tbody.innerHTML = '';

                if (meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="7" class="text-center py-10 text-gray-400 font-black tracking-widest">No records found</td></tr>`;
                    return;
                }

                meta.data.forEach((item, i) => {
                    tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition-colors">
                       <td class="text-gray-400">${meta.from + i}</td>
                        <td>${item.class_name}</td>
                        <td>${item.group_name || 'N/A'}</td>
                        <td><span class="bg-gray-100 px-2 py-0.5 text-[10px]">${item.section_name}</span></td>
                        <td>${item.session_name}</td>
                        <td class="text-blue-600">${item.exam_name}</td>
                        <td>
                            <div class="flex justify-center gap-3">
                                <button onclick="editExam(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                <button onclick="deleteExam(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                renderPagination(meta);
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText =
                `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const btnClass = "pagination-btn";
            controls.innerHTML +=
                `<button class="${btnClass}" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchExams(${meta.current_page - 1})"><i class="mdi mdi-chevron-left"></i></button>`;

            for (let i = 1; i <= meta.last_page; i++) {
                controls.innerHTML +=
                    `<button class="${btnClass} ${meta.current_page === i ? 'active' : ''}" onclick="fetchExams(${i})">${i}</button>`;
            }

            controls.innerHTML +=
                `<button class="${btnClass}" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchExams(${meta.current_page + 1})"><i class="mdi mdi-chevron-right"></i></button>`;
        }

        // 4. Form Submission
        document.getElementById('examForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const data = {
                class_name: document.getElementById('class_name').value,
                group_name: document.getElementById('group_name').value,
                section_name: document.getElementById('section_name').value,
                session_name: document.getElementById('session_name').value,
                exam_name: document.getElementById('exam_name').value,
            };

            const req = id ? axios.put(`/api/school-exam-names/${id}`, data) : axios.post('/api/school-exam-names',
                data);
            req.then(() => {
                Toastify({
                    text: "Success!",
                    style: {
                        background: "#2563eb"
                    }
                }).showToast();
                closeExamModal();
                fetchExams(currentPage);
            }).catch(() => Swal.fire('Error', 'Verification failed', 'error'));
        };

        // 5. Edit (includes class_id in all dependency fetches)
        async function editExam(id) {
            const res = await axios.get(`/api/school-exam-names/${id}`);
            const d = res.data;

            document.getElementById('edit_id').value = d.id;
            document.getElementById('exam_name').value = d.exam_name;
            document.getElementById('modalTitle').innerText = "Edit Exam Name";

            const classSelect = document.getElementById('class_name');
            classSelect.value = d.class_name;
            const classId = classSelect.options[classSelect.selectedIndex].getAttribute('data-id');

            // Fetch Groups
            const groupRes = await axios.get(`/api/get-school-groups?class_id=${classId}`);
            fillSpecific('group_name', groupRes.data.data, 'group_name', 'Group');
            document.getElementById('group_name').value = d.group_name || '';

            // Fetch Sections
            let groupId = '';
            if (document.getElementById('group_name').selectedIndex > 0) {
                groupId = document.getElementById('group_name').options[document.getElementById('group_name')
                    .selectedIndex].getAttribute('data-id');
            }
            const secRes = await axios.get(`/api/get-school-sections?class_id=${classId}&group_id=${groupId}`);
            fillSpecific('section_name', secRes.data.data, 'section_name', 'Section');
            document.getElementById('section_name').value = d.section_name;

            // Fetch Sessions (Must include class_id)
            const sectionId = document.getElementById('section_name').options[document.getElementById('section_name')
                .selectedIndex].getAttribute('data-id');
            const sessRes = await axios.get(`/api/get-school-sessions?class_id=${classId}&section_id=${sectionId}`);
            fillSpecific('session_name', sessRes.data.data, 'session_year', 'Session');
            document.getElementById('session_name').value = d.session_name;

            document.getElementById('examModal').classList.remove('hidden');
        }

        function fillSpecific(id, data, field, label) {
            let opts = `<option value="">Select ${label}</option>`;
            data.forEach(item => {
                opts += `<option value="${item[field]}" data-id="${item.id}">${item[field]}</option>`;
            });
            document.getElementById(id).innerHTML = opts;
        }

        function deleteExam(id) {
            Swal.fire({
                title: 'Delete record?',
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-exam-names/${id}`).then(() => fetchExams(currentPage));
            });
        }

        function openExamModal() {
            document.getElementById('examForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = "Add Exam Name";
            resetSelect('group_name', 'Group');
            resetSelect('section_name', 'Section');
            resetSelect('session_name', 'Session');
            document.getElementById('examModal').classList.remove('hidden');
        }

        function closeExamModal() {
            document.getElementById('examModal').classList.add('hidden');
        }

        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        function resetFilters() {
            document.getElementById('filter_class').value = "";
            resetSelect('filter_group', 'Group');
            resetSelect('filter_section', 'Section');
            resetSelect('filter_session', 'Session');
            fetchExams(1);
        }

        function applyFilters() {
            closeFilterModal();
            fetchExams(1);
        }


        //Export Modal Opening Script
        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => {
                document.getElementById(id).classList.toggle('hidden', !show);
            };

            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });


        document.getElementById('examSearch').addEventListener('input', () => fetchExams(1));
        loadInitialClasses();
        fetchExams();
    </script>
@endsection
