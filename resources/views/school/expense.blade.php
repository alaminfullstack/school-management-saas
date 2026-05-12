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
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto !important;
            display: block;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th {
            padding: 12px 8px !important;
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 2px solid #edf2f7;
            color: #64748b;
        }

        td {
            padding: 8px 8px !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
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
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 4px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
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
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-4 mb-4" style="border-radius:0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        {{-- Dynamic Page Title using the current route name / menu name --}}
                        <h2 id="pageHeader" class="text-xl font-bold text-gray-800 leading-tight"></h2>

                        <div class="flex items-center text-slate-400 text-xs mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1 text-[8px]"></i>
                            <span id="pageTitle" class="font-medium text-slate-500"></span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative w-full sm:w-56">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="expenseSearch" placeholder="Search expense..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius:0;">
                        </div>
                        <button id="openExpenseModal"
                            class="btn-outline-premium px-5 py-2 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="mdi mdi-plus"></i> Add Expense
                        </button>
                        <button onclick="exportExpense()"
                            class="btn-outline-secondary px-5 py-2 text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="mdi mdi-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table class="min-w-[800px] text-[11px]">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Date</th>
                                <th>Expense Reason</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">Showing 0
                        to 0 of 0 entries</div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="expenseModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] p-2 sm:p-4 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[95vh]">

            <div class="px-5 py-3 border-b flex justify-between items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="mdi mdi-cash-minus text-xl"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-gray-800 uppercase text-sm leading-tight">Add Expense</h3>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Finance Desk</p>
                    </div>
                </div>
                <button type="button" onclick="closeExpenseModal()"
                    class="text-gray-400 hover:text-black p-1 transition-colors">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form id="expenseForm" class="flex flex-col overflow-hidden m-0">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow">
                    <input type="hidden" id="expense_id">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Transaction Date</label>
                            <input type="date" id="date" required class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1">
                            <label class="text-[9px] font-bold text-blue-600 uppercase mb-1 block">Amount (BDT)</label>
                            <input type="number" id="amount" placeholder="0.00" required
                                class="form-input-fixed w-full border-blue-50 focus:border-blue-400">
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Payee Name</label>
                            <input type="text" id="name" placeholder="Name of person/vendor" required
                                class="form-input-fixed w-full">
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Description</label>
                            <input type="text" id="expense_reason" placeholder="e.g. Utility Bill, Stationery" required
                                class="form-input-fixed w-full">
                        </div>

                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeExpenseModal()"
                        class="w-full sm:w-auto btn-outline-secondary px-6 py-2 text-[10px] uppercase tracking-widest order-2 sm:order-1">
                        <i class="mdi mdi-close-circle-outline text-sm"></i>
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn"
                        class="w-full sm:w-auto btn-outline-premium px-8 py-2 text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 order-1 sm:order-2">
                        <i class="mdi mdi-check-decagram-outline text-sm"></i>
                        Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        const expenseModal = document.getElementById('expenseModal');

        document.getElementById('openExpenseModal').onclick = () => {
            document.getElementById('expenseForm').reset();
            document.getElementById('expense_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add Expense';
            expenseModal.classList.remove('hidden');
        }

        function closeExpenseModal() {
            expenseModal.classList.add('hidden');
        }

        function fetchExpenses(page = 1) {
            currentPage = page;
            const search = document.getElementById('expenseSearch').value;
            axios.get('/api/expenses', {
                    params: {
                        search,
                        page
                    }
                })
                .then(res => {
                    const expenses = res.data.data;
                    const meta = res.data;
                    const tbody = document.getElementById('expenseTableBody');
                    tbody.innerHTML = '';
                    if (expenses.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="6" class="text-center py-6 text-gray-400">No records found</td></tr>`;
                        return;
                    }
                    expenses.forEach((e, index) => {
                        tbody.innerHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td>${meta.from+index}</td>
                            <td>${e.date}</td>
                            <td>${e.expense_reason}</td>
                            <td>${e.name}</td>
                            <td class="font-bold text-red-600">${e.amount}</td>
                            <td>
                                <div class="flex justify-center gap-2">
                                    <button onclick="editExpense(${e.id})" class="action-icon-btn text-amber-500"><i class="mdi mdi-pencil-box-outline"></i></button>
                                    <button onclick="deleteExpense(${e.id})" class="action-icon-btn text-red-500"><i class="mdi mdi-trash-can-outline"></i></button>
                                </div>
                            </td>
                        </tr>`;
                    });
                    renderPagination(meta);
                });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            info.innerText = `Showing ${meta.from||0} to ${meta.to||0} of ${meta.total} entries`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchExpenses(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page===i?'active':''}`;
                btn.innerText = i;
                btn.onclick = () => fetchExpenses(i);
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchExpenses(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('expenseSearch').addEventListener('input', () => fetchExpenses(1));

        document.getElementById('expenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('expense_id').value;
            const saveBtn = document.getElementById('saveBtn');

            const data = {
                date: document.getElementById('date').value,
                expense_reason: document.getElementById('expense_reason').value,
                name: document.getElementById('name').value,
                amount: document.getElementById('amount').value
            };

            saveBtn.disabled = true;

            // Match api.php: Route::post('/expenses/{id}') for update
            const url = id ? `/api/expenses/${id}` : '/api/expenses';

            axios.post(url, data)
                .then(() => {
                    Toastify({
                        text: id ? "Expense updated" : "Expense saved",
                        style: {
                            background: "#10b981"
                        }
                    }).showToast();
                    closeExpenseModal();
                    fetchExpenses(currentPage);
                }).catch(err => {
                    console.error(err.response.data);
                    const errorMsg = err.response.data.message || 'Check validation errors';
                    Swal.fire('Error', 'Could not save record: ' + errorMsg, 'error');
                }).finally(() => {
                    saveBtn.disabled = false;
                });
        });

        function editExpense(id) {
            axios.get('/api/expenses/' + id).then(res => {
                const e = res.data;
                document.getElementById('expense_id').value = e.id;
                document.getElementById('date').value = e.date;
                document.getElementById('expense_reason').value = e.expense_reason;
                document.getElementById('name').value = e.name;
                document.getElementById('amount').value = e.amount;
                document.getElementById('modalTitle').innerText = "Edit Expense";
                expenseModal.classList.remove('hidden');
            });
        }

        function deleteExpense(id) {
            Swal.fire({
                title: 'Delete?',
                text: "Permanent action",
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/expenses/' + id).then(() => {
                        Toastify({
                            text: "Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchExpenses(currentPage);
                    });
                }
            });
        }

        function exportExpense() {
            window.open('/api/expenses-export', '_blank');
        }
        fetchExpenses();
    </script>
@endsection
