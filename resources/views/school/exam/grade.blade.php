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
                text-transform: capitalize !important;
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
            padding: 0px;
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
                            <input type="text" id="searchInput" onkeyup="handleSearch(this)"
                                placeholder="Search Grades..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="openFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openGradeModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Grade
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInputMobile"
                        onkeyup="document.getElementById('searchInput').value = this.value; handleSearch(document.getElementById('searchInput'))"
                        placeholder="Search Grades..."
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
                            Grade filter
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
                                <select id="filter_section" onchange="handleCascade(this, 'filter', 'subject')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Subject Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Subject</label>
                            <div class="relative">
                                <select id="filter_subject"
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
                        <button onclick="exportData('pdf')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button onclick="exportData('excel')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button onclick="window.print()"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button id="closeExport" onclick="document.getElementById('exportModal').classList.add('hidden')"
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
                                <th width="50">SL</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th class="text-center">Min Mark</th>
                                <th class="text-center">Max Mark</th>
                                <th class="text-center">Point No</th>
                                <th class="text-center">Letter</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="gradeTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>


    {{-- Main Grade Modal --}}
    <div id="gradeModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'gradeModal')">

        <div class="bg-white w-full max-w-4xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()">

            {{-- Header - Centered, No Badge, No Cross --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <span id="stepIndicator" class="hidden">STEP 1/2</span>
                    <h3 id="modalTitle"
                        class="text-gray-800 text-[13px] font-medium leading-tight capitalize tracking-normal text-center">
                        Add Exam Grade
                    </h3>
                </div>
            </div>

            <form id="gradeForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="edit_id">

                {{-- Body --}}
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">

                    {{-- STEP 1: Selection --}}
                    <div id="step1" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                                <select id="class_name" name="class_name"
                                    onchange="handleCascade(this, 'modal', 'group')"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] focus:border-blue-600 outline-none transition-colors"
                                    required style="border-radius: 0;"></select>
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                                <select id="group_name" name="group_name"
                                    onchange="handleCascade(this, 'modal', 'section')"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] focus:border-blue-600 outline-none transition-colors"
                                    style="border-radius: 0;"></select>
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                                <select id="section_name" name="section_name"
                                    onchange="handleCascade(this, 'modal', 'subject')"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] focus:border-blue-600 outline-none transition-colors"
                                    style="border-radius: 0;"></select>
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Subject</label>
                                <select id="subject_name" name="subject_name"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] focus:border-blue-600 outline-none transition-colors"
                                    required style="border-radius: 0;"></select>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Grade Entry --}}
                    <div id="step2" class="hidden">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                            <h4 class="text-[11px] font-medium text-blue-600 capitalize tracking-normal">Grade Distribution
                            </h4>
                        </div>

                        {{-- Dynamic Rows Header - Point No integrated --}}
                        <div class="grid grid-cols-9 gap-2 mb-2 px-1 hidden sm:grid">
                            <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Min Mark</div>
                            <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Max Mark</div>
                            <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Point No</div>
                            <div class="col-span-2 text-[9px] capitalize text-gray-400 font-normal">Letter</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div id="gradeRowsContainer" class="space-y-3">
                            {{-- Rows injected via JS --}}
                            {{-- Note: In your addGradeRow() JS, update the template to use 4 main columns + delete button --}}
                        </div>
                        <div class="mt-2 flex justify-end pr-5 sm:pr-3">
                            <button type="button" onclick="addGradeRow()"
                                class="text-blue-600 hover:text-blue-800 transition-all p-1">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row justify-end gap-2 sticky bottom-0">

                    <button type="button" onclick="closeGradeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] border border-gray-200 text-gray-600 text-[10px] tracking-normal capitalize flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-all"
                        style="border-radius: 0;">
                        Cancel
                    </button>

                    <button type="button" id="backBtn" onclick="toggleStep(1)"
                        class="hidden w-1/2 sm:w-auto sm:px-8 h-[32px] border border-gray-200 text-gray-600 text-[10px] tracking-normal capitalize flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-all"
                        style="border-radius: 0;">
                        Back
                    </button>

                    <button type="button" id="nextBtn" onclick="validateStep1()"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] border border-gray-200 text-gray-800 text-[10px] tracking-normal capitalize flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-all"
                        style="border-radius: 0;">
                        Next
                    </button>

                    <button type="submit" id="saveBtn"
                        class="hidden w-1/2 sm:w-auto sm:px-12 h-[32px] border border-gray-200 text-gray-800 text-[10px] tracking-normal capitalize flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-all"
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
        let searchTimer;
        let currentStep = 1;

        // --- Core UI Logic ---

        function toggleStep(step) {
            currentStep = step;
            const s1 = document.getElementById('step1');
            const s2 = document.getElementById('step2');
            const nextBtn = document.getElementById('nextBtn');
            const backBtn = document.getElementById('backBtn');
            const saveBtn = document.getElementById('saveBtn');
            const indicator = document.getElementById('stepIndicator');

            if (step === 1) {
                s1.classList.remove('hidden');
                s2.classList.add('hidden');
                nextBtn.classList.remove('hidden');
                backBtn.classList.add('hidden');
                saveBtn.classList.add('hidden');
                indicator.innerText = "STEP 1/2";
            } else {
                s1.classList.add('hidden');
                s2.classList.remove('hidden');
                nextBtn.classList.add('hidden');
                backBtn.classList.remove('hidden');
                saveBtn.classList.remove('hidden');
                indicator.innerText = "STEP 2/2";

                // Ensure at least one row exists
                if (document.getElementById('gradeRowsContainer').children.length === 0) {
                    addGradeRow();
                }
            }
        }

        function validateStep1() {
            const cls = document.getElementById('class_name').value;
            const sub = document.getElementById('subject_name').value;
            if (!cls || !sub) {
                Swal.fire('Required', 'Please select Class and Subject first', 'warning');
                return;
            }
            toggleStep(2);
        }

        function addGradeRow(data = null) {
            const container = document.getElementById('gradeRowsContainer');
            const rowId = Date.now() + Math.random();
            const rowHtml = `
        <div class="grid grid-cols-1 sm:grid-cols-9 gap-2 items-center bg-white p-3 border border-gray-100 shadow-sm grade-row" id="row_${rowId}">
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Min Mark</label>
                <input type="number" name="min_mark" value="${data?.min_mark || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" required style="border-radius: 0;">
            </div>
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Max Mark</label>
                <input type="number" name="max_mark" value="${data?.max_mark || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" required style="border-radius: 0;">
            </div>           
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Point No</label>
                <input type="number" step="0.01" name="number_point" value="${data?.number_point || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" required style="border-radius: 0;">
            </div>
            <div class="col-span-2">
                <label class="sm:hidden text-[9px] text-gray-400">Letter</label>
                <input type="text" name="letter_name" value="${data?.letter_name || ''}" class="w-full border border-gray-200 py-1 px-2 text-[11px] h-[30px]" required style="border-radius: 0;">
            </div>
            <div class="col-span-1 flex justify-end">
                <button type="button" onclick="document.getElementById('row_${rowId}').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', rowHtml);
        }

        // --- API & Data Logic ---

        function loadInitialClasses() {
            return axios.get('/api/get-school-classes').then(res => {
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

        async function handleCascade(element, context, nextLevel) {
            const selectedOption = element.options[element.selectedIndex];
            const parentId = selectedOption ? selectedOption.getAttribute('data-id') : null;
            const targets = {
                group: context === 'modal' ? 'group_name' : 'filter_group',
                section: context === 'modal' ? 'section_name' : 'filter_section',
                subject: context === 'modal' ? 'subject_name' : 'filter_subject'
            };

            if (nextLevel === 'group') {
                resetSelect(targets.group, context === 'modal' ? 'Group' : 'Groups');
                resetSelect(targets.section, context === 'modal' ? 'Section' : 'Sections');
                resetSelect(targets.subject, context === 'modal' ? 'Subject' : 'Subjects');
                if (parentId) await fetchAndFill(`/api/get-school-groups?class_id=${parentId}`, targets.group,
                    context === 'modal' ? 'Group' : 'Groups', 'group_name');
            } else if (nextLevel === 'section') {
                resetSelect(targets.section, context === 'modal' ? 'Section' : 'Sections');
                resetSelect(targets.subject, context === 'modal' ? 'Subject' : 'Subjects');
                if (parentId) await fetchAndFill(`/api/get-school-sections?group_id=${parentId}`, targets.section,
                    context === 'modal' ? 'Section' : 'Sections', 'section_name');
            } else if (nextLevel === 'subject') {
                resetSelect(targets.subject, context === 'modal' ? 'Subject' : 'Subjects');
                if (parentId) await fetchAndFill(`/api/get-school-subjects?section_id=${parentId}`, targets.subject,
                    context === 'modal' ? 'Subject' : 'Subjects', 'subject_name');
            }
        }

        function fetchAndFill(url, elementId, label, textField) {
            return axios.get(url).then(res => {
                let opts = `<option value="">Select ${label}</option>`;
                if (elementId.includes('filter')) opts = `<option value="">All ${label}</option>`;
                res.data.data.forEach(item => {
                    opts +=
                        `<option value="${item[textField]}" data-id="${item.id}">${item[textField]}</option>`;
                });
                document.getElementById(elementId).innerHTML = opts;
            });
        }

        function resetSelect(id, label) {
            const el = document.getElementById(id);
            if (el) {
                const prefix = id.includes('filter') ? 'All' : 'Select';
                el.innerHTML = `<option value="">${prefix} ${label}</option>`;
            }
        }

        function fetchGrades(page = 1) {
            currentPage = page;
            const params = {
                page,
                search: document.getElementById('searchInput').value,
                class_name: document.getElementById('filter_class').value,
                group_name: document.getElementById('filter_group').value,
                section_name: document.getElementById('filter_section').value,
                subject_name: document.getElementById('filter_subject').value
            };

            axios.get('/api/school-exam-grades', {
                params
            }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('gradeTableBody');
                tbody.innerHTML = '';

                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="11" class="text-center py-10 text-gray-400 uppercase font-black tracking-widest">No records found</td></tr>`;
                    document.getElementById('paginationInfo').innerText = `0 of 0`;
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                meta.data.forEach((item, i) => {
                    tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="text-gray-400 text-[11px]">${meta.from + i}</td>
                <td class="text-gray-700 font-normal">${item.class_name}</td>
                <td class="text-gray-500">${item.group_name || '—'}</td>
                <td>
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 text-[10px] font-normal">
                        ${item.section_name || 'Global'}
                    </span>
                </td>
                <td class="text-gray-700 font-normal">${item.subject_name}</td>
                <td class="text-center text-gray-600 font-normal">${item.min_mark}</td>
                <td class="text-center text-gray-600 font-normal">${item.max_mark}</td>                  
                <td class="text-center text-gray-600 font-normal">${item.number_point}</td>
                <td class="text-center">
                    <span class="text-gray-700 font-normal tracking-normal">${item.letter_name}</span>
                </td>
                <td>
                    <div class="flex justify-center gap-3">
                        <button onclick="editGrade(${item.id})" class="action-icon-btn text-blue-500 hover:text-blue-700 transition-colors">
                            <i class="far fa-edit" style="font-size: 15px;"></i>
                        </button>
                        <button onclick="deleteGrade(${item.id})" class="action-icon-btn text-red-400 hover:text-red-600 transition-colors">
                            <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
                });
                renderPagination(meta);
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            if (!meta.data || meta.total === 0) return;
            const btnClass = "pagination-btn";

            controls.innerHTML +=
                `<button class="${btnClass}" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchGrades(${meta.current_page - 1})"><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    controls.innerHTML +=
                        `<button class="${btnClass} ${meta.current_page === i ? 'active' : ''}" onclick="fetchGrades(${i})">${i}</button>`;
                } else if (i === meta.current_page - 2 || i === meta.current_page + 2) {
                    controls.innerHTML += `<span class="px-2 text-gray-400">...</span>`;
                }
            }
            controls.innerHTML +=
                `<button class="${btnClass}" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchGrades(${meta.current_page + 1})"><i class="mdi mdi-chevron-right"></i></button>`;
        }

        document.getElementById('gradeForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;

            // Collect grades array from rows
            const gradeRows = document.querySelectorAll('.grade-row');
            const grades = Array.from(gradeRows).map(row => ({
                min_mark: row.querySelector('[name="min_mark"]').value,
                max_mark: row.querySelector('[name="max_mark"]').value,
                number_point: row.querySelector('[name="number_point"]').value,
                letter_name: row.querySelector('[name="letter_name"]').value,
            }));

            const data = {
                class_name: document.getElementById('class_name').value,
                group_name: document.getElementById('group_name').value,
                section_name: document.getElementById('section_name').value,
                subject_name: document.getElementById('subject_name').value,
                grades: grades // Passing full array for 'store'
            };

            const req = id ? axios.put(`/api/school-exam-grades/${id}`, data) : axios.post('/api/school-exam-grades',
                data);

            req.then(() => {
                Toastify({
                    text: "Action Successful!",
                    style: {
                        background: "#2563eb"
                    }
                }).showToast();
                closeGradeModal();
                fetchGrades(currentPage);
            }).catch(err => {
                if (err.response?.status === 422) Swal.fire('Wait!', err.response.data.message, 'warning');
                else Swal.fire('Error', 'Something went wrong', 'error');
            });
        };

        async function editGrade(id) {
            try {
                const res = await axios.get(`/api/school-exam-grades/${id}`);
                const d = res.data;

                document.getElementById('edit_id').value = d.id;
                document.getElementById('modalTitle').innerText = "Edit Grade Configuration";
                document.getElementById('gradeRowsContainer').innerHTML = '';

                // Add single row for edit
                addGradeRow(d);

                // Cascade Logic
                const classSelect = document.getElementById('class_name');
                classSelect.value = d.class_name;
                await handleCascade(classSelect, 'modal', 'group');

                const groupSelect = document.getElementById('group_name');
                if (d.group_name) groupSelect.value = d.group_name;
                await handleCascade(groupSelect, 'modal', 'section');

                const sectionSelect = document.getElementById('section_name');
                if (d.section_name) sectionSelect.value = d.section_name;
                await handleCascade(sectionSelect, 'modal', 'subject');

                const subjectSelect = document.getElementById('subject_name');
                if (d.subject_name) subjectSelect.value = d.subject_name;

                document.getElementById('gradeModal').classList.remove('hidden');
                toggleStep(1); // Start on step 1 to allow context change
            } catch (error) {
                Swal.fire('Error', 'Failed to fetch record data', 'error');
            }
        }

        function deleteGrade(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This grade configuration will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'YES, DELETE'
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-exam-grades/${id}`).then(() => fetchGrades(
                    currentPage));
            });
        }

        function openGradeModal() {
            document.getElementById('gradeForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = "Add Exam Grade";
            document.getElementById('gradeRowsContainer').innerHTML = '';

            // Reset cascaded dropdowns
            document.getElementById('group_name').innerHTML = '<option value="">Select Group</option>';
            document.getElementById('section_name').innerHTML = '<option value="">Select Section</option>';
            document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';

            toggleStep(1);
            document.getElementById('gradeModal').classList.remove('hidden');
        }

        function closeGradeModal() {
            document.getElementById('gradeModal').classList.add('hidden');
        }

        function handleSearch(input) {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchGrades(1), 500);
        }

        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        function closeOnOutsideClick(e, id) {
            if (e.target.id === id) id === 'filterModal' ? closeFilterModal() : closeGradeModal();
        }

        function resetFilters() {
            document.getElementById('filter_class').value = "";
            document.getElementById('filter_group').innerHTML = '<option value="">All Groups</option>';
            document.getElementById('filter_section').innerHTML = '<option value="">All Sections</option>';
            document.getElementById('filter_subject').innerHTML = '<option value="">All Subjects</option>';
            document.getElementById('searchInput').value = "";
            closeFilterModal();
            fetchGrades(1);
        }

        function applyFilters() {
            closeFilterModal();
            fetchGrades(1);
        }

        loadInitialClasses().then(() => fetchGrades());
    </script>
@endsection
