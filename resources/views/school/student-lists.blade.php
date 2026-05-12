@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        /* Global & Table Styles */
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
            padding: 1rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }

        .table-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 0px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        /* ================= Table Container ================= */
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 0 !important;
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
            border-radius: 3px !important;
        }

        /* ================= Table Core ================= */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            border: 1px solid #e5e7eb !important;
            font-size: 13px !important;
        }

        /* ================= Table Header ================= */
        th {
            padding: 12px 16px !important;
            background: #f8fafc !important;
            color: #374151 !important;
            font-weight: 600 !important;
            vertical-align: middle !important;
            text-align: left !important;
            text-transform: none !important;
            border-bottom: 2px solid #e5e7eb !important;
            border-right: 1px solid #e5e7eb !important;
            white-space: nowrap !important;
        }

        th:last-child {
            border-right: none !important;
            text-align: center !important;
        }

        /* ================= Table Body ================= */
        tr {
            border-bottom: 1px solid #e5e7eb !important;
        }

        tr:last-child {
            border-bottom: none !important;
        }

        tr:hover {
            background: #f9fafb !important;
        }

        td {
            padding: 12px 16px !important;
            vertical-align: middle !important;
            border-right: 1px solid #e5e7eb !important;
            font-size: 13px !important;
            color: #374151 !important;
            background: transparent !important;
            white-space: nowrap !important;
        }

        td:first-child {
            border-left: none !important;
        }

        td:last-child {
            border-right: none !important;
            text-align: center !important;
        }

        /* Photo Styling */
        .student-photo {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 1px solid #e5e7eb !important;
        }

        /* Table Photo - Force Circular Shape */
        .table-photo {
            border-radius: 50% !important;
            width: 32px !important;
            height: 32px !important;
            object-fit: cover !important;
            border: 1px solid #e5e7eb !important;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }

        @media (min-width: 640px) {
            .table-photo {
                width: 40px !important;
                height: 40px !important;
            }
        }

        /* Student Name Styling */
        .student-name {
            font-weight: 600 !important;
            color: #111827 !important;
        }

        /* ID Number Styling */
        .student-id {
            font-family: 'Courier New', monospace !important;
            font-weight: 600 !important;
            color: #4b5563 !important;
        }

        /* Cell Data Styling */
        .cell-data {
            font-weight: 400 !important;
            color: #4b5563 !important;
        }

        /* Action Buttons Container */
        .action-buttons {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* Action Buttons */
        .btn-action {
            width: 32px !important;
            height: 32px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            border: 1px solid #e5e7eb !important;
            background: #ffffff !important;
            color: #4b5563 !important;
        }

        .btn-action:hover {
            background: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-btn {
            padding: 6px 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #374151;
            border-color: #374151;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .table-card {
                border-radius: 0px;
            }

            th {
                padding: 10px 12px !important;
                font-size: 11px !important;
            }

            td {
                padding: 10px 12px !important;
                font-size: 12px !important;
            }

            .student-photo {
                width: 32px !important;
                height: 32px !important;
            }

            .student-name {
                font-size: 12px !important;
            }

            .student-id {
                font-size: 11px !important;
            }

            .btn-action {
                width: 28px !important;
                height: 28px !important;
            }

            .btn-action i {
                font-size: 13px !important;
            }

            .pagination-container {
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.75rem;
            }

            .pagination-btn {
                padding: 5px 10px;
                font-size: 12px;
            }

            /* Force circular photos on mobile */
            td img {
                border-radius: 50% !important;
                width: 32px !important;
                height: 32px !important;
                object-fit: cover !important;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 8px 10px !important;
            }

            .student-name {
                font-size: 11px !important;
            }

            .action-buttons {
                gap: 4px !important;
            }

            .btn-action {
                width: 26px !important;
                height: 26px !important;
            }

            .btn-action i {
                font-size: 12px !important;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 14px;
            color: #9ca3af;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-premium:hover::before {
            opacity: 1;
        }

        .btn-outline-premium:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(100, 116, 139, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-secondary:hover::before {
            opacity: 1;
        }

        .btn-outline-secondary:active {
            transform: translateY(0) scale(0.98);
        }

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #edf2f7;
        }

        .pagination-btn {
            padding: 5px 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all .2s;
            cursor: pointer;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .pagination-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
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
            display: block;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
            box-shadow: none;
        }

        .edit-field-disabled {
            background: #f9fafb !important;
            color: #6b7280 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            transition: all .2s;
            background: none;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        .premium-modal {
            backdrop-filter: blur(4px);
        }

        .modal-content-sharp {
            border-radius: 0 !important;
        }

        .image-preview-box {
            width: 45px;
            height: 45px;
            border: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8fafc;
        }

        .image-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Status Badges */
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
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
                            <input type="text" id="studentSearch" placeholder="Search students..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            {{-- this icon hide only on mobile --}}
                            <i class="fa-solid fa-filter hidden sm:block"></i> Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            <i class="fa-solid fa-download hidden sm:block"></i> Export
                        </button>

                        <a href="{{ route('school.student-admission') }}"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            <i class="fa-solid fa-user-plus hidden sm:block"></i> Add Student
                        </a>

                        <button id="btnBulkUpload"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            <i class="fa-solid fa-file-import hidden sm:block"></i> Bulk Upload
                        </button>
                    </div>
                </div>

                {{-- Mobile Search --}}
                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="studentSearchMobile" placeholder="Search students..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Student Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-6">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Student Filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 flex flex-col gap-3">
                        {{-- Class --}}
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Classes</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group --}}
                        <div>
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

                        {{-- Section --}}
                        <div>
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

                        {{-- Session --}}
                        <div>
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

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl">
                    <div class="flex flex-col gap-1.5">
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">PDF</button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">EXCEL</button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">PRINT</button>
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">Cancel</button>
                    </div>
                </div>
            </div>

            {{-- Bulk Upload Modal --}}
            <div id="bulkUploadModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-6">
                <div class="bg-white w-full max-w-[380px] modal-content-sharp shadow-2xl" style="border-radius: 0;">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <span class="text-[11px] font-semibold text-gray-700 tracking-widest uppercase">Bulk Student Upload</span>
                        <button id="btnBulkClose" class="text-gray-400 hover:text-gray-600 text-sm leading-none">&times;</button>
                    </div>

                    <div class="p-4">
                        {{-- State 1: Selector --}}
                        <div id="bulkState_selector">
                            <p class="text-[10px] text-gray-500 mb-3">
                                Select the class combination, download the template, fill in student data, then upload.
                            </p>

                            {{-- Class --}}
                            <div class="mb-2">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Class <span class="text-red-400">*</span></label>
                                <div class="bulk-dd-wrap relative" id="wrap_bulkClass">
                                    <button type="button" class="bulk-dd-trigger w-full border border-gray-200 px-2 h-[32px] bg-white flex items-center justify-between focus:outline-none" style="border-radius:0;">
                                        <span class="bulk-dd-label text-[11px] text-gray-400">Select Class</span>
                                        <i class="fas fa-chevron-down text-[9px] text-gray-400 flex-shrink-0"></i>
                                    </button>
                                    <ul class="bulk-dd-list hidden absolute left-0 right-0 top-full bg-white border border-gray-200 overflow-y-auto shadow-md" style="border-radius:0; z-index:200; max-height:150px; border-top:none;"></ul>
                                </div>
                            </div>

                            {{-- Group --}}
                            <div class="mb-2">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Group <span class="text-gray-300">(optional)</span></label>
                                <div class="bulk-dd-wrap relative" id="wrap_bulkGroup">
                                    <button type="button" class="bulk-dd-trigger w-full border border-gray-200 px-2 h-[32px] bg-white flex items-center justify-between focus:outline-none" style="border-radius:0;">
                                        <span class="bulk-dd-label text-[11px] text-gray-400">No Group / All Groups</span>
                                        <i class="fas fa-chevron-down text-[9px] text-gray-400 flex-shrink-0"></i>
                                    </button>
                                    <ul class="bulk-dd-list hidden absolute left-0 right-0 top-full bg-white border border-gray-200 overflow-y-auto shadow-md" style="border-radius:0; z-index:200; max-height:150px; border-top:none;"></ul>
                                </div>
                            </div>

                            {{-- Section --}}
                            <div class="mb-2">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Section <span class="text-red-400">*</span></label>
                                <div class="bulk-dd-wrap relative" id="wrap_bulkSection">
                                    <button type="button" class="bulk-dd-trigger w-full border border-gray-200 px-2 h-[32px] bg-white flex items-center justify-between focus:outline-none" style="border-radius:0;">
                                        <span class="bulk-dd-label text-[11px] text-gray-400">Select Section</span>
                                        <i class="fas fa-chevron-down text-[9px] text-gray-400 flex-shrink-0"></i>
                                    </button>
                                    <ul class="bulk-dd-list hidden absolute left-0 right-0 top-full bg-white border border-gray-200 overflow-y-auto shadow-md" style="border-radius:0; z-index:200; max-height:150px; border-top:none;"></ul>
                                </div>
                            </div>

                            {{-- Session --}}
                            <div class="mb-3">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Session <span class="text-red-400">*</span></label>
                                <div class="bulk-dd-wrap relative" id="wrap_bulkSession">
                                    <button type="button" class="bulk-dd-trigger w-full border border-gray-200 px-2 h-[32px] bg-white flex items-center justify-between focus:outline-none" style="border-radius:0;">
                                        <span class="bulk-dd-label text-[11px] text-gray-400">Select Session</span>
                                        <i class="fas fa-chevron-down text-[9px] text-gray-400 flex-shrink-0"></i>
                                    </button>
                                    <ul class="bulk-dd-list hidden absolute left-0 right-0 top-full bg-white border border-gray-200 overflow-y-auto shadow-md" style="border-radius:0; z-index:200; max-height:150px; border-top:none;"></ul>
                                </div>
                            </div>

                            <p id="bulkSelectorError" class="text-[10px] text-red-500 mb-2 hidden"></p>

                            <button id="btnDownloadTemplate"
                                class="btn-outline-secondary border border-gray-200 w-full h-[32px] text-[11px] mb-2 flex items-center justify-center gap-1.5 tracking-wider disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                                <i class="fa-solid fa-download text-[10px]"></i> Download Template
                            </button>

                            <button id="btnGoToUpload"
                                class="btn-outline-premium border border-gray-200 w-full h-[32px] text-[11px] flex items-center justify-center gap-1.5 tracking-wider disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                                Next: Upload File <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>

                        {{-- State 2: Upload --}}
                        <div id="bulkState_upload" class="hidden">
                            <div id="bulkComboBanner" class="text-[10px] text-blue-700 bg-blue-50 border border-blue-100 px-3 py-2 mb-3 leading-relaxed"></div>
                            <div class="mb-3">
                                <input type="file" id="bulkFileInput" accept=".xlsx,.xls"
                                    class="form-input-fixed text-[10px] file:mr-3 file:py-1 file:px-3 file:border file:border-gray-100 file:text-[9px] file:bg-gray-50 file:text-gray-600 border border-gray-200 w-full h-[32px] flex items-center"
                                    style="border-radius: 0;" />
                            </div>
                            <p id="bulkUploadError" class="text-[10px] text-red-500 mb-2 hidden"></p>
                            <div class="flex gap-2">
                                <button id="btnBackToSelector"
                                    class="btn-outline-secondary border border-gray-200 h-[32px] px-4 text-[11px] flex items-center justify-center gap-1 tracking-wider">
                                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Back
                                </button>
                                <button id="btnUploadBulk"
                                    class="btn-outline-premium border border-gray-200 flex-1 h-[32px] text-[11px] flex items-center justify-center gap-1.5 tracking-wider">
                                    <i class="fa-solid fa-upload text-[10px]"></i> Upload & Import
                                </button>
                            </div>
                        </div>

                        {{-- State 3: Processing --}}
                        <div id="bulkState_processing" class="hidden text-center py-6">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
                            <p class="text-[12px] text-gray-600">Importing students, please wait...</p>
                        </div>

                        {{-- State 4: Result --}}
                        <div id="bulkState_result" class="hidden">
                            <p id="bulkResultText" class="text-[12px] font-medium mb-3"></p>
                            <div id="bulkErrorSection" class="hidden">
                                <p class="text-[10px] font-semibold text-red-600 mb-2">Validation Errors:</p>
                                <div class="overflow-auto max-h-[220px] border border-gray-200">
                                    <table class="w-full text-[10px]">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="px-2 py-1 text-left border-r border-gray-200 whitespace-nowrap">Row</th>
                                                <th class="px-2 py-1 text-left border-r border-gray-200 whitespace-nowrap">Field</th>
                                                <th class="px-2 py-1 text-left">Error</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bulkErrorTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <button id="btnBulkReset"
                                class="btn-outline-secondary border border-gray-200 w-full h-[32px] text-[11px] mt-3 tracking-wider">
                                Upload Another File
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left">Sl</th>
                                <th>Photo</th>
                                <th>ID Number</th>
                                <th class="text-left">Student Name</th>
                                <th class="text-left">Father's name</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>



    {{-- Student Details Modal --}}
    <div id="detailsModal"
        class="premium-modal hidden fixed inset-0 z-[80] flex items-center justify-center bg-black/50 p-4">
        <div
            class="bg-white w-full max-w-5xl max-h-[70vh] lg:max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border border-slate-200">
            <div class="px-6 py-2 lg:py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-md text-slate-700 capitalize">Student Details</h3>
            </div>

            <form id="detailsForm" class="overflow-y-auto p-6 custom-scrollbar">
                <input type="hidden" id="det_student_id">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <div class="space-y-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Academic information</h4>

                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Student id number</label>
                            <input name="student_id_number" class="form-input text-xs bg-gray-50" readonly>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Class</label>
                                <input name="class" class="form-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Section</label>
                                <input name="section" class="form-input text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Group</label>
                                <input name="group" class="form-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Session</label>
                                <input name="session" class="form-input text-xs">
                            </div>
                        </div>

                        <div class="pt-4 space-y-3">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Previous school</h4>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">School name</label>
                                <input name="previous_school" class="form-input text-xs mt-1">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Last exam result</label>
                                <input name="last_exam_result" class="form-input text-xs mt-1">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 border-x border-slate-50 px-0 md:px-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Personal details</h4>

                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Student name</label>
                            <input name="student_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Father's name</label>
                            <input name="father_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Mother's name</label>
                            <input name="mother_name" class="form-input text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Mobile number</label>
                            <input name="mobile" class="form-input text-xs">
                        </div>

                        <div class="pt-4">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Guardian information</h4>

                            <div id="guardian_details_area"
                                class="bg-slate-50 p-3 mt-2 border border-slate-100 rounded-none space-y-3">

                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Guardian name</label>
                                    <input name="g_name" class="form-input text-xs mt-1 rounded-none">
                                </div>

                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Guardian mobile</label>
                                    <input name="g_mobile" class="form-input text-xs mt-1 rounded-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Current address</h4>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Village or area</label>
                            <input name="current_village" class="form-input text-xs mt-1">
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Division</label>
                                <input name="current_division" class="form-input text-xs mt-1">
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">District</label>
                                <input name="current_district" class="form-input text-xs mt-1">
                            </div>
                        </div>
                        <div>
                            <label class="text-[11px] text-gray-500 capitalize">Upazila</label>
                            <input name="current_upazila" class="form-input text-xs mt-1">
                        </div>

                        <div class="pt-4 space-y-3">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Permanent address</h4>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Village or area</label>
                                <input name="permanent_village" class="form-input text-xs mt-1">
                            </div>
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">Division</label>
                                    <input name="permanent_division" class="form-input text-xs mt-1">
                                </div>
                                <div>
                                    <label class="text-[11px] text-gray-500 capitalize">District</label>
                                    <input name="permanent_district" class="form-input text-xs mt-1">
                                </div>
                            </div>
                            <div>
                                <label class="text-[11px] text-gray-500 capitalize">Upazila</label>
                                <input name="permanent_upazila" class="form-input text-xs mt-1">
                            </div>
                        </div>

                        <div class="pt-4">
                            <h4 class="text-xs text-slate-600 border-b pb-1 capitalize">Application status</h4>
                            <div class="mt-2">
                                <label class="text-[11px] text-gray-500 capitalize">Update status</label>
                                <select name="status" class="form-input text-xs mt-1">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="px-6 py-2 lg:py-3 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button onclick="closeDetailsModal()"
                    class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                    style="border-radius: 0;">Cancel</button>
                <button onclick="updateFullDetails()"
                    class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                    style="border-radius: 0;">
                    Update
                </button>
            </div>
        </div>
    </div>



    {{-- Update Student Modal --}}
    <div id="studentModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-4 sm:p-8 backdrop-blur-sm">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl flex flex-col border border-gray-100 max-h-[90vh] overflow-hidden">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white flex-shrink-0">
                <h3 class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Update Student Info
                </h3>
            </div>

            <form id="studentForm" class="flex flex-col overflow-hidden flex-1" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="student_id" id="student_id">

                <div class="p-4 sm:p-5 space-y-4 bg-gray-50/30 overflow-y-auto custom-scrollbar flex-1">

                    {{-- Section 1: Admission Details --}}
                    <div class="border border-gray-200 bg-white p-3 sm:p-4">
                        <h4 class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-b pb-1.5 mb-3">Admission Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">School Name</label>
                                <input type="text" name="school"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius:0; background:#f1f5f9; color:#94a3b8;" readonly />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">ID Number</label>
                                <input type="text" name="student_id_number"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius:0; background:#f1f5f9; color:#94a3b8;" readonly />
                            </div>
                            {{-- <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Student Name</label>
                                <input type="text" name="student_name"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div> --}}
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Class</label>
                                <select name="class" id="edit_class"
                                    class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Group</label>
                                <select name="group" id="edit_group"
                                    class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                    <option value="">Select Group</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Section</label>
                                <select name="section" id="edit_section"
                                    class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Session</label>
                                <select name="session" id="edit_session"
                                    class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Admission Fee</label>
                                <input type="text" name="admission_fee"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius:0; background:#f1f5f9; color:#94a3b8;" readonly />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Admission Date</label>
                                <input type="date" name="admission_date"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Previous School --}}
                    <div class="border border-gray-200 bg-white p-3 sm:p-4">
                        <h4 class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-b pb-1.5 mb-3">Previous School</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">School Name</label>
                                <input type="text" name="previous_school"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Class</label>
                                <input type="text" name="previous_class"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Group</label>
                                <input type="text" name="previous_group"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Section</label>
                                <input type="text" name="previous_section"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Session</label>
                                <input type="text" name="previous_session"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Last Exam Result</label>
                                <input type="text" name="last_exam_result"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Student Personal --}}
                    <div class="border border-gray-200 bg-white p-3 sm:p-4">
                        <h4 class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-b pb-1.5 mb-3">Student Personal</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Student Name</label>
                                <input type="text" name="student_name" 
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Father's Name</label>
                                <input type="text" name="father_name"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Mother's Name</label>
                                <input type="text" name="mother_name"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1 capitalize">Mobile</label>
                                <input type="text" name="mobile"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                            </div>
                        </div>

                        {{-- Current Address --}}
                        <div class="mt-4">
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest border-b pb-1 mb-2">Current Address</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Division</label>
                                    <select id="edit_current_division" name="current_division"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">Division</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">District</label>
                                    <select id="edit_current_district" name="current_district"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">District</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Upazila</label>
                                    <select id="edit_current_upazila" name="current_upazila"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">Upazila</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Village</label>
                                    <input type="text" name="current_village" id="edit_current_village"
                                        class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                                </div>
                            </div>
                        </div>

                        {{-- Same as Current Address checkbox --}}
                        <div class="flex items-center gap-2 mt-3">
                            <input type="checkbox" id="sameAsCurrentAddressModal" onchange="toggleSameAddressModal()"
                                class="w-4 h-4 accent-blue-600 cursor-pointer">
                            <label for="sameAsCurrentAddressModal" class="text-[12px] text-gray-600 cursor-pointer select-none">Same as Current Address</label>
                        </div>

                        {{-- Permanent Address --}}
                        <div class="mt-2">
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest border-b pb-1 mb-2">Permanent Address</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Division</label>
                                    <select id="edit_permanent_division" name="permanent_division"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">Division</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">District</label>
                                    <select id="edit_permanent_district" name="permanent_district"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">District</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Upazila</label>
                                    <select id="edit_permanent_upazila" name="permanent_upazila"
                                        class="form-input-fixed w-full border border-gray-200 py-0 px-2 text-xs h-[32px] appearance-none bg-white" style="border-radius:0;">
                                        <option value="">Upazila</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1 capitalize">Village</label>
                                    <input type="text" name="permanent_village" id="edit_permanent_village"
                                        class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]" style="border-radius:0;" />
                                </div>
                            </div>
                        </div>

                        {{-- Profile Photo --}}
                        <div class="mt-4">
                            <p class="text-[9px] font-semibold text-gray-300 uppercase tracking-widest border-b pb-1 mb-2">Profile Photo</p>
                            <div class="flex gap-3 items-center">
                                <div class="flex-grow">
                                    <input type="file" name="image" id="photoInput"
                                        class="form-input-fixed w-full text-[11px] file:mr-4 file:py-1 file:px-3 file:border file:border-gray-100 file:text-[10px] file:bg-gray-50 file:text-gray-600 border border-gray-200 h-[32px] flex items-center"
                                        accept="image/*" style="border-radius:0;" />
                                </div>
                                <div class="w-[40px] h-[40px] border border-gray-200 bg-white flex items-center justify-center overflow-hidden flex-shrink-0"
                                    id="imagePreview" style="border-radius:50%;">
                                    <i class="mdi mdi-camera text-gray-300 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer is fixed --}}
                <div
                    class="px-4 sm:px-6 py-3 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 flex-shrink-0">
                    <button type="button" id="closeStudentModal" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Discard
                    </button>
                    <button type="button" id="activeEditableBtn" onclick="activateEditing()"
                        class="w-1/2 sm:w-auto sm:px-6 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center gap-1.5 whitespace-nowrap"
                        style="border-radius: 0;">
                        <i class="fa-regular fa-pen-to-square text-[11px]"></i> Editable
                    </button>
                    <button type="submit" id="saveStudentBtn" disabled
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] hidden btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap opacity-50 cursor-not-allowed"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const studentModal = document.getElementById('studentModal');
        const detailsModal = document.getElementById('detailsModal');
        const filterModal  = document.getElementById('filterModal');
        const exportModal  = document.getElementById('exportModal');
        const photoInput   = document.getElementById('photoInput');
        const imagePreview = document.getElementById('imagePreview');
        const detailsForm  = document.getElementById('detailsForm');
        let currentPage    = 1;

        // --- PHOTO PREVIEW LOGIC ---
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML =
                    `<img src="${e.target.result}" class="w-full h-full object-cover" />`;
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            }
        });

        // --- CORE FETCH FUNCTION ---
        function fetchStudents(page = 1) {
            currentPage = page;
            const search = document.getElementById('studentSearch').value;

            // Comprehensive Filters
            const classVal = document.getElementById('classFilter').value;
            const groupVal = document.getElementById('groupFilter').value;
            const sectionVal = document.getElementById('sectionFilter').value;
            const sessionVal = document.getElementById('sessionFilter').value;

            axios.get('{{ url('/api/school/students') }}', {
                    params: {
                        search,
                        class: classVal,
                        group: groupVal,
                        section: sectionVal,
                        session: sessionVal,
                        page
                    }
                })
                .then(res => {
                    const students = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('studentTableBody');
                    tbody.innerHTML = '';

                    students.forEach((s, index) => {
                        const sl = (meta.current_page - 1) * meta.per_page + (index + 1);
                        const photoUrl = s.image ? `/storage/${s.image}` :
                            `https://ui-avatars.com/api/?background=random&name=${s.student_name}`;

                        tbody.innerHTML += `
                <tr>
                    <td class="font-normal text-gray-400 text-[12px]">${sl}</td>
                    <td class="align-middle"><img src="${photoUrl}" class="table-photo" style="border-radius: 50% !important; width: 32px !important; height: 32px !important; object-fit: cover !important;" /></td>
                    <td><span class="student-id">${s.student_id_number}</span></td>
                    <td><span class="student-name">${s.student_name}</span></td>
                    <td><span class="student-father-name">${s.father_name}</span></td>
                    <td><span class="cell-data">${s.class_name || s.class}</span></td>
                    <td><span class="cell-data">${s.group_name || s.group || '-'}</span></td>
                    <td><span class="cell-data">${s.section_name || s.section || '-'}</span></td>
                    <td><span class="cell-data">${s.session_year || s.session}</span></td>
                    
                    <td>
                        <div class="action-buttons">
                            
                            <button onclick="editStudent(${s.id})" title="Edit" class="btn-action edit">
                                <i class="far fa-edit" style="font-size: 15px;"></i>
                            </button>
                            <button onclick="deleteStudent(${s.id})" title="Delete" class="btn-action delete">
                                <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
                    });

                    renderPagination(meta);
                }).catch(e => console.error("Load failed", e));
        }

        // --- DEPENDENT DROPDOWN LOGIC ---
        async function loadFilterOptions() {
            try {
                const res = await axios.get('{{ url('/api/get-school-classes') }}');
                const classSelect = document.getElementById('classFilter');
                if (classSelect) {
                    classSelect.innerHTML = '<option value="">All Classes</option>';
                    res.data.data.forEach(c => {
                        classSelect.innerHTML +=
                            `<option value="${c.id}" data-id="${c.id}">${c.class_name}</option>`;
                    });
                }
            } catch (e) {
                console.error("Class load failed", e);
            }
        }

        document.getElementById('classFilter')?.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const classId = selectedOption.getAttribute('data-id');
            const groupSelect = document.getElementById('groupFilter');
            const sectionSelect = document.getElementById('sectionFilter');
            const sessionSelect = document.getElementById('sessionFilter');

            groupSelect.innerHTML = '<option value="">All Groups</option>';
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            sessionSelect.innerHTML = '<option value="">All Sessions</option>';

            if (!classId) return;

            const gRes = await axios.get('{{ url('/api/get-school-groups') }}', {
                params: {
                    class_id: classId
                }
            });
            gRes.data.data.forEach(g => {
                groupSelect.innerHTML +=
                    `<option value="${g.id}" data-id="${g.id}">${g.group_name}</option>`;
            });

            const sRes = await axios.get('{{ url('/api/get-school-sessions') }}', {
                params: {
                    class_id: classId
                }
            });
            sRes.data.data.forEach(s => {
                sessionSelect.innerHTML +=
                    `<option value="${s.id}">${s.session_year}</option>`;
            });
        });

        document.getElementById('groupFilter')?.addEventListener('change', async function() {
            const selectedOption = this.options[this.selectedIndex];
            const groupId = selectedOption.getAttribute('data-id');
            const sectionSelect = document.getElementById('sectionFilter');
            sectionSelect.innerHTML = '<option value="">All Sections</option>';

            if (!groupId) return;

            const res = await axios.get('{{ url('/api/get-school-sections') }}', {
                params: {
                    group_id: groupId
                }
            });
            res.data.data.forEach(sec => {
                sectionSelect.innerHTML +=
                    `<option value="${sec.id}">${sec.section_name}</option>`;
            });
        });

        // --- PERMANENTLY DISABLED FIELDS (never editable) ---
        const PERM_DISABLED = ['student_id_number', 'admission_fee', 'previous_school'];

        function setModalReadOnly(readOnly) {
            document.querySelectorAll('#studentForm input, #studentForm select').forEach(el => {
                const name = el.getAttribute('name');
                const isPerm = PERM_DISABLED.includes(name);
                if (isPerm || readOnly) {
                    el.classList.add('edit-field-disabled');
                    if (el.tagName === 'INPUT') el.readOnly = true;
                } else {
                    el.classList.remove('edit-field-disabled');
                    if (el.tagName === 'INPUT') el.readOnly = false;
                }
                // Permanently disabled fields always get readonly regardless
                if (isPerm && el.tagName === 'INPUT') el.readOnly = true;
            });
        }

        function activateEditing() {
            setModalReadOnly(false);
            document.getElementById('activeEditableBtn').classList.add('hidden');
            const saveBtn = document.getElementById('saveStudentBtn');
            saveBtn.disabled = false;
            saveBtn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
        }

        // --- ACTIONS ---
        async function editStudent(id) {
            // Show modal immediately — don't wait for API
            studentModal.classList.remove('hidden');
            // Reset to read-only view mode and reset footer buttons
            setModalReadOnly(true);
            document.getElementById('activeEditableBtn').classList.remove('hidden');
            const saveBtn = document.getElementById('saveStudentBtn');
            saveBtn.disabled = true;
            saveBtn.classList.add('hidden', 'opacity-50', 'cursor-not-allowed');

            try {
                // Round 1 (parallel): student data + class list + geo divisions
                const [studentRes, classRes, divRes] = await Promise.all([
                    axios.get('{{ url('/api/school/students') }}/' + id),
                    axios.get('{{ url('/api/get-school-classes') }}'),
                    axios.get(`${GEO_BASE}/divisions`)
                ]);

                const s = studentRes.data;
                document.getElementById('student_id').value = s.id;

                const setVal = (name, value) => {
                    const el = document.querySelector(`#studentForm [name="${name}"]`);
                    if (el) el.value = value ?? '';
                };

                setVal('student_id_number', s.student_id_number);
                setVal('school', s.school);
                setVal('student_name', s.student_name);
                setVal('father_name', s.father_name);
                setVal('mother_name', s.mother_name);
                setVal('mobile', s.mobile);
                setVal('previous_school', s.previous_school);
                setVal('previous_class', s.previous_class);
                setVal('previous_group', s.previous_group);
                setVal('previous_section', s.previous_section);
                setVal('previous_session', s.previous_session);
                setVal('last_exam_result', s.last_exam_result);
                setVal('current_village', s.current_village);
                setVal('permanent_village', s.permanent_village);
                setVal('admission_fee', s.admission_fee);
                setVal('admission_date', s.admission_date);

                document.getElementById('sameAsCurrentAddressModal').checked = false;
                toggleSameAddressModal();

                const photoUrl = s.image ? `/storage/${s.image}` :
                    `https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(s.student_name)}`;
                imagePreview.innerHTML = `<img src="${photoUrl}" class="w-full h-full object-cover" style="border-radius:50%;" />`;

                // Populate class select
                const classSelect = document.getElementById('edit_class');
                classSelect.innerHTML = '<option value="">Select Class</option>';
                classRes.data.data.forEach(c => classSelect.innerHTML += `<option value="${c.id}">${c.class_name}</option>`);
                classSelect.value = s.class;

                // Populate both division selects from the same divisions response
                const divs = divRes.data.data;
                ['edit_current_division', 'edit_permanent_division'].forEach(selId => {
                    const sel = document.getElementById(selId);
                    sel.innerHTML = '<option value="">Division</option>';
                    divs.forEach(d => sel.innerHTML += `<option value="${d.division}">${d.division}</option>`);
                });
                document.getElementById('edit_current_division').value  = s.current_division  ?? '';
                document.getElementById('edit_permanent_division').value = s.permanent_division ?? '';

                // Round 2 (parallel): group + session + current districts + permanent districts
                const round2 = [];
                if (s.class) {
                    round2.push(
                        axios.get('{{ url('/api/get-school-groups') }}',   { params: { class_id: s.class } }).then(r => ({ k: 'groups',   d: r.data.data })),
                        axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: s.class } }).then(r => ({ k: 'sessions', d: r.data.data }))
                    );
                }
                if (s.current_division)   round2.push(axios.get(`${GEO_BASE}/division/${s.current_division}`).then(r  => ({ k: 'curDists', d: r.data.data })));
                if (s.permanent_division) round2.push(axios.get(`${GEO_BASE}/division/${s.permanent_division}`).then(r => ({ k: 'perDists', d: r.data.data })));

                const r2 = await Promise.all(round2);
                r2.forEach(({ k, d }) => {
                    if (k === 'groups') {
                        const sel = document.getElementById('edit_group');
                        sel.innerHTML = '<option value="">Select Group</option>';
                        d.forEach(g => sel.innerHTML += `<option value="${g.id}">${g.group_name}</option>`);
                        sel.value = s.group;
                    } else if (k === 'sessions') {
                        const sel = document.getElementById('edit_session');
                        sel.innerHTML = '<option value="">Select Session</option>';
                        d.forEach(sess => sel.innerHTML += `<option value="${sess.id}">${sess.session_year}</option>`);
                        sel.value = s.session;
                    } else if (k === 'curDists') {
                        const sel = document.getElementById('edit_current_district');
                        sel.innerHTML = '<option value="">District</option>';
                        d.forEach(dist => sel.innerHTML += `<option value="${dist.district}">${dist.district}</option>`);
                        sel.value = s.current_district ?? '';
                    } else if (k === 'perDists') {
                        const sel = document.getElementById('edit_permanent_district');
                        sel.innerHTML = '<option value="">District</option>';
                        d.forEach(dist => sel.innerHTML += `<option value="${dist.district}">${dist.district}</option>`);
                        sel.value = s.permanent_district ?? '';
                    }
                });

                // Round 3 (parallel): sections + current upazila + permanent upazila
                const round3 = [];
                if (s.group)              round3.push(axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: s.group } }).then(r => ({ k: 'sections', d: r.data.data })));
                if (s.current_district)   round3.push(axios.get(`${GEO_BASE}/district/${s.current_district}`).then(r   => ({ k: 'curUpa', d: r.data.data[0].upazillas })));
                if (s.permanent_district) round3.push(axios.get(`${GEO_BASE}/district/${s.permanent_district}`).then(r => ({ k: 'perUpa', d: r.data.data[0].upazillas })));

                const r3 = await Promise.all(round3);
                r3.forEach(({ k, d }) => {
                    if (k === 'sections') {
                        const sel = document.getElementById('edit_section');
                        sel.innerHTML = '<option value="">Select Section</option>';
                        d.forEach(sec => sel.innerHTML += `<option value="${sec.id}">${sec.section_name}</option>`);
                        sel.value = s.section;
                    } else if (k === 'curUpa') {
                        const sel = document.getElementById('edit_current_upazila');
                        sel.innerHTML = '<option value="">Upazila</option>';
                        d.forEach(u => sel.innerHTML += `<option value="${u}">${u}</option>`);
                        sel.value = s.current_upazila ?? '';
                    } else if (k === 'perUpa') {
                        const sel = document.getElementById('edit_permanent_upazila');
                        sel.innerHTML = '<option value="">Upazila</option>';
                        d.forEach(u => sel.innerHTML += `<option value="${u}">${u}</option>`);
                        sel.value = s.permanent_upazila ?? '';
                    }
                });

            } catch (err) {
                console.error("Edit failed", err);
            }
        }

        // Cascade dropdowns inside the edit modal
        document.getElementById('edit_class')?.addEventListener('change', async function() {
            const classId = this.value;
            const groupSelect = document.getElementById('edit_group');
            const sectionSelect = document.getElementById('edit_section');
            const sessionSelect = document.getElementById('edit_session');
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            sessionSelect.innerHTML = '<option value="">Select Session</option>';
            if (!classId) return;
            const [gRes, sessRes] = await Promise.all([
                axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } }),
                axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: classId } })
            ]);
            gRes.data.data.forEach(g => {
                groupSelect.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
            });
            sessRes.data.data.forEach(s => {
                sessionSelect.innerHTML += `<option value="${s.id}">${s.session_year}</option>`;
            });
        });

        document.getElementById('edit_group')?.addEventListener('change', async function() {
            const groupId = this.value;
            const sectionSelect = document.getElementById('edit_section');
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (!groupId) return;
            const res = await axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: groupId } });
            res.data.data.forEach(sec => {
                sectionSelect.innerHTML += `<option value="${sec.id}">${sec.section_name}</option>`;
            });
        });

        // --- GEO FUNCTIONS FOR EDIT MODAL ---
        const GEO_BASE = 'https://bdapis.com/api/v1.2';

        async function loadModalDivisions() {
            try {
                const res = await axios.get(`${GEO_BASE}/divisions`);
                const divs = res.data.data;
                ['edit_current_division', 'edit_permanent_division'].forEach(id => {
                    const sel = document.getElementById(id);
                    if (!sel) return;
                    const current = sel.value;
                    sel.innerHTML = '<option value="">Division</option>';
                    divs.forEach(d => sel.innerHTML += `<option value="${d.division}">${d.division}</option>`);
                    if (current) sel.value = current;
                });
            } catch (e) { console.error('Division load failed', e); }
        }

        async function populateDistrictModal(divId, distId, upaId) {
            const divisionName = document.getElementById(divId).value;
            const distSel = document.getElementById(distId);
            const upaSel = document.getElementById(upaId);
            distSel.innerHTML = '<option value="">Loading...</option>';
            upaSel.innerHTML  = '<option value="">Upazila</option>';
            if (!divisionName) { distSel.innerHTML = '<option value="">District</option>'; return; }
            const res = await axios.get(`${GEO_BASE}/division/${divisionName}`);
            distSel.innerHTML = '<option value="">District</option>';
            res.data.data.forEach(d => distSel.innerHTML += `<option value="${d.district}">${d.district}</option>`);
        }

        async function populateUpazilaModal(distId, upaId) {
            const districtName = document.getElementById(distId).value;
            const upaSel = document.getElementById(upaId);
            upaSel.innerHTML = '<option value="">Loading...</option>';
            if (!districtName) { upaSel.innerHTML = '<option value="">Upazila</option>'; return; }
            const res = await axios.get(`${GEO_BASE}/district/${districtName}`);
            const upazillas = res.data.data[0].upazillas;
            upaSel.innerHTML = '<option value="">Upazila</option>';
            upazillas.forEach(u => upaSel.innerHTML += `<option value="${u}">${u}</option>`);
        }

        async function syncPermanentFromCurrentModal() {
            if (!document.getElementById('sameAsCurrentAddressModal').checked) return;
            const curDiv  = document.getElementById('edit_current_division');
            const curDist = document.getElementById('edit_current_district');
            const curUpa  = document.getElementById('edit_current_upazila');
            const perDiv  = document.getElementById('edit_permanent_division');

            perDiv.value = curDiv.value;
            document.getElementById('edit_permanent_village').value = document.getElementById('edit_current_village').value;
            if (!curDiv.value) return;

            await populateDistrictModal('edit_permanent_division', 'edit_permanent_district', 'edit_permanent_upazila');
            document.getElementById('edit_permanent_district').value = curDist.value;
            if (!curDist.value) return;

            await populateUpazilaModal('edit_permanent_district', 'edit_permanent_upazila');
            document.getElementById('edit_permanent_upazila').value = curUpa.value;
        }

        function toggleSameAddressModal() {
            const checked = document.getElementById('sameAsCurrentAddressModal').checked;
            ['edit_permanent_division', 'edit_permanent_district', 'edit_permanent_upazila'].forEach(id => {
                const sel = document.getElementById(id);
                if (!sel) return;
                sel.style.pointerEvents = checked ? 'none' : '';
                sel.style.background = checked ? '#f9fafb' : '';
                sel.style.color = checked ? '#6b7280' : '';
                sel.style.cursor = checked ? 'not-allowed' : '';
            });
            const permVillage = document.getElementById('edit_permanent_village');
            if (permVillage) {
                permVillage.readOnly = checked;
                permVillage.style.background = checked ? '#f9fafb' : '';
                permVillage.style.color = checked ? '#6b7280' : '';
                permVillage.style.cursor = checked ? 'not-allowed' : '';
            }
            if (checked) syncPermanentFromCurrentModal();
        }

        // Cascade listeners for edit modal address selects
        document.getElementById('edit_current_division')?.addEventListener('change', async () => {
            await populateDistrictModal('edit_current_division', 'edit_current_district', 'edit_current_upazila');
            syncPermanentFromCurrentModal();
        });
        document.getElementById('edit_current_district')?.addEventListener('change', async () => {
            await populateUpazilaModal('edit_current_district', 'edit_current_upazila');
            syncPermanentFromCurrentModal();
        });
        document.getElementById('edit_current_upazila')?.addEventListener('change', () => syncPermanentFromCurrentModal());
        document.getElementById('edit_current_village')?.addEventListener('input', () => syncPermanentFromCurrentModal());
        document.getElementById('edit_permanent_division')?.addEventListener('change', () =>
            populateDistrictModal('edit_permanent_division', 'edit_permanent_district', 'edit_permanent_upazila'));
        document.getElementById('edit_permanent_district')?.addEventListener('change', () =>
            populateUpazilaModal('edit_permanent_district', 'edit_permanent_upazila'));

        function viewDetails(id) {
            axios.get('{{ url('/api/school/students/details') }}/' + id).then(res => {
                const s = res.data.student;
                const g = res.data.guardian;
                document.getElementById('det_student_id').value = s.id;
                Object.keys(s).forEach(key => {
                    const input = detailsForm.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (key === 'class') input.value = s.class_name || s.class || '';
                        else if (key === 'section') input.value = s.section_name || s.section || '';
                        else if (key === 'group') input.value = s.group_name || s.group || '';
                        else if (key === 'session') input.value = s.session_year || s.session || '';
                        else input.value = s[key] || '';
                    }
                });
                if (g) {
                    if (detailsForm.querySelector('[name="g_name"]')) detailsForm.querySelector('[name="g_name"]')
                        .value = g.name || '';
                    if (detailsForm.querySelector('[name="g_mobile"]')) detailsForm.querySelector(
                        '[name="g_mobile"]').value = g.mobile || '';
                }
                detailsModal.classList.remove('hidden');
            }).catch(err => console.error("Details fetch failed", err));
        }

        function updateFullDetails() {
            const id = document.getElementById('det_student_id').value;
            const formData = new FormData(detailsForm);
            const data = Object.fromEntries(formData.entries());
            axios.post(`{{ url('/api/school/students/update-details') }}/${id}`, data, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => {
                Toastify({
                    text: "Profile Updated Successfully!",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeDetailsModal();
                fetchStudents(currentPage);
            }).catch(err => alert("Update failed."));
        }

        document.getElementById('studentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const sid = document.getElementById('student_id').value;
            const formData = new FormData(this);

            // Clear all previous error borders
            document.querySelectorAll('#studentForm input, #studentForm select').forEach(el => {
                el.style.removeProperty('border-color');
            });

            axios.post(`{{ url('/api/school/students') }}/${sid}`, formData, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                Toastify({
                    text: "Updated Successfully!",
                    style: { background: "#10b981" }
                }).showToast();
                studentModal.classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => {
                if (err.response && err.response.status === 422) {
                    const errors = err.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        const el = document.querySelector(`#studentForm [name="${field}"]`);
                        if (el) el.style.setProperty('border-color', '#dc2626', 'important');
                    });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required',
                        text: 'Please fill all required fields.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        });

        function deleteStudent(id) {
            Swal.fire({
                title: 'Delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'CONFIRM DELETE',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('{{ url('/api/school/students') }}/' + id, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        Toastify({
                            text: "Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchStudents(currentPage);
                    });
                }
            });
        }

        // --- PAGINATION ---
        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (!info || !controls) return;
            info.innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchStudents(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                if (i > 5 && i < meta.last_page) continue;
                const pgBtn = document.createElement('button');
                pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                pgBtn.innerText = i;
                pgBtn.onclick = () => fetchStudents(i);
                controls.appendChild(pgBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchStudents(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        // --- FILTERS & MODALS ---
        document.getElementById('studentSearch')?.addEventListener('input', () => fetchStudents(1));

        document.getElementById('applyFilter')?.addEventListener('click', () => {
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

        document.getElementById('resetFilter')?.addEventListener('click', () => {
            document.getElementById('classFilter').value = '';
            document.getElementById('groupFilter').innerHTML = '<option value="">All Groups</option>';
            document.getElementById('sectionFilter').innerHTML = '<option value="">All Sections</option>';
            document.getElementById('sessionFilter').innerHTML = '<option value="">All Sessions</option>';
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

        function closeModal() {
            studentModal.classList.add('hidden');
        }

        function closeDetailsModal() {
            detailsModal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();
            loadModalDivisions();
            fetchStudents();

            // Modal Triggering Logic
            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', !show);
            };

            document.getElementById('btnFilter')?.addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('btnExport')?.addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport')?.addEventListener('click', () => toggleModal('exportModal',
                false));
            document.getElementById('closeStudentModal')?.addEventListener('click', closeModal);

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });

        // ============================================================
        // BULK UPLOAD
        // ============================================================

        // ── Custom dropdown (keeps option list inside modal bounds) ───────────
        class BulkDropdown {
            constructor(wrapperId, defaultLabel) {
                this.wrap        = document.getElementById(wrapperId);
                this.trigger     = this.wrap.querySelector('.bulk-dd-trigger');
                this.list        = this.wrap.querySelector('.bulk-dd-list');
                this.label       = this.trigger.querySelector('.bulk-dd-label');
                this.defaultLabel = defaultLabel;
                this.value       = '';
                this.text        = '';
                this._onChange   = null;

                this.trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !this.list.classList.contains('hidden');
                    // close all other bulk dropdowns first
                    document.querySelectorAll('.bulk-dd-wrap').forEach(w => {
                        w.querySelector('.bulk-dd-list').classList.add('hidden');
                        w.querySelector('.bulk-dd-trigger').style.borderColor = '';
                    });
                    if (!isOpen) {
                        this.list.classList.remove('hidden');
                        this.trigger.style.borderColor = '#3b82f6';
                    }
                });
            }

            setOptions(options) {
                this.list.innerHTML = '';
                options.forEach(opt => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-1.5 text-[11px] cursor-pointer hover:bg-blue-50 hover:text-blue-700 text-gray-700';
                    li.textContent  = opt.text;
                    li.dataset.value = String(opt.value);
                    li.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.value = String(opt.value);
                        this.text  = opt.text;
                        this.label.textContent = opt.text;
                        this.label.classList.remove('text-gray-400');
                        this.label.classList.add('text-gray-700');
                        this.list.classList.add('hidden');
                        this.trigger.style.borderColor = '';
                        if (this._onChange) this._onChange(this.value, this.text);
                    });
                    this.list.appendChild(li);
                });
            }

            // Reset display only — keeps existing options intact
            reset(label) {
                this.value = '';
                this.text  = '';
                this.label.textContent = label ?? this.defaultLabel;
                this.label.classList.add('text-gray-400');
                this.label.classList.remove('text-gray-700');
                this.list.classList.add('hidden');
                this.trigger.style.borderColor = '';
            }

            // Reset display AND wipe options
            clear(label) {
                this.reset(label);
                this.list.innerHTML = '';
            }

            onChange(fn) { this._onChange = fn; }
        }

        // Close all bulk dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.bulk-dd-wrap').forEach(w => {
                w.querySelector('.bulk-dd-list').classList.add('hidden');
                w.querySelector('.bulk-dd-trigger').style.borderColor = '';
            });
        });

        // Instantiate the four dropdowns
        const ddClass   = new BulkDropdown('wrap_bulkClass',   'Select Class');
        const ddGroup   = new BulkDropdown('wrap_bulkGroup',   'No Group / All Groups');
        const ddSection = new BulkDropdown('wrap_bulkSection', 'Select Section');
        const ddSession = new BulkDropdown('wrap_bulkSession', 'Select Session');

        // Tracked selection state
        let bulkClassId = '', bulkGroupId = '', bulkSectionId = '', bulkSessionId = '';
        let bulkComboText = '';

        function showBulkState(state) {
            ['selector', 'upload', 'processing', 'result'].forEach(s => {
                document.getElementById('bulkState_' + s).classList.toggle('hidden', s !== state);
            });
        }

        function bulkCanDownload() {
            return bulkClassId && bulkSectionId && bulkSessionId;
        }

        function updateBulkButtons() {
            const ok = bulkCanDownload();
            document.getElementById('btnDownloadTemplate').disabled = !ok;
            document.getElementById('btnGoToUpload').disabled       = !ok;
        }

        // Open modal → reset to selector state
        document.getElementById('btnBulkUpload')?.addEventListener('click', () => {
            bulkClassId = ''; bulkGroupId = ''; bulkSectionId = ''; bulkSessionId = '';
            bulkComboText = '';
            ddClass.reset();
            ddGroup.clear();
            ddSection.clear();
            ddSession.clear();
            document.getElementById('bulkSelectorError').classList.add('hidden');
            updateBulkButtons();
            showBulkState('selector');
            document.getElementById('bulkUploadModal').classList.remove('hidden');
        });

        document.getElementById('btnBulkClose')?.addEventListener('click', () => {
            document.getElementById('bulkUploadModal').classList.add('hidden');
        });

        // ── Load classes once at page load ────────────────────────────────────
        (async () => {
            try {
                const res = await axios.get('{{ url('/api/get-school-classes') }}');
                ddClass.setOptions(res.data.data.map(c => ({ value: c.id, text: c.class_name })));
            } catch (e) { console.error('Bulk class load failed', e); }
        })();

        // ── Cascade: Class → Group + Session ──────────────────────────────────
        ddClass.onChange(async (classId) => {
            bulkClassId   = classId;
            bulkGroupId   = '';
            bulkSectionId = '';
            bulkSessionId = '';
            ddGroup.clear('No Group / All Groups');
            ddSection.clear('Select Section');
            ddSession.clear('Select Session');
            updateBulkButtons();

            if (!classId) return;

            try {
                const gRes = await axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } });
                const groups = gRes.data.data;
                if (groups.length > 0) {
                    ddGroup.setOptions([
                        { value: '', text: 'No Group / All Groups' },
                        ...groups.map(g => ({ value: g.id, text: g.group_name }))
                    ]);
                }
            } catch (e) { console.error('Bulk group load failed', e); }

            await bulkLoadSessions();
        });

        // ── Cascade: Group → Section + Session ────────────────────────────────
        ddGroup.onChange(async (groupId) => {
            bulkGroupId   = groupId;
            bulkSectionId = '';
            ddSection.clear('Select Section');
            updateBulkButtons();

            try {
                const params = groupId ? { group_id: groupId } : {};
                const res = await axios.get('{{ url('/api/get-school-sections') }}', { params });
                ddSection.setOptions(res.data.data.map(s => ({ value: s.id, text: s.section_name })));
            } catch (e) { console.error('Bulk section load failed', e); }

            await bulkLoadSessions();
        });

        // ── Cascade: Section → Session ────────────────────────────────────────
        ddSection.onChange(async (sectionId) => {
            bulkSectionId = sectionId;
            updateBulkButtons();
            await bulkLoadSessions();
        });

        ddSession.onChange((sessionId) => {
            bulkSessionId = sessionId;
            updateBulkButtons();
        });

        async function bulkLoadSessions() {
            if (!bulkClassId) return;
            bulkSessionId = '';
            ddSession.clear('Select Session');
            updateBulkButtons();
            try {
                const params = { class_id: bulkClassId };
                if (bulkGroupId)   params.group_id   = bulkGroupId;
                if (bulkSectionId) params.section_id = bulkSectionId;
                const res = await axios.get('{{ url('/api/get-school-sessions') }}', { params });
                ddSession.setOptions(res.data.data.map(s => ({ value: s.id, text: s.session_year })));
            } catch (e) { console.error('Bulk session load failed', e); }
        }

        // ── Download template ─────────────────────────────────────────────────
        document.getElementById('btnDownloadTemplate')?.addEventListener('click', async () => {
            if (!bulkCanDownload()) return;

            const errEl = document.getElementById('bulkSelectorError');
            errEl.classList.add('hidden');

            try {
                const params = { class_id: bulkClassId, section_id: bulkSectionId, session_id: bulkSessionId };
                if (bulkGroupId) params.group_id = bulkGroupId;

                const res = await axios.get('{{ url('/api/school/student-import/template') }}', {
                    params,
                    responseType: 'blob'
                });

                const cd       = res.headers['content-disposition'] || '';
                const match    = cd.match(/filename="?([^";\n]+)"?/);
                const filename = match ? match[1] : 'student-template.xlsx';

                const url = URL.createObjectURL(res.data);
                const a   = document.createElement('a');
                a.href     = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            } catch (err) {
                errEl.textContent = 'Failed to download template. Please try again.';
                errEl.classList.remove('hidden');
            }
        });

        // ── Go to upload step ─────────────────────────────────────────────────
        document.getElementById('btnGoToUpload')?.addEventListener('click', () => {
            if (!bulkCanDownload()) return;

            bulkComboText = `Class: ${ddClass.text}`
                + (bulkGroupId ? ` · Group: ${ddGroup.text}` : '')
                + ` · Section: ${ddSection.text} · Session: ${ddSession.text}`;
            document.getElementById('bulkComboBanner').textContent = bulkComboText;

            document.getElementById('bulkFileInput').value = '';
            document.getElementById('bulkUploadError').classList.add('hidden');
            showBulkState('upload');
        });

        // ── Back to selector ──────────────────────────────────────────────────
        document.getElementById('btnBackToSelector')?.addEventListener('click', () => {
            showBulkState('selector');
        });

        // ── Upload & Import ───────────────────────────────────────────────────
        document.getElementById('btnUploadBulk')?.addEventListener('click', async () => {
            const file  = document.getElementById('bulkFileInput').files[0];
            const errEl = document.getElementById('bulkUploadError');
            errEl.classList.add('hidden');

            if (!file) {
                errEl.textContent = 'Please select an Excel file (.xlsx or .xls).';
                errEl.classList.remove('hidden');
                return;
            }

            const fd = new FormData();
            fd.append('file',       file);
            fd.append('class_id',   bulkClassId);
            fd.append('section_id', bulkSectionId);
            fd.append('session_id', bulkSessionId);
            if (bulkGroupId) fd.append('group_id', bulkGroupId);

            showBulkState('processing');

            try {
                const res = await axios.post('{{ url('/api/school/student-import/upload') }}', fd, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                showBulkResultSuccess(res.data.count);
            } catch (err) {
                if (err.response?.status === 422) {
                    const data = err.response.data;
                    if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
                        showBulkResultErrors(data.message, data.errors);
                    } else {
                        // Simple validation message (e.g. max rows, file issue)
                        showBulkState('upload');
                        errEl.textContent = data.message || 'Validation failed.';
                        errEl.classList.remove('hidden');
                    }
                } else {
                    const msg = err.response?.data?.message || 'An unexpected error occurred. Please try again.';
                    showBulkResultErrors(msg, []);
                }
            }
        });

        // ── Reset ─────────────────────────────────────────────────────────────
        document.getElementById('btnBulkReset')?.addEventListener('click', () => {
            document.getElementById('bulkFileInput').value = '';
            showBulkState('selector');
        });

        // ── Result helpers ────────────────────────────────────────────────────
        function showBulkResultSuccess(count) {
            showBulkState('result');
            const resultEl = document.getElementById('bulkResultText');
            resultEl.textContent = `Import complete. ${count} student(s) created successfully.`;
            resultEl.className   = 'text-[12px] text-green-600 font-medium mb-3';
            document.getElementById('bulkErrorSection').classList.add('hidden');
            fetchStudents(1);
        }

        function showBulkResultErrors(message, errors) {
            showBulkState('result');
            const resultEl   = document.getElementById('bulkResultText');
            const errSection = document.getElementById('bulkErrorSection');
            const errTbody   = document.getElementById('bulkErrorTableBody');

            resultEl.textContent = message;
            resultEl.className   = 'text-[12px] text-red-600 font-medium mb-3';

            errSection.classList.toggle('hidden', errors.length === 0);

            if (errors.length > 0) {
                errTbody.innerHTML = '';
                errors.forEach(e => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t border-gray-100';
                    tr.innerHTML = `
                        <td class="px-2 py-1 border-r border-gray-200 text-center">${e.row}</td>
                        <td class="px-2 py-1 border-r border-gray-200 whitespace-nowrap">${e.field}</td>
                        <td class="px-2 py-1">${e.message}</td>`;
                    errTbody.appendChild(tr);
                });
            }
        }
    </script>
@endsection