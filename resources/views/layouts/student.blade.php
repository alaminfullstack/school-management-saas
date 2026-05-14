<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 16rem;
            display: flex;
            flex-direction: column;
            z-index: 10;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            transition: transform .35s cubic-bezier(.4, 0, .2, 1);
        }

        @media(min-width:768px) {
            .sidebar {
                transform: translateX(0) !important;
            }
        }

        @media(max-width:767px) {
            .sidebar {
                top: 4rem;
                height: calc(100vh - 4rem);
            }
        }

        .sidebar-header-divider {
            border-bottom: 1px solid #e5e7eb;
            margin: 0 1.5rem;
        }

        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle {
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .sidebar-item,
        .sidebar-group-toggle,
        .nav-header {
            padding-left: 14px;
        }

        .sidebar-item {
            font-weight: 500;
            gap: 12px;
        }

        /* Groups */
        .sidebar-group {
            margin-bottom: 8px;
        }

        .sidebar-group-toggle {
            justify-content: space-between;
            font-weight: 500;
            padding-right: 14px;
            border-radius: 10px;
        }

        .sidebar-group-toggle span {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        /* Subitems */
        .sidebar-subitem {
            padding-left: 36px;
            font-weight: 400;
            position: relative;
            gap: 12px;
        }

        .sidebar-group-content {
            display: none;
            flex-direction: column;
            margin-top: 4px;
            position: relative;
        }

        .sidebar-group.open .sidebar-group-content {
            display: flex;
        }

        /* Vertical line */
        .sidebar-group-content::before {
            content: '';
            position: absolute;
            left: 26px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        /* Dot indicator */
        .sidebar-subitem::before {
            content: '';
            position: absolute;
            left: 21px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .sidebar-subitem.active::before {
            background: #1e40af;
        }

        /* Icons */
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle i {
            width: 16px;
            flex-shrink: 0;
        }

        /* Hover */
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #f1f5f9;
            color: #1e40af;
        }

        /* Active */
        .sidebar-item.active,
        .sidebar-subitem.active {
            background: #eaf1ff;
            color: #1e40af;
            box-shadow: 0 4px 12px rgba(30, 64, 175, .08);
        }

        .sidebar-item.active i,
        .sidebar-subitem.active i {
            color: #1e40af;
        }

        /* Headers */
        .nav-header {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-top: 1.5rem;
            margin-bottom: .5rem;
        }

        /* Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Mobile sidebar */
        .sidebar-mobile-hidden {
            transform: translateX(-100%);
        }

        .sidebar-mobile-show {
            transform: translateX(0);
        }

        /* Topbar */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            height: 4rem;
            left: 0;
            right: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        @media(min-width:768px) {
            .topbar {
                left: 16rem;
                width: calc(100% - 16rem);
                padding: 0 2rem;
            }
        }

        .main-scroll {
            overflow-y: auto;
            height: 100vh;
            padding-top: 4rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media(min-width:768px) {
            .main-scroll {
                padding-left: 2rem;
                padding-right: 2rem;
                margin-left: 16rem;
            }
        }

        .sidebar .logout-wrapper {
            margin-top: auto;
            margin-bottom: 2rem;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            z-index: 50;
        }

        .dropdown-menu.show {
            display: block;
        }

        .sidebar-group-toggle i {
            transition: transform .25s ease;
        }

        .sidebar-group.open .sidebar-group-toggle i {
            transform: rotate(90deg);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="flex">

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar sidebar-mobile-hidden">

            <!-- Logo -->
            <div class="p-6 text-center">
                <h1 class="text-2xl font-black tracking-tight text-slate-800">
                    SCHOOL<span class="text-blue-600">SAAS</span>
                </h1>
                <p class="text-[10px] font-bold uppercase tracking-[3px] text-slate-400">
                    Student Panel
                </p>
            </div>

            <div class="sidebar-header-divider"></div>

            <nav class="flex-1 overflow-y-auto custom-scrollbar p-3" id="menuNav">

                <a href="{{ route('student.dashboard') }}" data-title="Dashboard" data-link class="sidebar-item">
                    <i class="fas fa-th-large w-4"></i> Dashboard
                </a>

                <p class="nav-header">Teacher</p>

                <a href="{{ route('student.teacher.list') }}" data-title="Teacher List" data-link class="sidebar-item">
                    <i class="fas fa-chalkboard-teacher w-4"></i> Teacher List
                </a>

                <p class="nav-header">Student</p>

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-user-graduate w-4"></i> Student Section</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                    <div class="sidebar-group-content">
                        <a href="{{ route('student.student.list') }}" data-title="Student List" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-list"></i> Student List
                        </a>
                        <a href="{{ route('student.class.time') }}" data-title="Class Time" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-clock"></i> Class Time
                        </a>
                        <a href="{{ route('student.class.promote') }}" data-title="Class Promote" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-level-up-alt"></i> Class Promote
                        </a>
                        <a href="{{ route('student.assignment') }}" data-title="Assignment" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-book"></i> Assignment
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Logout -->
            <div class="p-4 logout-wrapper">
                <button id="logoutBtn"
                    class="w-full flex items-center justify-center gap-2 p-2 border border-red-500 text-red-500 rounded-lg text-sm font-semibold hover:bg-red-50">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>

        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">

            <!-- Topbar -->
            <header class="topbar">
                <div class="flex items-center gap-3">
                    <button id="hamburger" class="md:hidden text-blue-600 text-xl">
                        <i id="hamburgerIcon" class="fas fa-bars"></i>
                    </button>

                    <div class="flex items-center text-slate-500 text-sm">
                        <span>Student</span>
                        <i class="fas fa-chevron-right mx-2 text-[10px]"></i>
                        <span id="pageTitle" class="font-semibold text-slate-800">
                            @yield('page-title', 'Dashboard')
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <button id="profileBtn" class="flex items-center gap-2">
                        <img src="{{ auth()->user()->profile_image ? asset('profile_images/' . auth()->user()->profile_image) : '/images/avatar.png' }}"
                            class="w-8 h-8 rounded-full object-cover">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <ul id="profileMenu" class="dropdown-menu bg-white shadow-lg rounded-lg py-2 w-48 text-sm">
                        <li><a href="#" onclick="openPasswordModal()" class="px-4 py-2 hover:bg-gray-100 block">Change Password</a></li>
                        <li><a href="#" class="px-4 py-2 hover:bg-gray-100 block">Profile</a></li>
                        <li><a href="#" class="px-4 py-2 hover:bg-gray-100 block">Settings</a></li>
                        <li>
                            <hr class="my-1">
                        </li>
                        <li><a href="#" id="logoutBtnDropdown"
                                class="px-4 py-2 hover:bg-gray-100 block">Logout</a></li>
                    </ul>
                </div>
            </header>

            <!-- Page Content -->
            <main class="main-scroll" id="mainContent">
                @yield('content')
            </main>
        </div>

    </div>

    <div class="modal fade premium-modal" id="passwordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mx-auto" style="width: 95%; max-width: 480px;">
            <div class="modal-content modal-content-sharp shadow-2xl"
                style="border-radius: 0; border: 1.5px solid #1e293b !important; background: #fff;">

                <form id="passwordChangeForm">
                    @csrf
                    <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-slate-900 text-white flex-shrink-0 flex items-center justify-center">
                                <i class="fas fa-lock text-md"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-[11px] font-black text-gray-800 uppercase tracking-widest">Change Password</h3>
                            </div>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-800 transition-colors shadow-none"
                            data-bs-dismiss="modal">
                            <i class="mdi mdi-close text-lg"></i>
                        </button>
                    </div>

                    <div class="modal-body p-4 space-y-3">
                        <div class="w-full">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password" id="currentPassInput" class="form-control"
                                    style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; width: 100%; box-sizing: border-box;">
                                <i class="fas fa-eye toggle-password" data-target="currentPassInput" style="position: absolute; right: 10px; top: 9px; cursor: pointer; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                        </div>
                        <div class="w-full">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">New Password</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="newPassInput" class="form-control"
                                    style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; width: 100%; box-sizing: border-box;">
                                <i class="fas fa-eye toggle-password" data-target="newPassInput" style="position: absolute; right: 10px; top: 9px; cursor: pointer; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                        </div>
                        <div class="w-full">
                            <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block tracking-widest">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="new_password_confirmation" id="confirmNewPassInput" class="form-control"
                                    style="border: 1.5px solid #cbd5e1 !important; height: 34px; padding: 0 10px; font-size: 12px; outline: none; border-radius: 0; width: 100%; box-sizing: border-box;">
                                <i class="fas fa-eye toggle-password" data-target="confirmNewPassInput" style="position: absolute; right: 10px; top: 9px; cursor: pointer; color: #94a3b8; font-size: 11px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 border-t border-gray-100 p-3 bg-gray-50/50">
                        <button type="button"
                            class="w-full sm:w-auto px-4 py-2 text-[9px] font-black uppercase tracking-widest transition-all"
                            style="border: 1.5px solid #64748b !important; background: transparent; color: #64748b; border-radius: 0;"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="savePasswordBtn"
                            class="w-full sm:w-auto px-6 py-2 text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2"
                            style="border: 1.5px solid #2563eb !important; background: #2563eb; color: #ffffff; border-radius: 0;">
                            <i class="fas fa-check-circle-outline text-xs"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar')
        const hamburger = document.getElementById('hamburger')
        const icon = document.getElementById('hamburgerIcon')
        const pageTitle = document.getElementById('pageTitle')
        const menuNav = document.getElementById('menuNav')
        const logoutBtn = document.getElementById('logoutBtn')
        const profileBtn = document.getElementById('profileBtn')
        const profileMenu = document.getElementById('profileMenu')
        const passwordChangeForm = document.getElementById('passwordChangeForm')

        /* Mobile toggle */
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-mobile-show')
            sidebar.classList.toggle('sidebar-mobile-hidden')
            icon.classList.toggle('fa-bars')
            icon.classList.toggle('fa-times')
        })

        /* Sidebar group toggle */
        document.querySelectorAll('.sidebar-group-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const group = toggle.parentElement
                group.classList.toggle('open')
            })
        })

        /* Highlight active menu */
        function highlightActiveMenu() {
            const links = menuNav.querySelectorAll('[data-link]')
            const current = window.location.pathname

            links.forEach(link => {
                link.classList.remove('active')
                if (current === new URL(link.href).pathname) {
                    link.classList.add('active')
                    pageTitle.innerText = link.getAttribute('data-title')
                    const parent = link.closest('.sidebar-group')
                    if (parent) parent.classList.add('open')
                    menuNav.scrollTop = link.offsetTop - 100
                }
            })
        }

        window.addEventListener('DOMContentLoaded', highlightActiveMenu)

        /* Profile dropdown */
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation()
                profileMenu.classList.toggle('show')
            })

            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('show')
                }
            })
        }

        function openPasswordModal() {
            new bootstrap.Modal(document.getElementById('passwordModal')).show();
        }

        document.querySelectorAll('.toggle-password').forEach(iconEl => {
            iconEl.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target)
                if (!input) return
                if (input.type === 'password') {
                    input.type = 'text'
                    this.classList.remove('fa-eye')
                    this.classList.add('fa-eye-slash')
                } else {
                    input.type = 'password'
                    this.classList.remove('fa-eye-slash')
                    this.classList.add('fa-eye')
                }
            })
        })

        if (passwordChangeForm) {
            passwordChangeForm.addEventListener('submit', async (e) => {
                e.preventDefault()
                const btn = document.getElementById('savePasswordBtn')
                const originalText = btn.innerHTML
                btn.disabled = true
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...'

                try {
                    const formData = new FormData(passwordChangeForm)
                    const res = await fetch('/api/change-password', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    const data = await res.json()

                    if (res.ok && data.success) {
                        new bootstrap.Modal(document.getElementById('passwordModal')).hide()
                        passwordChangeForm.reset()
                        Swal.fire({
                            icon: 'success',
                            title: data.message || 'Password updated',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        })
                    } else {
                        let errorMsg = data.message || 'Password update failed'
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>')
                        }
                        throw new Error(errorMsg)
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: error.message || 'Unable to update password.'
                    })
                } finally {
                    btn.disabled = false
                    btn.innerHTML = originalText
                }
            })
        }

        /* Logout */
        logoutBtn.addEventListener('click', logoutUser)
        document.getElementById('logoutBtnDropdown')?.addEventListener('click', logoutUser)

        async function logoutUser() {
            try {
                const res = await fetch('{{ url('/api/logout') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                const data = await res.json()
                window.location.href = data.redirect ?? '/'
            } catch (e) {
                alert('Logout failed! Please try again.')
            }
        }
    </script>

    @stack('scripts')
</body>

</html>
