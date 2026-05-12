@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        /* 1. Global Reset & Sharp Edges */
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
        }

        /* 2. Container Padding */
        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: 0.75rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }


        /* 3. Table Sharp Style */
        .table-card {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            width: 100%;
            overflow: hidden;
        }

        /* ================= Table Container(UI Modification version) ================= */
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
        th:first-child,
        th:last-child {
            text-align: center !important;
        }

        th:last-child {
            border-right: none;
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


        /* 4. Outline Buttons */
        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all 0.2s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            transition: all 0.2s ease;
            border-radius: 0;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #ffffff;
        }

        /* 5. Pagination Styling - Compact & Sharp (UI Modifications Version) */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.75rem;
            background: #ffffff;
            border-top: 1px solid #edf2f7;
            min-height: 40px;
        }

        #paginationControls {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .pagination-btn {
            padding: 2px 8px;
            min-width: 28px;
            height: 24px;
            font-size: 10px;
            font-weight: 700;
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
                min-height: 32px;
                /* Slimmer bar */
            }

            .pagination-btn {
                min-width: 22px;
                /* Narrower buttons */
                height: 20px;
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

        /* 6. Form & Modal Scaling */
        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: 0.4rem 0.6rem;
            border-radius: 0;
            font-size: 0.8rem;
            background-color: #fff;
            outline: none;
            display: block;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
            box-shadow: none;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .eye-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-15%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 1rem;
            z-index: 10;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 4px;
            transition: color 0.2s;
            background: none;
            border: none;
            cursor: pointer;
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
                            <input type="text" id="teacherSearch" placeholder="Search Faculty..."
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

                        <button id="openTeacherModal"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Teacher
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="teacherSearchMobile" placeholder="Search Faculty..."
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
                            Teacher filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    {{-- Form Field Section --}}
                    <div class="mt-3 mb-4">
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Teacher</label>
                            <div class="relative">
                                <select id="designationFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Teacher...</option>
                                    {{-- Options dynamically loaded here --}}
                                </select>
                                {{-- Icon Wrapper --}}
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
                <div class="table-responsive custom-scrollbar">
                    <table class="min-w-[1200px] text-[11px]">
                        <thead>
                            <tr>
                                <th class="text-center font-bold">Photo</th>
                                <th class="text-left font-bold">Name</th>
                                <th class="text-left font-bold">Designation</th>
                                <th class="text-left font-bold">Id Number</th>
                                <th class="text-left font-bold">Phone</th>
                                <th class="text-left font-bold">Email</th>
                                <th class="text-left font-bold">Date of birth</th>
                                <th class="text-left font-bold">Join date</th>
                                <th class="text-center font-bold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="teacherTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Teacher Profile Modal --}}
    <div id="teacherModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Teacher registration
                </h3>
            </div>

            <form id="teacherForm" class="flex flex-col overflow-hidden m-0" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="teacher_id" id="teacher_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Full
                                name</label>
                            <input type="text" name="name" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                placeholder="John Doe" style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Designation</label>
                            <input type="text" name="designation"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                placeholder="e.g. Senior Lecturer" style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Mobile</label>
                            <input type="text" name="mobile" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                placeholder="+88017..." style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Date of
                                birth</label>
                            <input type="date" name="dob"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Email
                                address</label>
                            <input type="email" name="email" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                placeholder="teacher@school.com" style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1 password-wrapper relative">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Password</label>
                            <input type="password" name="password" id="passInput"
                                class="form-input-fixed w-full pr-10 border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                            <i class="mdi mdi-eye eye-toggle absolute right-3 bottom-2 text-gray-400 cursor-pointer"
                                onclick="togglePass('passInput')"></i>
                        </div>

                        <div class="col-span-1 password-wrapper relative">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Confirm
                                password</label>
                            <input type="password" name="password_confirmation" id="confirmPassInput"
                                class="form-input-fixed w-full pr-10 border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                            <i class="mdi mdi-eye eye-toggle absolute right-3 bottom-2 text-gray-400 cursor-pointer"
                                onclick="togglePass('confirmPassInput')"></i>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Profile
                                photo</label>
                            <div class="flex gap-3">
                                <div class="flex-grow">
                                    <input type="file" name="photo" id="photoInput"
                                        class="form-input-fixed w-full text-[11px] file:mr-4 file:py-1 file:px-3 file:border file:border-gray-100 file:text-[10px] file:bg-gray-50 file:text-gray-600 border border-gray-200 h-[32px] flex items-center"
                                        accept="image/*" style="border-radius: 0;" />
                                </div>
                                <div class="w-[32px] h-[32px] border border-gray-200 bg-white flex items-center justify-center overflow-hidden flex-shrink-0"
                                    id="imagePreview" style="border-radius: 0;">
                                    <i class="mdi mdi-camera text-gray-300 text-sm"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" id="closeTeacherModal"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const teacherModal = document.getElementById('teacherModal');
        const photoInput = document.getElementById('photoInput');
        const imagePreview = document.getElementById('imagePreview');
        let currentPage = 1;

        // Helper function for date formatting (DD/MM/YYYY)
        function formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return dateStr;
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function togglePass(id) {
            const input = document.getElementById(id);
            const icon = event.currentTarget;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('mdi-eye', 'mdi-eye-off');
            } else {
                input.type = "password";
                icon.classList.replace('mdi-eye-off', 'mdi-eye');
            }
        }

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" />`;
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            }
        });

        document.getElementById('openTeacherModal').addEventListener('click', () => {
            document.getElementById('teacherForm').reset();
            document.getElementById('teacher_id').value = '';
            imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            teacherModal.classList.remove('hidden');
        });

        document.getElementById('closeTeacherModal').addEventListener('click', () => teacherModal.classList.add('hidden'));

        const fetchTeachers = (page = 1) => {
            currentPage = page;
            const search = document.getElementById('teacherSearch').value;
            const designation = document.getElementById('designationFilter').value;

            axios.get('{{ url('/api/teachers') }}', {
                    params: {
                        search,
                        designation,
                        page
                    }
                })
                .then(res => {
                    const teachers = res.data.data || res.data;
                    const meta = res.data.meta || {
                        current_page: 1,
                        last_page: 1,
                        total: teachers.length,
                        from: 1,
                        to: teachers.length
                    };

                    const tbody = document.getElementById('teacherTableBody');
                    tbody.innerHTML = '';

                    teachers.forEach(t => {
                        const photoUrl = t.photo ? `/storage/${t.photo}` :
                            'https://ui-avatars.com/api/?background=random&name=' + t.name;

                        tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                           <td class="whitespace-nowrap"><img src="${photoUrl}" alt="${t.name}" /></td>
                            <td class="whitespace-nowrap">${t.name}</td>
                            <td class="whitespace-nowrap">${t.designation || 'N/A'}</td>
                            <td class="whitespace-nowrap">${t.id_number || 'PENDING'}</td>
                            <td class="whitespace-nowrap">${t.mobile}</td>
                            <td class="whitespace-nowrap">${t.email}</td>
                            <td class="whitespace-nowrap">${formatDate(t.dob)}</td>
                            <td class="whitespace-nowrap">${formatDate(t.created_at)}</td>
                            <td>
                                <div class="flex justify-center gap-3">
                                    <button onclick="editTeacher(${t.id})" class="action-icon-btn text-blue-500">
                                        <i class="far fa-edit" style="font-size: 15px;"></i>
                                    </button>
                                    <button onclick="deleteTeacher(${t.id})" class="action-icon-btn text-red-400">
                                        <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });

                    renderPagination(meta);
                }).catch(e => console.error("Load failed", e));
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');

            info.innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchTeachers(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                if (i > 5 && i < meta.last_page) continue;
                const pgBtn = document.createElement('button');
                pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                pgBtn.innerText = i;
                pgBtn.onclick = () => fetchTeachers(i);
                controls.appendChild(pgBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchTeachers(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        fetchTeachers();
        document.getElementById('teacherSearch').addEventListener('input', () => fetchTeachers(1));
        document.getElementById('designationFilter').addEventListener('change', () => fetchTeachers(1));

        document.getElementById('teacherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const tid = document.getElementById('teacher_id').value;
            const formData = new FormData(this);
            if (tid) {
                formData.append('_method', 'PUT');
            }
            const apiUrl = tid ? `{{ url('/api/teachers') }}/${tid}` : '{{ url('/api/teachers') }}';

            axios.post(apiUrl, formData, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(() => {
                    Toastify({
                        text: "Teacher Saved Successfully!",
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#10b981"
                        }
                    }).showToast();
                    teacherModal.classList.add('hidden');
                    fetchTeachers(currentPage);
                }).catch(err => {
                    let errorMsg = 'Action failed';
                    if (err.response && err.response.data.errors) {
                        errorMsg = Object.values(err.response.data.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: errorMsg
                    });
                });
        });

        function editTeacher(id) {
            axios.get('{{ url('/api/teachers') }}/' + id).then(res => {
                const t = res.data;
                document.getElementById('teacher_id').value = t.id;
                document.querySelector('input[name="name"]').value = t.name;
                document.querySelector('input[name="designation"]').value = t.designation;
                document.querySelector('input[name="mobile"]').value = t.mobile;
                document.querySelector('input[name="email"]').value = t.email;
                document.querySelector('input[name="dob"]').value = t.dob;
                const photoUrl = t.photo ? `/storage/${t.photo}` :
                    'https://ui-avatars.com/api/?background=random&name=' + t.name;
                imagePreview.innerHTML = `<img src="${photoUrl}" />`;
                teacherModal.classList.remove('hidden');
            });
        }

        function deleteTeacher(id) {
            Swal.fire({
                title: 'Delete Faculty?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000000',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'CONFIRM DELETE',
                customClass: {
                    popup: 'modal-content-sharp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('{{ url('/api/teachers') }}/' + id, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        Toastify({
                            text: "Record Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchTeachers(currentPage);
                    });
                }
            });
        }

        //Filter & Print modals opening Script
        document.addEventListener('DOMContentLoaded', function() {
            // Helper to toggle modal
            const toggleModal = (id, show) => {
                document.getElementById(id).classList.toggle('hidden', !show);
            };

            // Filter Modal
            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('resetFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('applyFilter').addEventListener('click', () => toggleModal('filterModal',
                false));

            // Export Modal
            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            // Close on outside click
            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });
    </script>
@endsection