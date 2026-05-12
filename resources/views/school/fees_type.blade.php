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
            font-family: 'Inter', sans-serif;
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
        }

        /* ================= Table Container ================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto !important;
            display: block;
            background: white;
            padding: 15px;
        }

        /* ================= Table ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            border: 1px solid #d1d5db;
            font-size: 12px;
        }

        /* ================= Table Header ================= */
        th {
            padding: 0 10px !important;
            height: 32px !important;
            min-height: 32px !important;
            line-height: 32px !important;
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            color: #374151;
            /* Natural dark text color */
            font-weight: 700;
            /* Normal Bold */
            vertical-align: middle;
            text-align: left;
            text-transform: none !important;
            /* Removes any forced casing */
        }

        /* Specifically center the Photo (first) and Action (last) headers */
        th:last-child {
            text-align: center !important;
        }

        th:last-child {
            text-align: center !important;
        }

        /* ================= Table Body ================= */
        tr {
            height: 32px !important;
            min-height: 32px !important;
        }

        td {
            padding: 0 10px !important;
            vertical-align: middle;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            font-size: 12px;
            height: 32px !important;
            min-height: 32px !important;
            color: inherit;
            /* Inherits normal text color */
        }

        /* ================= Circular Image Styling ================= */
        td img {
            /* Equal width and height are vital to prevent the "egg" shape */
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            /* Crops the image to fit the circle */
            display: block;
            margin: 0 auto;
            padding: 0 !important;
        }

        /* Shrink all other elements inside td */
        td *:not(img) {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
            height: auto !important;
            display: inline-block;
            max-height: 28px;
        }

        /* Remove right border for last column */
        td:last-child {
            border-right: none;
        }

        /* ================= Hover Effect ================= */
        tbody tr:hover {
            background: #f3f4f6;
        }

        /* ================= Mobile Adjustments ================= */
        @media (max-width: 768px) {

            tr,
            th,
            td {
                height: 30px !important;
                min-height: 30px !important;
                line-height: 30px !important;
            }

            th,
            td {
                padding: 0 6px !important;
            }

            td img {
                width: 24px !important;
                height: 24px !important;
            }

            .table-responsive {
                padding: 12px;
            }
        }

        .btn-outline-premium {
            background: transparent;
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

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .4rem .6rem;
            border-radius: 0;
            font-size: .8rem;
            background: #fff;
            outline: none;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
        }

        .hidden-field {
            display: none;
        }

        /* 5. Pagination Styling - Compact & Sharp (UI Modifications Version) */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.75rem;
            background: #ffffff;
            border-top: 1px solid #edf2f7;
            min-height: 48px;
        }

        #paginationControls {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .pagination-btn {
            padding: 2px 8px;
            min-width: 28px;
            height: 26px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            border-radius: 0;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
            background: #f8fafc;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        #paginationInfo {
            font-size: 9px !important;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }

        /* ================= Pagination Mobile Adjustments ================= */
        @media (max-width: 768px) {
            .pagination-container {
                padding: 0.3rem 0.5rem;
                /* Tighter padding */
                min-height: 35px;
                /* Slimmer bar */
            }

            .pagination-btn {
                min-width: 24px;
                /* Narrower buttons */
                height: 24px;
                /* Shorter buttons */
                padding: 0 4px;
                /* Minimal side padding */
                font-size: 9px;
                /* Smaller text */
                gap: 2px;
            }

            #paginationInfo {
                font-size: 8px !important;
                /* Tiny info text */
            }

            #paginationControls {
                gap: 2px;
                /* Pull buttons closer together */
            }
        }

        #feeModal {
            transition: opacity 0.2s ease-in-out;
        }

        #feeModal.hidden {
            display: none;
            opacity: 0;
        }

        #feeModal:not(.hidden) {
            display: flex;
            opacity: 1;
        }

        @media (max-width: 640px) {

            /* Ensures the text stays absolutely centered in the shorter buttons */
            .btn-outline-secondary,
            .btn-outline-premium {
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1 !important;
            }

            /* Minimize icon size further if needed on tiny screens */
            .mdi {
                margin-right: 2px !important;
            }
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
                            <input type="text" id="feeSearch" placeholder="Search Fees..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openFeeModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Fee
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="feeSearchMobile" placeholder="Search Fees..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    {{-- Modal Title Section --}}
                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Fee type filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    {{-- Form Field Section --}}
                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Classes</option>
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
                                <select id="groupFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Groups</option>
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
                                <select id="sectionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sections</option>
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
                                <select id="sessionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sessions</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Button Section --}}
                    <div class="flex gap-2">
                        <button id="resetFilter"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button id="applyFilter"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

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
                    <table class="min-w-[1200px]">
                        <thead>
                            <tr>
                                <th class="text-left">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Fees Type</th>
                                <th>Fees Name</th>
                                <th>Amount</th>
                                <th>Due Fee</th>
                                <th>Pay Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="feeTableBody" class="bg-white divide-y divide-gray-100">
                            <tr>
                                <td colspan="13"
                                    class="text-center py-10 text-gray-400 uppercase text-[10px] font-bold tracking-widest">
                                    <i class="mdi mdi-loading mdi-spin mr-2"></i> Initializing Account Records...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo"> 0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fee Modal --}}
    <div id="feeModal" class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle" class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Add New Fee
                </h3>
            </div>

            <form id="feeForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="fee_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_id" onchange="loadGroups()" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_id" onchange="loadSections()" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_id" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <select id="session_id" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" required style="border-radius: 0;"></select>
                        </div>

                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Select
                                Fee Type
                            </label>
                            <select id="fee_type_name" class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]" required onchange="handleFeeTypeChange()" style="border-radius: 0;">
                                <option value="">Select Fee Type</option>
                                <option value="Admission">Admission Fee</option>
                                <option value="Monthly">Monthly Fee</option>
                                <option value="Exams">Exam Fee</option>
                                <option value="Boarding Food">Boarding Food</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <div id="div_fee_name" class="col-span-1 hidden-field">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Fee Name</label>
                            <input type="text" id="fee_name_input" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" placeholder="e.g. Jan-2026 Tuition" style="border-radius: 0;" />
                        </div>

                        <div id="div_exam_name" class="col-span-1 hidden-field">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Exam Name</label>
                            <select id="exam_id" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" onchange="autoFillExamFee()" style="border-radius: 0;"></select>
                        </div>

                        <div id="div_student_name" class="col-span-1 hidden-field">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Student Name</label>
                            <select id="student_id" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius: 0;"></select>
                        </div>

                        <div id="div_amount" class="col-span-1 hidden-field">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Amount</label>
                            <input type="number" id="amount" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs text-blue-600 bg-blue-50/20 h-[32px]" placeholder="0.00" style="border-radius: 0;" />
                        </div>

                        <div id="div_pay_date" class="col-span-1 hidden-field">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Pay Date</label>
                            <input type="date" id="pay_date" class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius: 0;" />
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeFeeModal()" class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap" style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn" class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap" style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const api = axios.create({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        let currentPage  = 1;
        let examList     = [];
        let studentsList = [];
        let classesList  = [];
        let groupsList   = [];
        let sectionsList = [];

        // --- Core Initialization ---

        async function preloadData() {
            try {
                const results = await Promise.allSettled([
                    api.get('/api/get-school-classes'),
                    api.get('/api/get-school-exams'),
                    api.get('/api/school/students'),
                    api.get('/api/get-school-groups'),
                    api.get('/api/get-school-sections')
                ]);

                classesList = results[0].status === 'fulfilled' ? (results[0].value.data.data || []) : [];
                examList = results[1].status === 'fulfilled' ? (results[1].value.data.data || []) : [];
                studentsList = results[2].status === 'fulfilled' ? (results[2].value.data.data || []) : [];
                groupsList = results[3].status === 'fulfilled' ? (results[3].value.data.data || []) : [];
                sectionsList = results[4].status === 'fulfilled' ? (results[4].value.data.data || []) : [];

                populateDropdown('class_id', classesList, 'id', 'class_name', 'Select Class');
                populateDropdown('exam_id', examList, 'id', 'exam_name', 'Select Exam');
                populateDropdown('student_id', studentsList, 'id', 'student_name', 'Select Student');

                populateDropdown('classFilter', classesList, 'id', 'class_name', 'All Classes');

                if (classesList.length > 0) {
                    const firstClassId = classesList[0].id;
                    document.getElementById('classFilter').value = firstClassId;
                    await handleFilterClassChange(true);
                } else {
                    fetchFeeTypes(1);
                }

            } catch (e) {
                console.error("Data pre-loading failed", e);
                fetchFeeTypes(1);
            }
        }

        function populateDropdown(elemId, data, valKey, textKey, defaultText) {
            const s = document.getElementById(elemId);
            if (!s) return;
            let html = `<option value="">${defaultText}</option>`;
            data.forEach(item => html += `<option value="${item[valKey]}">${item[textKey]}</option>`);
            s.innerHTML = html;
        }

        // --- Student Fetching Logic ---

        async function fetchStudents(selectedStudentId = null) {
            const type = document.getElementById('fee_type_name').value;
            if (type !== 'Boarding Food') return;

            const params = {
                class_id: document.getElementById('class_id').value,
                group_id: document.getElementById('group_id').value,
                section_id: document.getElementById('section_id').value,
                session_id: document.getElementById('session_id').value
            };

            if (!params.class_id || !params.session_id) return;

            try {
                const res = await api.get('/api/get-school-students', {
                    params
                });
                const students = res.data.data || [];

                const s = document.getElementById('student_id');
                let html = `<option value="">Select Student</option>`;
                students.forEach(st => {
                    html +=`<option value="${st.id}" ${selectedStudentId == st.id ? 'selected' : ''}>${st.student_name}</option>`;
                });
                s.innerHTML = html;
            } catch (e) {
                console.error("Fetch Students failed", e);
            }
        }

        // --- Filter & Form Cascading Logic ---

        async function handleFilterClassChange(isInitial = false) {
            const classId = document.getElementById('classFilter').value;
            const gSelect = document.getElementById('groupFilter');
            let html = '<option value="">All Groups</option>';
            groupsList.filter(g => g.class_id == classId).forEach(g => html +=`<option value="${g.id}">${g.group_name}</option>`);
            gSelect.innerHTML = html;
            if (isInitial && gSelect.options.length > 1) gSelect.selectedIndex = 1;
            await handleFilterGroupChange(isInitial);
        }

        async function handleFilterGroupChange(isInitial = false) {
            const groupId = document.getElementById('groupFilter').value;
            const secSelect = document.getElementById('sectionFilter');
            let html = '<option value="">All Sections</option>';
            if (groupId) sectionsList.filter(sec => sec.group_id == groupId).forEach(sec => html +=
                `<option value="${sec.id}">${sec.section_name}</option>`);
            secSelect.innerHTML = html;
            if (isInitial && secSelect.options.length > 1) secSelect.selectedIndex = 1;
            await handleFilterSectionChange(isInitial);
        }

        async function handleFilterSectionChange(isInitial = false) {
            const classId = document.getElementById('classFilter').value;
            const groupId = document.getElementById('groupFilter').value;
            const sectionId = document.getElementById('sectionFilter').value;
            const sessSelect = document.getElementById('sessionFilter');
            if (!classId) {
                sessSelect.innerHTML = '<option value="">All Sessions</option>';
                if (isInitial) fetchFeeTypes(1);
                return;
            }
            try {
                const res = await api.get('/api/get-school-sessions', {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });
                const sessions = res.data.data || [];
                let html = '<option value="">All Sessions</option>';
                sessions.forEach(sess => html += `<option value="${sess.id}">${sess.session_year}</option>`);
                sessSelect.innerHTML = html;
                if (isInitial && sessions.length > 0) sessSelect.value = sessions[0].id;
            } catch (e) {
                console.error(e);
            }
            if (isInitial) fetchFeeTypes(1);
        }

        // --- Main Form Helpers (Load Groups/Sections/Sessions) ---

        function loadGroups(selectedGroupId = null) {
            const classId = document.getElementById('class_id').value;

            // Reset downstream fields and fee-related inputs when class changes
            if (!selectedGroupId) {
                document.getElementById('group_id').innerHTML   = '<option value="">No Group</option>';
                document.getElementById('section_id').innerHTML = '<option value="">No Section</option>';
                document.getElementById('session_id').innerHTML = '<option value="">Select Session</option>';
                document.getElementById('fee_type_name').value  = '';
                document.getElementById('fee_name_input').value = '';
                document.getElementById('amount').value         = '';
                document.getElementById('pay_date').value       = '';
                document.getElementById('exam_id').innerHTML    = '<option value="">Select Exam</option>';
                document.getElementById('student_id').innerHTML = '<option value="">Select Student</option>';
                ['div_fee_name', 'div_exam_name', 'div_student_name', 'div_amount', 'div_pay_date']
                    .forEach(f => { document.getElementById(f).style.display = 'none'; });
            }

            const s = document.getElementById('group_id');
            let html = '<option value="">No Group</option>';
            groupsList.filter(g => g.class_id == classId).forEach(g => {
                html += `<option value="${g.id}" ${selectedGroupId == g.id ? 'selected' : ''}>${g.group_name}</option>`;
            });
            s.innerHTML = html;
            loadSections();
        }

        function loadSections(selectedSectionId = null) {
            const groupId = document.getElementById('group_id').value;
            const s = document.getElementById('section_id');
            let html = '<option value="">No Section</option>';
            if (groupId) {
                sectionsList.filter(sec => sec.group_id == groupId).forEach(sec => {
                    html +=`<option value="${sec.id}" ${selectedSectionId == sec.id ? 'selected' : ''}>${sec.section_name}</option>`;
                });
            }
            s.innerHTML = html;
            loadSessions();
        }

        async function loadSessions(selectedSessionId = null) {
            const classId = document.getElementById('class_id').value;
            const groupId = document.getElementById('group_id').value;
            const sectionId = document.getElementById('section_id').value;
            const s = document.getElementById('session_id');

            if (!classId) {
                s.innerHTML = '<option value="">Select Session</option>';
                return;
            }

            try {
                const res = await api.get('/api/get-school-sessions', {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });
                const sessions = res.data.data || [];
                let html = '<option value="">Select Session</option>';
                sessions.forEach(sess => {
                    html +=`<option value="${sess.id}" ${selectedSessionId == sess.id ? 'selected' : ''}>${sess.session_year}</option>`;
                });
                s.innerHTML = html;
                fetchStudents();
            } catch (e) {
                console.error("Session load failed", e);
            }
        }

        // --- Table & Form Submissions ---

        function fetchFeeTypes(page = 1) {
            currentPage = page;
            const search = document.getElementById('feeSearch').value;
            const filters = {
                class_id: document.getElementById('classFilter').value,
                group_id: document.getElementById('groupFilter').value,
                section_id: document.getElementById('sectionFilter').value,
                session_id: document.getElementById('sessionFilter').value,
                search: search,
                page: page
            };

            // Fetch fee types and all discounts in parallel
            Promise.all([
                api.get('/api/fee-types', { params: filters }),
                api.get('/api/fee-discounts', { params: { all: true } })
            ]).then(([feeRes, discountRes]) => {
                const meta = feeRes.data;
                const fees = feeRes.data.data || [];
                const discounts = discountRes.data.data || [];

                // Build a lookup: "student_id|fee_type_name|fee_name" => discount record
                const discountMap = {};
                discounts.forEach(d => {
                    const key = `${d.student_id}|${d.fee_type?.fee_type_name || ''}|${d.fee_name || ''}`;
                    discountMap[key] = d;
                });

                const tbody = document.getElementById('feeTableBody');

                if (fees.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="13" class="text-center py-6 text-gray-400">No records found.</td></tr>';
                    renderPagination(meta);
                    return;
                }

                let html = '';
                fees.forEach((item, index) => {
                    let detailName = 'N/A';
                    if (['Admission', 'Monthly', 'Others'].includes(item.fee_type_name)) {
                        detailName = item.fee_name || 'N/A';
                    } else if (item.fee_type_name === 'Exams') {
                        detailName = item.school_exam ? item.school_exam.exam_name : (item.fee_name || 'N/A');
                    } else if (item.fee_type_name === 'Boarding Food') {
                        detailName = item.student ? item.student.student_name : (item.fee_name || 'N/A');
                    }

                    // Check if this fee row has a discount for its student
                    const discountKey = `${item.student_id}|${item.fee_type_name}|${item.fee_name || ''}`;
                    const discount = item.student_id ? discountMap[discountKey] : null;

                    // Effective total (after discount if any)
                    const effectiveTotal = discount
                        ? parseFloat(discount.after_discount)
                        : parseFloat(item.amount);

                    // Calculate remaining due from payments
                    const totalPaid = item.total_paid != null ? parseFloat(item.total_paid) : 0;
                    const remainingDue = Math.max(effectiveTotal - totalPaid, 0);

                    let amountCell;
                    if (discount) {
                        amountCell = `
                            <span style="text-decoration: line-through; color: #9ca3af; font-size: 10px; font-weight: 700;">${parseFloat(item.amount).toFixed(2)}</span>
                            <span style="color: #16a34a; font-weight: 600; margin-left: 4px;">${effectiveTotal.toFixed(2)}</span>`;
                    } else {
                        amountCell = `<span>${item.amount}</span>`;
                    }

                    const dueColor = remainingDue > 0 ? '#ef4444' : '#10b981';
                    const dueCell = `<span style="color:${dueColor};font-weight:700;">${remainingDue.toFixed(2)}</span>`;

                    html += `
                <tr class="hover:bg-slate-50 transition-colors text-[11px]">
                    <td>${(meta.from || 0) + index}</td>
                    <td class="text-gray-700">${item.school_class?.class_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_group?.group_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_section?.section_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_session?.session_year || 'N/A'}</td>
                    <td class="text-gray-600">${item.student?.student_id_number || 'N/A'}</td>
                    <td class="text-gray-600">${item.student?.student_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.fee_type_name}</td>
                    <td class="text-gray-600">${detailName}</td>
                    <td class="text-gray-700">${amountCell}</td>
                    <td>${dueCell}</td>
                    <td class="text-gray-500">${formatDate(item.pay_date)}</td>
                    <td>
                        <div class="flex justify-center gap-3">
                            <button onclick="editFee(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteFee(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
                });
                tbody.innerHTML = html;
                renderPagination(meta);
            }).catch(() => {
                document.getElementById('feeTableBody').innerHTML = '<tr><td colspan="13" class="text-center py-6 text-red-400">Error loading data.</td></tr>';
            });
        }

        function handleFeeTypeChange() {
            const type = document.getElementById('fee_type_name').value;
            const fields = ['div_fee_name', 'div_exam_name', 'div_student_name', 'div_amount', 'div_pay_date'];
            fields.forEach(f => {
                const el = document.getElementById(f);
                if (el) el.style.display = 'none';
            });

            if (!type) return;
            document.getElementById('div_amount').style.display = 'block';
            document.getElementById('div_pay_date').style.display = 'block';

            if (['Admission', 'Monthly', 'Others'].includes(type)) {
                document.getElementById('div_fee_name').style.display = 'block';
            } else if (type === 'Exams') {
                document.getElementById('div_exam_name').style.display = 'block';
                loadFilteredExams();
            } else if (type === 'Boarding Food') {
                document.getElementById('div_student_name').style.display = 'block';
                fetchStudents();
            }
        }

        // Load exams filtered by the currently selected class/group/section/session
        async function loadFilteredExams(selectedExamId = null) {
            const classId   = document.getElementById('class_id').value;
            const groupId   = document.getElementById('group_id').value;
            const sectionId = document.getElementById('section_id').value;
            const sessionId = document.getElementById('session_id').value;

            const examSelect = document.getElementById('exam_id');
            examSelect.innerHTML = '<option value="">Loading...</option>';

            // Resolve names from the preloaded lists
            const classObj   = classesList.find(c => c.id == classId);
            const groupObj   = groupsList.find(g => g.id == groupId);
            const sectionObj = sectionsList.find(s => s.id == sectionId);

            // Session year needs to come from the session dropdown text
            const sessionOption = document.querySelector(`#session_id option[value="${sessionId}"]`);
            const sessionName   = sessionOption ? sessionOption.textContent.trim() : '';

            const params = {};
            if (classObj)   params.class_name   = classObj.class_name;
            if (groupObj)   params.group_name   = groupObj.group_name;
            if (sectionObj) params.section_name = sectionObj.section_name;
            if (sessionName) params.session_name = sessionName;

            try {
                const res = await api.get('/api/get-school-exams', { params });
                const exams = res.data.data || [];

                let html = '<option value="">Select Exam</option>';
                if (exams.length === 0) {
                    html = '<option value="">No exams found for this class/session</option>';
                } else {
                    exams.forEach(ex => {
                        html += `<option value="${ex.id}" ${selectedExamId == ex.id ? 'selected' : ''}>${ex.exam_name}</option>`;
                    });
                }
                examSelect.innerHTML = html;
            } catch (e) {
                console.error('Exam load failed', e);
                examSelect.innerHTML = '<option value="">Failed to load exams</option>';
            }
        }

        document.getElementById('feeForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('fee_id').value;
            const saveBtn = document.getElementById('saveBtn');
            const targetClassId = document.getElementById('class_id').value;

            const data = {
                class_id: targetClassId,
                group_id: document.getElementById('group_id').value,
                section_id: document.getElementById('section_id').value,
                session_id: document.getElementById('session_id').value,
                fee_type_name: document.getElementById('fee_type_name').value,
                fee_name: document.getElementById('fee_name_input').value,
                exam_id: document.getElementById('exam_id').value,
                student_id: document.getElementById('student_id').value,
                amount: document.getElementById('amount').value,
                pay_date: document.getElementById('pay_date').value,
            };

            saveBtn.disabled = true;
            const req = id ? api.put(`/api/fee-types/${id}`, data) : api.post('/api/fee-types', data);

            req.then(() => {
                Toastify({
                    text: "Data Saved Successfully",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeFeeModal();
                document.getElementById('classFilter').value = targetClassId;
                handleFilterClassChange(false).then(() => fetchFeeTypes(1));
            }).catch(err => {
                Swal.fire('Error', err.response?.data?.message || 'Save failed.', 'error');
            }).finally(() => saveBtn.disabled = false);
        };

        async function editFee(id) {
            try {
                const res = await api.get(`/api/fee-types/${id}`);
                const item = res.data;
                document.getElementById('feeForm').reset();
                document.getElementById('fee_id').value = item.id;
                document.getElementById('modalTitle').innerText = 'Edit Fee Configuration';
                document.getElementById('class_id').value = item.class_id || '';
                document.getElementById('fee_type_name').value = item.fee_type_name || '';
                document.getElementById('amount').value = item.amount || '';
                document.getElementById('pay_date').value = item.pay_date || '';
                document.getElementById('fee_name_input').value = item.fee_name || '';

                loadGroups(item.group_id);
                loadSections(item.section_id);
                await loadSessions(item.session_id);

                // For Exams type, load filtered exams first, then restore the selected exam
                if (item.fee_type_name === 'Exams') {
                    // Show the exam field manually before loading
                    ['div_fee_name', 'div_exam_name', 'div_student_name', 'div_amount', 'div_pay_date'].forEach(f => {
                        const el = document.getElementById(f);
                        if (el) el.style.display = 'none';
                    });
                    document.getElementById('div_exam_name').style.display = 'block';
                    document.getElementById('div_amount').style.display = 'block';
                    document.getElementById('div_pay_date').style.display = 'block';
                    await loadFilteredExams(item.exam_id);
                } else {
                    handleFeeTypeChange();
                }

                if (item.fee_type_name === 'Boarding Food') {
                    document.getElementById('student_id').value = item.student_id || '';
                    fetchStudents(item.student_id);
                }

                document.getElementById('feeModal').classList.remove('hidden');
            } catch (e) {
                Swal.fire('Error', 'Failed to fetch record.', 'error');
            }
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const [year, month, day] = dateString.split('-');
            return (year && month && day) ? `${day}/${month}/${year}` : dateString;
        }

        function deleteFee(id) {
            Swal.fire({
                title: 'Delete this fee?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/api/fee-types/${id}`).then(() => {
                        Toastify({
                            text: "Deleted Successfully",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchFeeTypes(currentPage);
                    });
                }
            });
        }

        function renderPagination(meta) {
            const info = document.getElementById('paginationInfo');
            const controls = document.getElementById('paginationControls');
            info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
            controls.innerHTML = '';
            if (!meta.total || meta.total === 0) return;

            const createBtn = (content, page, active = false, disabled = false) => {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${active ? 'active' : ''}`;
                btn.innerHTML = content;
                if (disabled) btn.disabled = true;
                if (!disabled && !active) btn.onclick = () => fetchFeeTypes(page);
                return btn;
            };

            const btnGroup = document.createElement('div');
            btnGroup.className = 'flex items-center gap-1';
            btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-left text-lg"></i>', meta.current_page - 1, false,meta.current_page === 1));

            let startPage = Math.max(1, meta.current_page - 2);
            let endPage = Math.min(meta.last_page, startPage + 4);
            if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

            for (let i = startPage; i <= endPage; i++) {
                btnGroup.appendChild(createBtn(i, i, i === meta.current_page));
            }

            btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-right text-lg"></i>', meta.current_page + 1, false,
                meta.current_page === meta.last_page));
            controls.appendChild(btnGroup);
        }

        function openFeeModal() {
            document.getElementById('feeForm').reset();
            document.getElementById('fee_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add New Fee';
            document.getElementById('group_id').innerHTML = '<option value="">No Group</option>';
            document.getElementById('section_id').innerHTML = '<option value="">No Section</option>';
            document.getElementById('session_id').innerHTML = '<option value="">Select Session</option>';
            handleFeeTypeChange();
            document.getElementById('feeModal').classList.remove('hidden');
        }

        function closeFeeModal() {
            document.getElementById('feeModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnFilter').addEventListener('click', () => document.getElementById(
                'filterModal').classList.remove('hidden'));
            document.getElementById('applyFilter').addEventListener('click', () => {
                fetchFeeTypes(1);
                document.getElementById('filterModal').classList.add('hidden');
            });
            document.getElementById('resetFilter').addEventListener('click', () => {
                document.getElementById('classFilter').value = '';
                document.getElementById('groupFilter').innerHTML = '<option value="">All Groups</option>';
                document.getElementById('sectionFilter').innerHTML =
                    '<option value="">All Sections</option>';
                document.getElementById('sessionFilter').innerHTML =
                    '<option value="">All Sessions</option>';
                fetchFeeTypes(1);
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.getElementById('btnExport').addEventListener('click', () => document.getElementById('exportModal').classList.remove('hidden'));
            document.getElementById('closeExport').addEventListener('click', () => document.getElementById('exportModal').classList.add('hidden'));

            document.getElementById('classFilter').addEventListener('change', () => handleFilterClassChange(false));
            document.getElementById('groupFilter').addEventListener('change', () => handleFilterGroupChange(false));
            document.getElementById('sectionFilter').addEventListener('change', () => handleFilterSectionChange(false));

            document.getElementById('class_id').addEventListener('change', () => loadGroups());
            document.getElementById('group_id').addEventListener('change', () => loadSections());
            document.getElementById('section_id').addEventListener('change', () => loadSessions());
            document.getElementById('session_id').addEventListener('change', () => fetchStudents());
            document.getElementById('fee_type_name').addEventListener('change', () => handleFeeTypeChange());

            document.getElementById('feeSearch').addEventListener('input', () => fetchFeeTypes(1));

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
            };
        });

        preloadData();
    </script>
@endsection
