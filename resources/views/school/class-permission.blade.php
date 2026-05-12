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


        .btn-outline-premium {
            background: transparent;
            border: 1px solid #2563eb;
            color: #2563eb;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            font-size: .85rem;
            outline: none;
            border-radius: 0;
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
            {{-- Header Section --}}
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Teacher
                            Access Control</h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500">Permissions</span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="permSearch" placeholder="Search Teacher..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">Filter</button>
                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">Export</button>
                        <button onclick="openModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">Add
                            Permission</button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="permSearchMobile" placeholder="Search Teacher..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Teacher Name</th>
                                <th>Designation</th>
                                <th>Subject Name</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="permTableBody"></tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between p-3 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0</div>
                    <div class="flex items-center gap-1" id="paginationLinks"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Modal --}}
    <div id="filterModal"
        class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12">
        <div class="bg-white p-4 w-full max-w-[320px] shadow-2xl" style="border-radius: 0;">
            <div>
                <h3 class="text-gray-800 text-[13px] font-medium text-center capitalize">Permission Filter</h3>
                <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
            </div>
            <div class="mt-3 mb-4 flex flex-col gap-3">
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                    <select id="classFilter" class="form-input-fixed text-xs h-8">
                        <option value="">Select Class...</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                    <select id="groupFilter" class="form-input-fixed text-xs h-8">
                        <option value="">Select Group...</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                    <select id="sectionFilter" class="form-input-fixed text-xs h-8">
                        <option value="">Select Section...</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button id="resetFilter" class="btn-outline-secondary border w-full text-[11px] h-8">Reset</button>
                <button id="applyFilter" class="btn-outline-premium border w-full text-[11px] h-8">Apply</button>
            </div>
        </div>
    </div>

    {{-- Export Modal --}}
    <div id="exportModal"
        class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12">
        <div class="bg-white p-4 w-auto min-w-[140px] shadow-2xl">
            <div class="flex flex-col gap-1.5">
                <button class="btn-outline-secondary border py-1.5 px-4 text-[10px] tracking-widest w-full">PDF</button>
                <button class="btn-outline-secondary border py-1.5 px-4 text-[10px] tracking-widest w-full">EXCEL</button>
                <button class="btn-outline-secondary border py-1.5 px-4 text-[10px] tracking-widest w-full">PRINT</button>
                <button id="closeExport" class="mt-1 py-1.5 text-[10px] text-gray-400 border w-full">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Grant Permission Modal --}}
    <div id="permModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'permModal')">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Grant Permission
                </h3>
            </div>

            <form id="permForm" class="flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="record_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Select
                                Teacher</label>
                            <select id="teacher_id"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;">
                                <option value="">Choose teacher...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_id" onchange="filterDependents()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;">
                                <option value="">Choose class...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Subject</label>
                            <select id="subject_id"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                required style="border-radius: 0;">
                                <option value="">Choose subject...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_id" onchange="filterSections()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">None</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_id"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">None</option>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
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
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let rawClasses = [],
            rawGroups = [],
            rawSections = [],
            rawSubjects = [],
            rawTeachers = [];

        // Separate data fetching from UI rendering to prevent auto-unselect issues
        async function initData() {
            try {
                const [c, g, s, sub, t] = await Promise.all([
                    axios.get('/api/get-school-classes'),
                    axios.get('/api/get-school-groups'),
                    axios.get('/api/get-school-sections'),
                    axios.get('/api/get-school-subjects'),
                    axios.get('/api/teachers')
                ]);

                rawClasses = c.data.data || [];
                rawGroups = g.data.data || [];
                rawSections = s.data.data || [];
                rawSubjects = sub.data.data || [];
                rawTeachers = t.data.data || [];

                renderStaticDropdowns();
            } catch (error) {
                console.error("Error loading initial data", error);
            }
        }

        function renderStaticDropdowns() {
            const tSel = document.getElementById('teacher_id');
            const currentTeacher = tSel.value;
            tSel.innerHTML = '<option value="">Choose Teacher...</option>';
            rawTeachers.forEach(t => {
                tSel.innerHTML += `<option value="${t.id}">${t.name} (${t.designation})</option>`;
            });
            if (currentTeacher) tSel.value = currentTeacher;

            const clsSel = document.getElementById('class_id');
            const currentClass = clsSel.value;
            clsSel.innerHTML = '<option value="">Choose Class...</option>';
            rawClasses.forEach(c => {
                clsSel.innerHTML += `<option value="${c.id}">${c.class_name}</option>`;
            });
            if (currentClass) clsSel.value = currentClass;
        }

        function filterDependents() {
            const classId = document.getElementById('class_id').value;

            // Filter Groups
            const grpSel = document.getElementById('group_id');
            const currentGrp = grpSel.value;
            grpSel.innerHTML = '<option value="">None</option>';
            rawGroups.filter(g => g.class_id == classId).forEach(g => {
                grpSel.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
            });
            if (currentGrp) grpSel.value = currentGrp;

            // Filter Subjects
            const subSel = document.getElementById('subject_id');
            const currentSub = subSel.value;
            subSel.innerHTML = '<option value="">Choose Subject...</option>';
            rawSubjects.filter(sub => sub.class_id == classId).forEach(sub => {
                subSel.innerHTML += `<option value="${sub.id}">${sub.subject_name}</option>`;
            });
            if (currentSub) subSel.value = currentSub;

            filterSections();
        }

        function filterSections() {
            const classId = document.getElementById('class_id').value;
            const groupId = document.getElementById('group_id').value;
            const secSel = document.getElementById('section_id');
            const currentSec = secSel.value;

            secSel.innerHTML = '<option value="">None</option>';
            rawSections.filter(s => s.class_id == classId && (!groupId || s.group_id == groupId)).forEach(s => {
                secSel.innerHTML += `<option value="${s.id}">${s.section_name}</option>`;
            });
            if (currentSec) secSel.value = currentSec;
        }

        function fetchPermissions(page = 1) {
            const search = document.getElementById('permSearch').value;
            axios.get('/api/teacher-permissions', {
                params: {
                    search,
                    page
                }
            }).then(res => {
                const tbody = document.getElementById('permTableBody');
                const {
                    data,
                    from,
                    to,
                    total,
                    current_page,
                    last_page
                } = res.data;

                // PERFORMANCE FIX: Build string once and inject to DOM once
                let rows = '';
                data.forEach((item, index) => {
                    rows += `
                <tr>
                    <td class="text-gray-400 font-mono">${(from || 0) + index}</td>
                    <td>${item.school_class?.class_name || '-'}</td>
                    <td>${item.school_group?.group_name || '-'}</td>
                    <td>${item.school_section?.section_name || '-'}</td>
                    <td><span>${item.teacher_name}</span></td>
                    <td>${item.teacher_designation}</td>
                    <td>${item.school_subject?.subject_name || '-'}</td>
                    <td>
                        <div class="flex justify-center gap-3">
                            <button onclick="editRecord(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteRecord(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
                });

                tbody.innerHTML = rows;

                document.getElementById('paginationInfo').innerText = `${to || 0} OF ${total || 0}`;
                renderPagination(current_page, last_page);
            });
        }

        function renderPagination(current, last) {
            const container = document.getElementById('paginationLinks');
            container.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = current === 1;
            prevBtn.onclick = () => fetchPermissions(current - 1);
            container.appendChild(prevBtn);

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `pagination-btn ${i === current ? 'active' : ''}`;
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => fetchPermissions(i);
                    container.appendChild(pageBtn);
                } else if (i === current - 2 || i === current + 2) {
                    const dots = document.createElement('span');
                    dots.className = 'px-1 text-gray-400';
                    dots.innerText = '...';
                    container.appendChild(dots);
                }
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = current === last;
            nextBtn.onclick = () => fetchPermissions(current + 1);
            container.appendChild(nextBtn);
        }

        async function openModal() {
            document.getElementById('permForm').reset();
            document.getElementById('record_id').value = '';
            if (rawClasses.length === 0) await initData();
            else renderStaticDropdowns();

            document.getElementById('permModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('permModal').classList.add('hidden');
        }

        document.getElementById('permForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('record_id').value;
            const data = {
                teacher_id: document.getElementById('teacher_id').value,
                class_id: document.getElementById('class_id').value,
                subject_id: document.getElementById('subject_id').value,
                group_id: document.getElementById('group_id').value,
                section_id: document.getElementById('section_id').value,
            };

            const req = id ? axios.put(`/api/teacher-permissions/${id}`, data) : axios.post('/api/teacher-permissions',
                data);
            req.then(() => {
                Toastify({
                    text: id ? "Permission Updated" : "Permission Created",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                fetchPermissions();
            });
        };

        async function editRecord(id) {
            const res = await axios.get(`/api/teacher-permissions/${id}`);
            const item = res.data;
            await openModal();
            document.getElementById('record_id').value = item.id;
            document.getElementById('teacher_id').value = item.teacher_id;
            document.getElementById('class_id').value = item.class_id;

            filterDependents();

            setTimeout(() => {
                document.getElementById('group_id').value = item.group_id || '';
                filterSections();
                document.getElementById('section_id').value = item.section_id || '';
                document.getElementById('subject_id').value = item.subject_id;
            }, 100);
        }

        function deleteRecord(id) {
            Swal.fire({
                title: 'Revoke Permission?',
                text: "The teacher will lose access to this section.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Revoke',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/teacher-permissions/${id}`).then(() => fetchPermissions());
                }
            });
        }

        document.getElementById('permSearch').addEventListener('input', () => fetchPermissions(1));
        document.getElementById('permSearchMobile').addEventListener('input', (e) => {
            document.getElementById('permSearch').value = e.target.value;
            fetchPermissions(1);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => document.getElementById(id).classList.toggle('hidden', !show);
            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('resetFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('applyFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            window.onclick = (event) => {
                if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
            };

            initData();
            fetchPermissions();
        });
    </script>
@endsection