@extends('layouts.admin')

@section('title', 'Dynamic Operations')
@section('page-title', 'Dynamic Operations')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Brutalist/Flat Design */
        .op-card,
        button,
        input,
        select,
        .toastify {
            border-radius: 0 !important;
        }

        .op-card {
            transition: all .3s;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            background: #ffffff;
            margin-bottom: 1.5rem;
        }

        .preview-img {
            width: 100%;
            height: 120px;
            object-fit: contain;
            background: #f9fafb;
            border: 1px solid #f3f4f6;
        }

        .banner-grid-img {
            height: 100px;
            width: 100%;
            object-fit: cover;
            border: 1px solid #f3f4f6;
        }

        /* Overlay fix for absolute icons */
        .asset-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            z-index: 10;
        }

        .group:hover .asset-overlay {
            opacity: 1;
        }

        .toast-success {
            background: #10b981 !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;
        }

        .toast-error {
            background: #ef4444 !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
        }
    </style>

    <div class="p-4 md:p-6 bg-gray-50 min-h-screen">

        <div
            class="bg-white shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Dynamic Operations</h2>
                <p class="text-xs text-gray-500">Configure global site assets and promotional content</p>
            </div>
        </div>

        <div class="op-card">
            <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">General Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach (['brand_title' => 'Brand Title', 'brand_description' => 'Description', 'promotion_text' => 'Promotion Text'] as $id => $label)
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">{{ $label }}</label>
                        <div class="flex">
                            <input type="text" id="{{ $id }}"
                                class="border border-gray-200 px-3 py-2 text-sm w-full outline-none focus:border-blue-500"
                                placeholder="Enter {{ $label }}...">
                            <button onclick="saveText('{{ $id }}')"
                                class="bg-blue-600 text-white px-4 hover:bg-blue-700 transition">
                                <i class="fas fa-save"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ([
                    'brand_logo'             => ['title' => 'Brand Logo',      'size' => '(512 × 512) px'],
                    'school_dashboard_logo'  => ['title' => 'Dashboard Logo',  'size' => '(512 × 512) px'],
                    'brand_banner'           => ['title' => 'Landing Banner',  'size' => '(1080 × 240) px'],
                ] as $field => $info)
                    <div class="op-card mb-0">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-gray-700 uppercase">{{ $info['title'] }}</h3>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $info['size'] }}</span>
                        </div>
                        <div id="{{ $field }}_preview_container" class="mb-3 relative group">
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" id="{{ $field }}_file" class="hidden"
                                onchange="uploadSingle('{{ $field }}')">
                            <button onclick="document.getElementById('{{ $field }}_file').click()"
                                class="w-full py-2 border border-dashed border-gray-300 text-gray-500 hover:bg-gray-50 text-xs font-medium transition">
                                <i class="fas fa-upload mr-1"></i> Select File
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="op-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-gray-700 uppercase">Dashboard Banners</h3>
                    <span class="text-[10px] text-gray-400 font-medium">(2354 × 600) px</span>
                    <input type="file" id="school_banners_input" multiple class="hidden"
                        onchange="uploadMultipleBanners()">
                    <button onclick="document.getElementById('school_banners_input').click()"
                        class="h-8 w-8 bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700 transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <div id="school_banner_list"
                    class="flex flex-col gap-2 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[70]">
        <div class="bg-white shadow-2xl w-full max-w-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold uppercase text-sm">Update Asset</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="fas fa-times"></i></button>
            </div>
            <input type="file" id="modal_file" class="w-full border border-gray-200 p-2 text-sm mb-4">
            <input type="hidden" id="modal_field">
            <div class="flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-[10px] font-bold">CANCEL</button>
                <button onclick="confirmEdit()" class="px-4 py-2 bg-blue-600 text-white text-[10px] font-bold">SAVE
                    CHANGES</button>
            </div>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let settings = {};

        function showToast(msg, type = "success") {
            Toastify({
                text: msg,
                duration: 2500,
                gravity: "top",
                position: "right",
                className: type === "success" ? "toast-success" : "toast-error",
            }).showToast();
        }

        async function loadData() {
            try {
                const res = await axios.get('/api/dynamic-operation');
                settings = res.data;
                ['brand_title', 'brand_description', 'promotion_text'].forEach(id => {
                    if (document.getElementById(id)) document.getElementById(id).value = settings[id] || '';
                });
                renderUI();
            } catch (err) {
                console.error("Load failed", err);
            }
        }

        function renderUI() {
            const storageBase = "/storage/";

            // Single Assets
            const singles = ['brand_logo', 'school_dashboard_logo', 'brand_banner'];
            singles.forEach(field => {
                const container = document.getElementById(`${field}_preview_container`);
                if (!container) return;

                if (settings[field]) {
                    container.innerHTML = `
                        <div class="relative group border border-gray-100 p-1">
                            <img src="${storageBase + settings[field]}" class="preview-img">
                            <div class="asset-overlay">
                                <button onclick="openEdit('${field}')" class="h-8 w-8 bg-white text-blue-600 flex items-center justify-center hover:bg-blue-50 shadow-sm">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <button onclick="deleteSingle('${field}')" class="h-8 w-8 bg-white text-red-600 flex items-center justify-center hover:bg-red-50 shadow-sm">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>`;
                } else {
                    container.innerHTML =
                        `<div class="preview-img flex items-center justify-center text-gray-300 text-[10px] uppercase font-bold italic">No Image</div>`;
                }
            });

            // Multiple Banners
            const bannerList = document.getElementById('school_banner_list');
            if (!bannerList) return;
            bannerList.innerHTML = "";

            if (settings.school_dashboard_banners && Array.isArray(settings.school_dashboard_banners)) {
                settings.school_dashboard_banners.forEach((path, index) => {
                    const div = document.createElement('div');
                    div.className = "flex items-center gap-3 border border-gray-100 p-2";
                    div.innerHTML = `
                        <img src="${storageBase + path}" class="h-12 w-20 object-cover flex-shrink-0 border border-gray-100">
                        <span class="flex-1 text-xs text-gray-400 truncate">Banner ${index + 1}</span>
                        <button onclick="confirmDeleteBanner(${index})" class="h-8 w-8 flex-shrink-0 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition">
                            <i class="fas fa-trash-can text-xs"></i>
                        </button>`;
                    bannerList.appendChild(div);
                });
            }
        }

        function saveText(field) {
            const val = document.getElementById(field).value;
            axios.post('/api/dynamic-operation/update-text', {
                    field,
                    value: val
                }, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(res => {
                    settings = res.data;
                    showToast('Settings saved');
                });
        }

        function uploadSingle(field) {
            const fileInput = document.getElementById(`${field}_file`);
            if (!fileInput.files[0]) return;
            const form = new FormData();
            form.append('image', fileInput.files[0]);
            form.append('field', field);
            axios.post('/api/dynamic-operation/upload-logo', form, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(res => {
                    settings = res.data;
                    renderUI();
                    showToast('Asset uploaded');
                    fileInput.value = "";
                });
        }

        function uploadMultipleBanners() {
            const fileInput = document.getElementById('school_banners_input');
            const files = fileInput.files;
            if (files.length === 0) return;
            const form = new FormData();
            for (let i = 0; i < files.length; i++) form.append('images[]', files[i]);
            axios.post('/api/dynamic-operation/upload-school-banners', form, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(res => {
                    settings = res.data;
                    renderUI();
                    showToast('Banners added');
                    fileInput.value = "";
                });
        }

        function deleteSingle(field) {
            Swal.fire({
                title: 'Delete Asset?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                customClass: {
                    popup: 'rounded-0',
                    confirmButton: 'bg-red-600 px-4 py-2 text-white text-xs mx-1',
                    cancelButton: 'bg-gray-200 px-4 py-2 text-gray-700 text-xs mx-1'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    axios.post('/api/dynamic-operation/delete-image', {
                            field
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        })
                        .then(res => {
                            settings = res.data;
                            renderUI();
                            showToast('Asset removed');
                        });
                }
            });
        }

        function confirmDeleteBanner(index) {
            Swal.fire({
                title: 'Remove Banner?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Remove',
                customClass: {
                    popup: 'rounded-0',
                    confirmButton: 'bg-red-600 px-4 py-2 text-white text-xs mx-1',
                    cancelButton: 'bg-gray-200 px-4 py-2 text-gray-700 text-xs mx-1'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    axios.post('/api/dynamic-operation/delete-school-banner', {
                            index
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        })
                        .then(res => {
                            settings = res.data;
                            renderUI();
                            showToast('Banner removed');
                        });
                }
            });
        }

        function openEdit(field) {
            document.getElementById('modal_field').value = field;
            document.getElementById('editModal').classList.replace('hidden', 'flex');
        }

        function closeModal() {
            document.getElementById('editModal').classList.replace('flex', 'hidden');
            document.getElementById('modal_file').value = "";
        }

        function confirmEdit() {
            const field = document.getElementById('modal_field').value;
            const file = document.getElementById('modal_file').files[0];
            if (!file) return showToast("Select a file", "error");
            const form = new FormData();
            form.append('image', file);
            form.append('field', field);
            axios.post('/api/dynamic-operation/upload-logo', form, {
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(res => {
                    settings = res.data;
                    renderUI();
                    closeModal();
                    showToast('Asset updated');
                });
        }

        loadData();
    </script>
@endsection