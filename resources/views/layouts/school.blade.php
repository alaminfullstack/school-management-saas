<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Dashboard')</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">



    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f6fb;
            margin: 0;
        }

        .show {
            display: block !important;
        }

        /* Sidebar */
        .sidebar {
            background: #2C2421;
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


        /* CSS FOR INCREASE OR DECREASE SPACE BETWEEN MENU SUBMENU */
        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle {
            margin-bottom: 12px;
            /* INCREASED from 6px */
            color: rgb(255, 255, 255);
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            margin-left: 8px !important;
            margin-right: 5px !important;
            padding-top: 5px;
            /* ADDED: Vertical Padding for taller hover box */
            padding-bottom: 5px;
            /* ADDED: Vertical Padding for taller hover box */
        }


        /* SIDEBAR SUBMENU SPACE & HOVER BOX SIZE , START POINT FIX DESKTOP MODE*/
        .sidebar-subitem {
            margin-left: 36px !important;
            /* This creates the indentation */
            padding-left: 10px !important;
            /* This is the space between hover edge and icon */
            margin-right: 5px !important;
            font-weight: 400;
            position: relative;
            gap: 12px;
            margin-bottom: 12px;
            transition: none !important;
            display: flex;
            align-items: center;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        /* SIDEBAR SUBMENU SPACE & HOVER BOX SIZE , START POINT FIX MOBILE MODE*/
        @media (max-width: 767px) {

            .sidebar-item,
            .sidebar-group-toggle {
                margin-left: 12px !important;
                margin-right: 12px !important;
                padding-left: 10px !important;
                width: auto;
            }

            /* Forces submenus to stay indented while keeping hover box tight */
            .sidebar-subitem {
                margin-left: 36px !important;
                /* Indent the whole element */
                margin-right: 12px !important;
                padding-left: 10px !important;
                /* Hover box starts 10px before the icon */
                width: auto;
            }
        }

        /* Hover Left Padding Of Menus*/
        .sidebar-item,
        .sidebar-group-toggle,
        .nav-header {
            padding-left: 10px !important;
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


        /* Vertical line */
        .sidebar-group-content {
            display: none;
            flex-direction: column;
            margin-top: 4px;
            position: relative;
        }

        .sidebar-group.open .sidebar-group-content {
            display: flex;
        }


        /* Vertical connection line removed */
        .sidebar-group-content::before {
            display: none;
        }


        /* Dot indicators removed */
        .sidebar-subitem::before,
        .sidebar-subitem.active::before {
            display: none;
        }

        /* Icons */
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle i {
            width: 16px;
            flex-shrink: 0;
        }

        /* Premium Hover State */
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #ff8c00 !important;
            /* Orange background on hover */
            color: #1e40af !important;
            /* Blue text on hover */
            transition: none !important;
        }

        /* Specific Icon swap on hover */
        .sidebar-item:hover i,
        .sidebar-subitem:hover i,
        .sidebar-group-toggle:hover span i:first-child {
            background: transparent !important;
            /* Blue box on hover */
            color: #ffffff !important;
            /* White icon on hover for "Elite" contrast */
            border-color: transparent !important;
        }


        /* Active */
        .sidebar-item.active,
        .sidebar-subitem.active {
            background: #fc8208;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 64, 175, .08);
        }

        .sidebar-item.active i,
        .sidebar-subitem.active i {
            color: #1e40af;
        }

        /* Circular icon containers - Blue box with Orange icon */
        .sidebar-subitem i,
        .sidebar-item i,
        .sidebar-group-toggle span i:first-child {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 24px;
            /* Slightly smaller since no border needed */
            height: 24px;
            background: transparent !important;
            /* Removed background */
            border: none !important;
            /* Removed border */
            color: #ffffff !important;
            /* Icon remains white */
            font-size: 1.1rem;
            /* Slightly larger for visibility */
            flex-shrink: 0;
        }

        /* ================= Sidebar Polishing ================= */

        /* Ensure main items align perfectly with group titles */
        .sidebar-item {
            padding-left: 14px;
            /* already set */
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Add consistent spacing between groups */
        .sidebar-group {
            margin-bottom: 20px;
            /* increased from 8px for better spacing */
        }

        /* Optional: separate dashboard from first group slightly */
        .sidebar-item:first-child {
            margin-bottom: 12px;
        }

        /* Optional: make subitem icons and text aligned with main items */
        .sidebar-subitem {
            padding-left: 36px;
            /* already set */
            gap: 12px;
        }

        /* Remove extra left shift of Dashboard icon */
        .sidebar-item i {
            margin-left: 0;
        }

        /* Flat high-performance hover state */
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover {
            background: #ff8c00 !important;
            /* Subtle tint */
            color: #ffffff !important;
            /* Your premium orange */
            transition: none !important;
        }

        /* Optional: slightly round the group toggle for polish */
        .sidebar-group-toggle {
            border-radius: 12px;
            padding-left: 14px;
            padding-right: 14px;
        }

        /* --- REMOVE BORDER RADIUS ON HOVER & ACTIVE STATES --- */

        /* Target every clickable item in the sidebar */
        .sidebar-item,
        .sidebar-subitem,
        .sidebar-group-toggle,
        .sidebar-item:hover,
        .sidebar-subitem:hover,
        .sidebar-group-toggle:hover,
        .sidebar-item.active,
        .sidebar-subitem.active {
            border-radius: 0 !important;
            /* Force sharp corners */
        }

        /* Specific fix for the group toggle which had 12px set previously */
        .sidebar-group-toggle {
            border-radius: 0 !important;
        }

        /* Ensure the active state box is also sharp */
        .sidebar-item.active,
        .sidebar-subitem.active {
            border-radius: 0 !important;
            box-shadow: none !important;
            /* Optional: remove shadow for a flatter, cleaner elite look */
        }

        /* Optional: extra space before group headers (if using nav-header) */
        .nav-header {
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }

        /* Move Dashboard text slightly left */
        .sidebar-item:first-child {
            padding-left: 8px;
            /* reduce left padding for Dashboard */
        }

        /* Keep the icon same size but align it left nicely */
        .sidebar-item:first-child i {
            margin-left: 0;
        }

        /* Arrow stays normal, no border */
        .sidebar-group-toggle i:last-child {
            border: none;
            background: transparent;
            width: auto;
            height: auto;
            font-size: 0.65rem;
            /* optional smaller size */
        }


        /* Keeps dashboard text aligned on mobile */
        .sidebar-item:first-child {
            padding-left: 10px !important;
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

        .logout-wrapper button {
            border-radius: 0 !important;
        }

        .global-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 40;
            display: none;
        }

        html {
            scroll-behavior: smooth;
        }


        /* Only rotate the Chevron (last icon), keep the circular icon static */
        .sidebar-group-toggle i:last-child {
            transition: transform .25s ease;
        }

        .sidebar-group.open .sidebar-group-toggle i:last-child {
            transform: rotate(90deg);
        }

        /* Force the main circular icon to stay upright */
        .sidebar-group.open .sidebar-group-toggle i:first-child {
            transform: none !important;
        }

        /* Dropdown */
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

        /* Modal Styles */
        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .preview-container {
            width: 100px;
            height: 100px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* NEWLY ADDED CSS */

        /* Smaller circular topbar buttons */
        .topbar button a {
            width: 32px;
            /* 8rem ~ 32px */
            height: 32px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar button i {
            font-size: 0.875rem;
            /* Slightly smaller icon */
        }

        /* Hamburger button mobile circular border */
        #hamburger {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        #hamburger i {
            font-size: 0.875rem;
            /* match topbar icons */
        }

        /* Update this specific block in your <style> */
        .topbar .flex.items-center.gap-2 a,
        .topbar .flex.items-center.gap-2 button {
            width: 32px !important;
            /* Matches your #hamburger size */
            height: 32px !important;
            /* Matches your #hamburger size */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50% !important;
        }

        .topbar .flex.items-center.gap-2 i {
            font-size: 0.875rem !important;
            /* Matches #hamburger icon size */
        }


        /* --- OVERRIDE: Global Subscription Box (Orange Version) --- */
        .subscription-box {
            /* Premium Orange to Pink Gradient */
            background: linear-gradient(135deg, #ff8c00 0%, #ed64a6 100%) !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 1.25rem !important;
            transition: all 0.3s ease;
            margin-left: 7px !important;
            margin-right: 7px !important;
            margin-top: 50px !important;
            margin-bottom: 50px !important;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.2);
        }

        /* Force all internal text to Blue */
        .subscription-box .nav-header {
            color: #ffffff !important;
            /* Deep Navy Blue */
            font-size: 10px !important;
            text-transform: uppercase !important;
            margin-bottom: 8px !important;
            font-weight: 700 !important;
            opacity: 0.9;
            margin-left: 0px !important;
            /* Ensure margin is zero */
            padding-left: 0px !important;
            /* Ensure padding is zero */
            display: block;
            /* Ensures it behaves as a full-width block */
        }

        .subscription-box #sidePlanName {
            color: #ffffff !important;
            /* Deep Navy Blue */
            font-weight: 800 !important;
        }

        .subscription-box #sideExpiryDate {
            color: #ffffff !important;
            /* Deep Navy Blue */
            font-size: 10px !important;
            opacity: 0.8;
        }

        /* Progress Bar: Blue on a Light Ghostly Track */
        .subscription-box .bg-gray-200 {
            background-color: rgba(30, 64, 175, 0.15) !important;
            /* Ghostly Blue track */
            height: 4px !important;
            border: none !important;
        }

        .subscription-box #sideProgressBar {
            background-color: #1e40af !important;
            /* Solid Navy Blue bar */
            box-shadow: 0 0 5px rgba(30, 64, 175, 0.3);
        }

        /* The Upgrade Button: Solid Navy Blue */
        .subscription-box .upgrade-btn {
            background-color: #1e40af !important;
            /* Navy Blue */
            color: #ffffff !important;
            /* White text for readability on blue */
            border-radius: 0 !important;
            border: none !important;
            font-weight: 900 !important;
            width: 100% !important;
            padding: 7px !important;
            margin-top: 5px !important;
            transition: transform 0.2s ease !important;
        }

        .subscription-box .upgrade-btn:hover {
            background-color: #1d4ed8 !important;
            /* Slightly lighter blue on hover */
            transform: scale(1.02);
        }

        /* --- COLLAPSED RAIL STATE --- */
        @media (min-width: 768px) {
            .collapsed-mode .subscription-box {
                width: 50px !important;
                height: 50px !important;
                margin: 10px auto !important;
                /* PERFECTLY CENTERED */
                padding: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            /* Hide everything except the icon button in rail mode */
            .collapsed-mode .subscription-box .plan-info>p,
            .collapsed-mode .subscription-box .bg-gray-200,
            .collapsed-mode .subscription-box .upgrade-btn span {
                display: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn {
                width: 100% !important;
                height: 100% !important;
                background: transparent !important;
                /* Let gradient show through */
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .collapsed-mode .subscription-box .upgrade-btn i {
                font-size: 1.25rem !important;
                color: #1e40af !important;
                /* Icon stays blue */
            }
        }

        /* Sidebar Base - Add Transition */
        .sidebar {
            transition: width .35s cubic-bezier(.4, 0, .2, 1), transform .35s cubic-bezier(.4, 0, .2, 1) !important;
        }


        /* DESKTOP RAIL MODE (768px+) */
        @media (min-width: 768px) {

            /* When collapsed class is present */
            .collapsed-mode .sidebar {
                width: 75px !important;
            }

            /* FIX: Ensure Topbar and Main Content align to the new sidebar width */
            .collapsed-mode .topbar {
                left: 75px !important;
                width: calc(100% - 75px) !important;
            }

            .collapsed-mode .main-scroll {
                margin-left: 75px !important;
                /* Changed from left: 75px to margin-left for better stability */
                width: calc(100% - 75px) !important;
                padding-left: 2rem;
                padding-right: 2rem;
            }

            /* Hide text and elements - Use opacity and visibility for smoother transitions */
            .collapsed-mode .sidebar span,
            .collapsed-mode .sidebar .fa-chevron-right,
            .collapsed-mode .sidebar .nav-header,
            .collapsed-mode .sidebar .subscription-box,
            .collapsed-mode .sidebar .sidebar-header-divider,
            .collapsed-mode .sidebar-item {
                font-size: 0;
                white-space: nowrap;
            }

            /* Specifically hide the dropdown arrow in collapsed mode */
            .collapsed-mode .sidebar .sidebar-group-toggle i:last-child {
                display: none;
            }

            /* Center the icons in the rail */
            .collapsed-mode .sidebar-item,
            .collapsed-mode .sidebar-group-toggle {
                justify-content: center !important;
                padding: 5px 15px !important;
                /* Balanced vertical padding, zero horizontal */
                margin-left: 10px !important;
                /* Add small margins so the hover box doesn't touch sidebar edges */
                margin-right: 10px !important;
                gap: 0 !important;
                /* Remove the gap so icon centers perfectly */
                width: auto !important;
            }

            /* Center the icons exactly in the middle of the hover box */
            .collapsed-mode .sidebar-item i,
            .collapsed-mode .sidebar-group-toggle span i:first-child {
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
            }

            /* Hide sub-menu items entirely */
            .collapsed-mode .sidebar-group-content {
                display: none !important;
            }

            /* --- 1. Symmetrical Logo Container --- */
            .collapsed-mode .sidebar .p-6 {
                padding: 20px 0 !important;
                /* Increased vertical space for premium feel */
                display: flex !important;
                justify-content: center !important;
                margin: 0 !important;
                /* Remove any inherited side margins */
            }

            .collapsed-mode .sidebar .w-full.h-20 {
                width: 50px !important;
                height: 50px !important;
                margin: 0 auto !important;
                border: 1px solid #e5e7eb;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            /* --- 2. Symmetrical Subscription Box (Orange Gradient) --- */
            .collapsed-mode .subscription-box {
                width: 50px !important;
                height: 50px !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                /* Premium Orange/Gold Gradient */
                background: linear-gradient(135deg, #ff8c00 0%, #ed64a6 100%) !important;
                border: none !important;
                box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3);
                /* Optional: Soft glow */
                margin-left: 0px !important;
            }

            /* Hide all text/progress bars inside the box */
            .collapsed-mode .subscription-box .plan-info>p,
            .collapsed-mode .subscription-box .bg-gray-200 {
                display: none !important;
            }

            /* --- 3. Transform Upgrade Button --- */
            .collapsed-mode .subscription-box .plan-info {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .collapsed-mode .subscription-box .upgrade-btn {
                width: 100% !important;
                height: 100% !important;
                background: transparent !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                border: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn span {
                display: none !important;
            }

            .collapsed-mode .subscription-box .upgrade-btn i {
                font-size: 1.25rem !important;
                /* Prominent icon */
                margin: 0 !important;
                color: white !important;
            }

            .topbar,
            .main-scroll {
                transition: all .35s cubic-bezier(.4, 0, .2, 1);
            }

           

            #profileMenu {
                display: none;
                position: absolute;
                right: 0;
                top: 100%;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                min-width: 200px;
                z-index: 1000;
            }

             /* Profile Dropdown */
            #profileMenu.show {
                display: block !important;
            }

            #profileBtn {
                cursor: pointer;
            }

            /* Password Modal Styles */
            .toggle-password {
                cursor: pointer;
                color: #94a3b8;
                font-size: 11px;
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
            }
        }
    </style>

    @stack('styles')
</head>

@php
    // Fetch School Record for Logo
    $school = \App\Models\School::where('user_id', auth()->id())->first();
    $schoolLogo = $school && $school->logo ? asset('storage/' . $school->logo) : asset('images/school_default.png');
@endphp

<body>

    <div class="flex">

        <aside id="sidebar" class="sidebar sidebar-mobile-hidden">

            <div class="p-6 text-center">
                <div class="w-full h-20 flex items-center justify-center overflow-hidden border border-gray-300 bg-white"
                    style="border-radius: 0;">
                    <img id="sideSchoolLogo" src="{{ asset('images/aasthaacademiclogo.jpeg') }}" alt="School Logo"
                        class="w-full h-full object-contain" style="display: block;">
                </div>
            </div>


            <div class="sidebar-header-divider"></div>

            <nav class="flex-1 overflow-y-auto custom-scrollbar p-3" id="menuNav">

                <a href="{{ route('school.dashboard') }}" data-title="Dashboard" data-link class="sidebar-item">
                    <i class="hgi hgi-stroke hgi-rounded hgi-dashboard-browsing w-4"></i>
                    Dashboard
                </a>

                {{-- <p class="nav-header">Teacher Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-teacher w-4"></i>
                            Teacher List
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.teacher-registration') }}" data-title="Teacher Registration" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-add-01"></i>
                            Teacher
                        </a>

                        <a href="{{ route('school.class-permission') }}" data-title="Class Permission" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-lock-password"></i>
                            Permission
                        </a>

                        <a href="{{ route('school.teacher-id-card') }}" data-title="Teacher ID Card" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                            Id Card
                        </a>

                        <a href="{{ route('school.teacher-attendance') }}" data-title="Teacher Attendance" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-check-in-01"></i>
                            Attendance
                        </a>

                    </div>
                </div>


                <!-- ================= Student Management ================= -->
                {{-- <p class="nav-header">Student Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-student w-4"></i>
                            Student List
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.students') }}" data-title="Student Lists" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-group"></i>
                            Admission
                        </a>

                        <a href="{{ route('school.student-promote') }}" data-title="Promote Students" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-arrow-up-01"></i>
                            Promote
                        </a>

                        <a href="{{ route('school.student-class-time') }}" data-title="Class Time" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-clock-01"></i>
                            Class Time
                        </a>

                        <!-- New Menus -->

                        <a href="{{ route('school.student-id-card') }}" data-title="Student ID Card" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-identity-card"></i>
                            ID Card
                        </a>

                        <a href="{{ route('school.student-attendance') }}" data-title="Student Attendance" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-check-in-01"></i>
                            Attendance
                        </a>

                        <!-- Guardian merged here -->

                        <a href="{{ route('school.guardians') }}" data-title="Guardian Lists" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-account"></i>
                            Guardian
                        </a>

                    </div>
                </div>


                <!-- ================= Class & Academics Management ================= -->
                {{-- <p class="nav-header">Class & Academics</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-book-open-01 w-4"></i>
                            Academic List
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.classes') }}" data-title="Class" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-layers-01"></i>
                            Class
                        </a>

                        <a href="{{ route('school.groups') }}" data-title="Group" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-grid-view"></i>
                            Group
                        </a>

                        <a href="{{ route('school.sections') }}" data-title="Section" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-grid-table"></i>

                            Section
                        </a>

                        <a href="{{ route('school.sessions') }}" data-title="Session" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                            Session
                        </a>

                        <a href="{{ route('school.subjects') }}" data-title="Subject" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                            Subject
                        </a>

                        <a href="{{ route('school.syllabus') }}" data-title="Syllabus" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-scroll"></i>
                            Syllabus
                        </a>

                        <a href="{{ route('school.class-routine') }}" data-title="Class Routine" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                            Class Routine
                        </a>

                    </div>
                </div>



                <!-- ================= Exam Management ================= -->
                {{-- <p class="nav-header">Exam Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-file-02"></i>
                            Exam List
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <!-- Exam Name -->
                        <a href="{{ route('school.exam-name') }}" data-title="Exam Name" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-file-01"></i>
                            Exam Name
                        </a>

                        <!-- Exam Routine -->
                        <a href="{{ route('school.exam-routine') }}" data-title="Exam Routine" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-03"></i>
                            Exam Routine
                        </a>

                        <!-- Grade -->
                        <a href="{{ route('school.grade') }}" data-title="Grade" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-award-01"></i>
                            Grade
                        </a>

                        <!-- Admit Card -->
                        <a href="{{ route('school.admit-card') }}" data-title="Admit Card" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-file-02"></i>
                            Admit Card
                        </a>

                        <!-- Seat Plan -->
                        <a href="{{ route('school.seat-plan') }}" data-title="Seat Number" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-chair-01"></i>
                            Seat Number
                        </a>

                        <!-- Mark Submit -->
                        <a href="{{ route('school.mark-submit') }}" data-title="Mark Submit" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-edit-01"></i>
                            Mark Submit
                        </a>

                        <!-- Schedule -->
                        <a href="{{ route('school.schedule') }}" data-title="Schedule" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-clock-01"></i>
                            Schedule
                        </a>

                        <!-- Result Find -->
                        <a href="{{ route('school.result-find') }}" data-title="Result Find" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-search-01"></i>
                            Result Find
                        </a>

                        <!-- Certificate -->
                        <a href="#" data-title="Certificate" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-certificate-01"></i>
                            Certificate
                        </a>

                    </div>
                </div>



                <!-- ================= Fees ================= -->
                {{-- <p class="nav-header">Fees Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-wallet-02"></i>
                            Fee Management
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.fees-type') }}" data-title="Fees Type" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-tag-01"></i>
                            Fee List
                        </a>

                        <a href="{{ route('school.discount') }}" data-title="Discount" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-percent"></i>
                            Discount
                        </a>

                        <a href="{{ route('school.payment') }}" data-title="Payment" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-credit-card"></i>
                            Payment
                        </a>

                        <a href="{{ route('school.due-list') }}" data-title="Due List" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-alert-circle"></i>
                            Due List
                        </a>

                    </div>
                </div>



                <!-- ================= Finance Management ================= -->
                {{-- <p class="nav-header">Finance Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-money-bag-02 w-4"></i>
                            Financial
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.membership') }}" data-title="Membership" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-account"></i>
                            Member No
                        </a>

                        <a href="{{ route('school.income') }}" data-title="Income" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-money-add-02"></i>
                            Income
                        </a>

                        <a href="{{ route('school.expense') }}" data-title="Expense" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-credit-card"></i>
                            Expense
                        </a>

                        <!-- New Financial Submenus -->

                        <a href="{{ route('school.product') }}" data-title="Product" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-package"></i>
                            Product
                        </a>

                        <a href="{{ route('school.supplier') }}" data-title="Supplier" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-multiple"></i>
                            Supplier
                        </a>

                        <a href="{{ route('school.purchase') }}" data-title="Purchase" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-shopping-cart-02"></i>
                            Purchase
                        </a>

                        <a href="{{ route('school.due-paid') }}" data-title="Due Paid" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-money-receive-02"></i>
                            Due Paid
                        </a>

                    </div>
                </div>



                {{-- <!-- ================= Inventory ================= -->

                <p class="nav-header">Inventory Management</p>

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span><i class="fas fa-boxes w-4"></i> Inventory</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.product') }}" data-title="Product" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-box"></i> Product
                        </a>

                        <a href="{{ route('school.purchase') }}" data-title="Purchase" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-shopping-cart"></i> Purchase
                        </a>

                        <a href="{{ route('school.return') }}" data-title="Return" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-undo"></i> Return
                        </a>

                        <a href="{{ route('school.due-paid') }}" data-title="Due Paid" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-money-check"></i> Due Paid
                        </a>

                        <a href="{{ route('school.profit-loss') }}" data-title="Profit Loss" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-chart-line"></i> Profit-Loss
                        </a>

                        <a href="{{ route('school.add-payment') }}" data-title="Add Payment" data-link
                            class="sidebar-subitem">
                            <i class="fas fa-credit-card"></i> Add Payment
                        </a>

                    </div>
                </div> --}}

                <!-- ================= HRM Management ================= -->
                {{-- <p class="nav-header">HRM Management</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-user-group"></i>
                            HRM System
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.employee') }}" data-title="Employee" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-user"></i>
                            Employee
                        </a>

                        <a href="{{ route('school.payroll') }}" data-title="Payroll" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-invoice"></i>
                            Payroll
                        </a>

                        <a href="{{ route('school.role-permission') }}" data-title="Role Permission" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-lock"></i>
                            Role Permission
                        </a>

                    </div>
                </div>

                <!-- ================= Question Bank ================= -->
                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                            Question Bank
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.omr') }}" data-title="OMR" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-database"></i>
                            OMR
                        </a>

                        <a href="{{ route('school.questions') }}" data-title="Questions" data-link class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-book-02"></i>
                            Questions
                        </a>

                    </div>
                </div>


                <!-- ================= Notification Management ================= -->

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-notification-03 w-4"></i>
                            Notification
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        <a href="{{ route('school.announcement') }}" data-title="Create Announcement" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-megaphone-02"></i>
                            Send Notice
                        </a>

                        <a href="{{ route('school.create-holiday') }}" data-title="Create Holiday" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-calendar-remove-01"></i>
                            Create Holiday
                        </a>

                    </div>
                </div>

                <!-- ================= Plan Management & SMS List ================= -->
                {{-- <p class="nav-header">SMS List</p> --}}

                <div class="sidebar-group">
                    <div class="sidebar-group-toggle">
                        <span>
                            <i class="hgi hgi-stroke hgi-rounded hgi-crown"></i>
                            SMS List
                        </span>
                        <i class="hgi hgi-stroke hgi-rounded hgi-arrow-right-01 text-xs"></i>
                    </div>

                    <div class="sidebar-group-content">

                        {{-- <a href="{{ route('school.current-plan') }}" data-title="Current Plan" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-crown"></i>
                            Package
                        </a> --}}

                        <a href="{{ route('school.sms-package') }}" data-title="SMS Package" data-link
                            class="sidebar-subitem">
                            <i class="hgi hgi-stroke hgi-rounded hgi-message-01"></i>
                            SMS
                        </a>

                    </div>
                </div>



                <div class="subscription-box mt-4 p-3 mb-2"
                    style="border-radius: 0; border: 1px solid #e5e7eb; background: #fff;">
                    <div class="plan-info">
                        <p class="nav-header" style="margin: 0 0 8px 0; padding: 0; letter-spacing: 0.5px; text-transform: capitalize !important; font-size: 12px !important;">
                            Subscription</p>

                        <p id="sidePlanName" class="text-sm font-bold text-slate-800 mb-1">Loading...</p>

                        <p id="sideExpiryDate" class="text-[10px] text-slate-500 mb-3">-- -- ----</p>

                        <div class="w-full bg-gray-200 h-1.5 mb-4" style="border-radius: 0; overflow: hidden;">
                            <div id="sideProgressBar" class="bg-blue-600 h-full"
                                style="width: 0%; border-radius: 0; transition: width 1s ease-in-out;"></div>
                        </div>

                        <a href="{{ route('school.current-plan') }}" class="upgrade-btn"
                            style="border-radius: 0; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #1e40af; color: white; padding: 10px; text-decoration: none;">
                            <i class="fas fa-rocket"></i>
                            <span class="menu-text">Upgrade Plan</span>
                        </a>
                    </div>
                </div>

            </nav>

            {{-- <div class="p-4 logout-wrapper">
                <button id="logoutBtn"
                    class="w-full flex items-center justify-center gap-2 p-2 border border-red-500 text-red-500 text-sm font-semibold">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div> --}}

        </aside>

        <div class="flex-1 flex flex-col">

            <header class="topbar">

                <div class="flex items-center gap-3">
                    <button id="hamburger" class="text-blue-600 text-xl">
                        <i id="hamburgerIcon" class="fas fa-bars"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2 relative z-50">

                    {{-- Support Toggle --}}
                    <a href="#" id="topbarSupportBtn"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 text-gray-600 hover:bg-slate-100"
                        title="Support">
                        <i class="fas fa-headset"></i>
                    </a>

                    {{-- Notification Toggle --}}
                    <div class="relative">
                        <button id="topbarNotificationBtn"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 text-gray-600 hover:bg-slate-100 relative"
                            title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span
                                class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full border border-white"></span>
                        </button>

                        {{-- Notification Box Container --}}
                        <div id="notificationBox"
                            class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 sm:bg-transparent sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-0 sm:p-0">

                            <div class="sm:hidden absolute inset-0 -z-10" id="mobileBackdrop"></div>

                            {{-- Main Box --}}
                            <div class="w-full max-w-[350px] sm:max-w-none sm:w-[450px] bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col animate-in fade-in zoom-in duration-150"
                                style="border-radius: 0; max-height: 480px;">

                                {{-- Header: Changed uppercase to capitalize --}}
                                <div
                                    class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                    <span
                                        class="text-xs font-bold capitalize tracking-wider text-slate-500">Notifications</span>
                                    <button id="closeNotify" class="sm:hidden text-slate-400 hover:text-slate-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                {{-- Body: Added capitalize to title and paragraph --}}
                                <div class="overflow-y-auto custom-scrollbar">
                                    <div
                                        class="px-4 py-4 border-b border-slate-50 bg-blue-50/40 hover:bg-slate-50 transition-colors cursor-pointer relative">
                                        <div class="absolute left-0 top-0 bottom-0 w-[3px] bg-blue-500"></div>
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-sm font-bold text-slate-800 capitalize">Exam schedule
                                                released</span>
                                            <span class="w-2 h-2 rounded-full bg-blue-500 mt-1"></span>
                                        </div>
                                        <p class="text-xs text-slate-600 leading-normal mb-2 capitalize">The final term
                                            exam schedule for class 10 has been uploaded.</p>
                                        <span class="text-[10px] text-slate-400 font-medium capitalize">Today • 10:30
                                            am</span>
                                    </div>

                                    <div
                                        class="px-4 py-4 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-sm font-semibold text-slate-700 capitalize">Fee payment
                                                success</span>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-normal mb-2 capitalize">Payment for
                                            invoice #inv-9920 was successful.</p>
                                        <span class="text-[10px] text-slate-400 font-medium capitalize">20 apr 2026 •
                                            04:15 pm</span>
                                    </div>
                                </div>

                                {{-- Footer: Added capitalize and kept centering override --}}
                                <a href="#"
                                    class="block pt-4 pb-5 text-xs font-bold text-blue-600 hover:bg-slate-100 border-t border-slate-100 transition-colors capitalize"
                                    style="text-align: center !important; width: 100% !important; display: block !important; margin: 0 !important;">
                                    Mark all as read
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Setting Toggle --}}
                    <a href="#" id="topbarSettingBtn"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 text-gray-600 hover:bg-slate-100"
                        title="Settings">
                        <i class="fas fa-cog"></i>
                    </a>

                    {{-- Profile Toggle --}}
                    {{-- <button data-bs-toggle="modal" data-bs-target="#editProfileModal"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 overflow-hidden hover:opacity-80 transition-opacity"
                        title="{{ auth()->user()->school_name }}">
                        <img src="{{ $schoolLogo }}" class="w-full h-full object-cover">
                    </button> --}}

                    {{-- Profile Toggle --}}
                    <div class="relative">
                        <button id="profileBtn"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition-all"
                            title="{{ auth()->user()->school_name }}">

                            {{-- Profile Icon (SVG) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-slate-600">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>

                        </button>

                        <div id="profileMenu" class="show absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 z-50">
                            <button type="button" class="w-full px-4 py-2 text-[12px] hover:bg-gray-100 text-left flex items-center gap-2" onclick="openProfileModal()" style="border: none; background: transparent; color: #334155; cursor: pointer;">
                                <i class="fas fa-edit text-xs"></i>
                                Edit Profile
                            </button>
                            <button type="button" class="w-full px-4 py-2 text-[12px] hover:bg-gray-100 text-left flex items-center gap-2" onclick="openPasswordModal()" style="border: none; background: transparent; color: #334155; cursor: pointer;">
                                <i class="fas fa-lock text-xs"></i>
                                Change Password
                            </button>
                            <hr class="my-1" style="margin-top: 4px; margin-bottom: 4px; border: none; border-top: 1px solid #e5e7eb;">
                            <a href="#" id="logoutBtnTop"
                                class="w-full px-4 py-2 text-[12px] hover:bg-red-50 text-left flex items-center gap-2 text-red-500"
                                style="text-decoration: none;">
                                <i class="fas fa-power-off text-xs"></i>
                                Logout
                            </a>
                        </div>
                    </div>

                </div>

            </header>

            <main class="main-scroll" id="mainContent">
                @yield('content')
            </main>

        </div>
    </div>


    <!--Edit School Profile Modal -->
    <div class="modal fade premium-modal" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mx-auto" style="width: 85%; max-width: 600px;">
            <div class="modal-content modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] border border-gray-100"
                style="border-radius: 0; background: #fff;">

                <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                    <h3
                        class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                        Update school profile
                    </h3>
                </div>

                <form id="profileUpdateForm" class="flex flex-col overflow-hidden m-0" enctype="multipart/form-data">
                    <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">

                        <div class="col-span-1 sm:col-span-2 mb-4">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Institution
                                logo</label>
                            <div class="flex gap-3">
                                <div class="flex-grow">
                                    <input type="file" name="logo" id="school_logo"
                                        class="form-input-fixed w-full text-[11px] file:mr-4 file:py-1 file:px-3 file:border file:border-gray-100 file:text-[10px] file:bg-gray-50 file:text-gray-600 border border-gray-200 h-[32px] flex items-center"
                                        accept="image/*" style="border-radius: 0;" onchange="previewImage(event)" />
                                </div>
                                <div class="w-[32px] h-[32px] border border-gray-200 bg-white flex items-center justify-center overflow-hidden flex-shrink-0"
                                    style="border-radius: 0;">
                                    <img id="logoPreview" src="{{ asset('storage/' . $school->logo) }}"
                                        alt="Logo" class="w-full h-full object-contain">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">School
                                    name</label>
                                <input type="text" name="school_name" value="{{ $school->school_name }}" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Mobile
                                    number</label>
                                <input type="text" name="mobile" value="{{ $school->mobile }}" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">EIIN
                                    number</label>
                                <input type="text" name="eiin_number" value="{{ $school->eiin_number }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1 sm:col-span-2">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Email
                                    address</label>
                                <input type="email" name="email" value="{{ $school->email }}" required
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1 sm:col-span-2 mt-2">
                                <p class="text-[10px] capitalize text-blue-500 border-b border-blue-50 pb-1 mb-1">
                                    Location details</p>
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Division</label>
                                <input type="text" name="division" value="{{ $school->division }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">District</label>
                                <input type="text" name="district" value="{{ $school->district }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Upazila</label>
                                <input type="text" name="upazila" value="{{ $school->upazila }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Village
                                </label>
                                <input type="text" name="village" value="{{ $school->village }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                    style="border-radius: 0;" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                        <button type="button" data-bs-dismiss="modal"
                            class="w-1/2 sm:w-auto sm:px-8 h-[32px] border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                            style="border-radius: 0; background: transparent; color: #64748b;">
                            Discard
                        </button>
                        <button type="submit"
                            class="w-1/2 sm:w-auto sm:px-12 h-[32px] border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                            style="border-radius: 0; background: #2563eb; color: #ffffff;">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade premium-modal" id="passwordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mx-auto" style="width: 85%; max-width: 500px;">
            <div class="modal-content modal-content-sharp shadow-2xl overflow-hidden flex flex-col border border-gray-100"
                style="border-radius: 0; background: #fff;">

                <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                    <h3
                        class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                        Change Password
                    </h3>
                </div>

                <form id="passwordChangeForm" class="flex flex-col overflow-hidden m-0">
                    @csrf
                    <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">

                        <div class="space-y-4">
                            <div class="w-full">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Current Password</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="currentPassInput"
                                        class="form-control w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                        style="border-radius: 0;" required>
                                    <i class="fas fa-eye toggle-password" data-target="currentPassInput" title="Toggle Password"></i>
                                </div>
                            </div>

                            <div class="w-full">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">New Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="newPassInput"
                                        class="form-control w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                        style="border-radius: 0;" required>
                                    <i class="fas fa-eye toggle-password" data-target="newPassInput" title="Toggle Password"></i>
                                </div>
                                <small class="text-[9px] text-slate-400 mt-1 block">Min 8 chars, uppercase, lowercase, numbers & symbols</small>
                            </div>

                            <div class="w-full">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password_confirmation" id="confirmNewPassInput"
                                        class="form-control w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                        style="border-radius: 0;" required>
                                    <i class="fas fa-eye toggle-password" data-target="confirmNewPassInput" title="Toggle Password"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                        <button type="button" data-bs-dismiss="modal"
                            class="w-1/2 sm:w-auto sm:px-8 h-[32px] border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                            style="border-radius: 0; background: transparent; color: #64748b;">
                            Cancel
                        </button>
                        <button type="submit" id="savePasswordBtn"
                            class="w-1/2 sm:w-auto sm:px-12 h-[32px] border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                            style="border-radius: 0; background: #2563eb; color: #ffffff;">
                            <i class="fas fa-check mr-1"></i> Change
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="globalModalBackdrop" class="global-modal-backdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* -------------------------------
                                                                                                                                                                                                                                                                    INITIALIZE ELEMENTS SAFELY
                                                                                                                                                                                                                                                                --------------------------------*/
        // We fetch these inside functions or check for null to prevent crashes
        const getEl = (id) => document.getElementById(id);

        /* --- Open Profile Modal --- */
        function openProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            modal.show();
        }

        /* --- Open Password Modal --- */
        function openPasswordModal() {
            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            modal.show();
        }

        /* --- Profile Dropdown Toggle --- */
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('show');
                }
            });
        }

        /* --- Password Eye Toggle --- */
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                }
            });
        });

        /* -------------------------------
            IMAGE PREVIEW
        --------------------------------*/
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = getEl('logoPreview');
                if (output) output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        /* -----------------------------------------------------------
        PROFILE UPDATE (API) - FULL DYNAMIC SYNC 
        ----------------------------------------------------------- */
        const profileForm = getEl('profileUpdateForm');

        if (profileForm) {
            profileForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);
                const btn = e.target.querySelector('button[type="submit"]');
                if (!btn) return;

                // UI Loading State
                const originalBtnText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                btn.disabled = true;

                try {
                    // Using the controller route
                    const res = await fetch('{{ url('/api/school/update-profile') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        // 1. Success Notification
                        Swal.fire({
                            icon: 'success',
                            title: data.message || 'Profile updated successfully!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        /* -------------------------------------------------------
                            DYNAMIC UI UPDATES (No Refresh Needed)
                        ------------------------------------------------------- */
                        // Update Topbar Profile Image & Name
                        const topbarLogo = document.querySelector('#profileBtn img');
                        const topbarName = document.querySelector('#profileBtn span');
                        const newName = formData.get('school_name');

                        if (data.data && data.data.logo_url) {
                            // Update Topbar Image
                            if (topbarLogo) topbarLogo.src = data.data.logo_url;

                            // Update Modal Preview only
                            const logoPreview = document.getElementById('logoPreview');
                            if (logoPreview) logoPreview.src = data.data.logo_url;

                            // NOTE: Sidebar logo (sideSchoolLogo) is intentionally 
                            // left untouched to preserve system branding.
                        }

                        if (topbarName && newName) {
                            topbarName.textContent = newName;
                        }

                        /* -------------------------------------------------------
                            CLEANUP: MODAL & BACKDROP STUCK FIX
                        ------------------------------------------------------- */
                        const modalEl = getEl('editProfileModal');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            // Force remove backdrop if Bootstrap's hide() fails to clear it
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) {
                                backdrop.remove();
                            }

                            // Restore scrolling to the body
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }

                    } else {
                        // Handle Validation Errors from Laravel
                        let errorMsg = data.message || 'Update Failed';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>');
                        }
                        throw new Error(errorMsg);
                    }

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Error',
                        html: error.message || 'Could not reach server.'
                    });
                } finally {
                    // Restore Button State
                    btn.innerHTML = originalBtnText;
                    btn.disabled = false;
                }
            });
        }

        /* --- Password Change Form --- */
        const passwordChangeForm = getEl('passwordChangeForm');

        if (passwordChangeForm) {
            passwordChangeForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const btn = getEl('savePasswordBtn');
                if (!btn) return;

                const originalBtnText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
                btn.disabled = true;

                const formData = new FormData(passwordChangeForm);

                try {
                    const res = await fetch('/api/change-password', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message || 'Password changed successfully!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        passwordChangeForm.reset();
                        const modal = bootstrap.Modal.getInstance(getEl('passwordModal'));
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        let errorMsg = data.message || 'Password update failed.';
                        if (data.errors) {
                            errorMsg = Object.values(data.errors).flat().join('<br>');
                        }
                        throw new Error(errorMsg);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: error.message || 'Could not reach server.'
                    });
                } finally {
                    btn.innerHTML = originalBtnText;
                    btn.disabled = false;
                }
            });
        }


        /* -------------------------------
            MOBILE SIDEBAR & TOGGLES
        --------------------------------*/
        getEl('hamburger')?.addEventListener('click', () => {
            const sidebar = getEl('sidebar');
            const icon = getEl('hamburgerIcon');
            const body = document.body; // Using body as the toggle host

            if (window.innerWidth < 768) {
                // --- MOBILE BEHAVIOR ---
                sidebar.classList.toggle('sidebar-mobile-show');
                sidebar.classList.toggle('sidebar-mobile-hidden');

                if (icon) {
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');
                }
            } else {
                // --- DESKTOP BEHAVIOR ---
                // We toggle a class on the body so the Topbar/Main content can react too
                body.classList.toggle('collapsed-mode');

                // Save state to keep it collapsed on page refresh
                const isCollapsed = body.classList.contains('collapsed-mode');
                localStorage.setItem('sidebar_collapsed', isCollapsed);
            }
        });

        // Auto-restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth >= 768) {
                if (localStorage.getItem('sidebar_collapsed') === 'true') {
                    document.body.classList.add('collapsed-mode');
                }
            }
        });
        document.querySelectorAll('.sidebar-group-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const group = toggle.parentElement;
                if (group) {
                    group.classList.toggle('open');
                    const groups = [...document.querySelectorAll('.sidebar-group')];
                    localStorage.setItem('sidebar_open_group', groups.indexOf(group));
                }
            });
        });

        getEl('menuNav')?.addEventListener('scroll', (e) => {
            localStorage.setItem('sidebar_scroll_position', e.target.scrollTop);
        });


        /* -------------------------------
        RESTORE & HIGHLIGHT STATES
        --------------------------------*/
        function highlightActiveMenu() {
            const menuNav = document.getElementById('menuNav'); // your sidebar wrapper
            if (!menuNav) return;

            const links = menuNav.querySelectorAll('[data-link]');
            const currentPath = window.location.pathname;

            const pageHeader = document.getElementById('pageHeader');
            const pageTitle = document.getElementById('pageTitle');

            // Close all groups first
            document.querySelectorAll('.sidebar-group').forEach(group => group.classList.remove('open'));

            links.forEach(link => {
                link.classList.remove('active');

                const hrefAttr = link.getAttribute('href');
                if (!hrefAttr || hrefAttr === '#') return;

                try {
                    const linkPath = new URL(link.href).pathname;

                    if (currentPath === linkPath) {
                        link.classList.add('active');

                        // Get parent group toggle text for h2
                        const parentGroup = link.closest('.sidebar-group');
                        let groupTitle = 'Dashboard';
                        if (parentGroup) {
                            const toggleSpan = parentGroup.querySelector('.sidebar-group-toggle > span');
                            if (toggleSpan) {
                                // Remove any inner <i> icons from text
                                groupTitle = toggleSpan.innerText.trim();
                            }
                            parentGroup.classList.add('open');
                        }

                        // Set h2 as sidebar group title
                        if (pageHeader) pageHeader.innerText = groupTitle;

                        // Set breadcrumb as active submenu text
                        if (pageTitle) pageTitle.innerText = link.innerText.trim();
                    }
                } catch (e) {
                    /* skip invalid URLs */
                }
            });
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', highlightActiveMenu);


        function restoreSidebarState() {
            const menuNav = getEl('menuNav');

            // Restore Scroll Position
            const savedScroll = localStorage.getItem('sidebar_scroll_position');
            if (menuNav && savedScroll !== null) {
                menuNav.scrollTop = savedScroll;
            }

            // Run the highlighter to open the correct group based on URL
            highlightActiveMenu();
        }

        /* -------------------------------
            DROPDOWNS & NOTIFICATIONS
        --------------------------------*/
        getEl('profileBtn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            getEl('profileMenu')?.classList.toggle('show');
        });

        getEl('notificationBtn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            getEl('notificationBox')?.classList.toggle('hidden');
        });

        window.addEventListener('click', () => {
            getEl('profileMenu')?.classList.remove('show');
        });

        /* -------------------------------
            LOGOUT 
        --------------------------------*/

        const logoutBtn = document.getElementById('logoutBtnTop');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', logoutUser);
        }

        async function logoutUser(e) {
            if (e) e.preventDefault();

            const btn = e.currentTarget;
            const originalContent = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.style.pointerEvents = 'none';

            try {
                const res = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();

                Swal.fire({
                    icon: 'success',
                    title: data.message || 'Logged out successfully!',
                    text: 'Redirecting to login page...',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = data.redirect ?? '/';
                });


            } catch (error) {
                // alert('Logout failed! Check your connection.');

                btn.innerHTML = originalContent;
                btn.style.pointerEvents = 'auto';
            }
        }

        /* -------------------------------
        PAGE LOAD INIT
        --------------------------------*/
        window.addEventListener('DOMContentLoaded', () => {
            highlightActiveMenu();
            restoreSidebarState();

            const logoutButtons = [
                'logoutBtn',
                'logoutBtnTop',
                'logoutBtnDropdown'
            ];

            logoutButtons.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.removeEventListener('click', logoutUser);
                    el.addEventListener('click', logoutUser);
                }
            });
        });
    </script>

    @stack('scripts')


    {{-- GLOBAL SCRIPT FOR SUBSCRIPTION PLAN DATA & DYNAMIC LOGO --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        (function() {
            window.addEventListener('load', function() {
                const sideName = document.getElementById('sidePlanName');
                const sideExpiry = document.getElementById('sideExpiryDate');
                const sideBar = document.getElementById('sideProgressBar');

                if (!sideName) return;

                const token = document.querySelector('meta[name="csrf-token"]');
                if (token) {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                }

                axios.get('/api/current-subscription')
                    .then(res => {
                        const s = res.data.subscription;
                        if (s) {
                            // Standard casing as per your request
                            sideName.innerText = s.package_name;
                            sideExpiry.innerText = "Expires: " + s.expires_at;

                            // Precise Progress Calculation
                            const start = new Date(s.start_date_raw);
                            const end = new Date(s.expires_at_raw || s.expires_at);
                            const today = new Date();

                            const totalDuration = end - start;
                            const timeElapsed = today - start;

                            // Calculate percentage of time remaining
                            let percent = 100 - Math.floor((timeElapsed / totalDuration) * 100);

                            // Safety bounds
                            percent = Math.min(100, Math.max(0, percent));

                            sideBar.style.width = percent + "%";

                            // Danger UI: Less than 5 days left
                            let days = parseInt(s.days_remaining) || 0;
                            if (days < 5) {
                                sideBar.style.backgroundColor = "#ef4444";
                                sideName.style.color = "#ef4444";
                            }
                        }
                    })
                    .catch(err => {
                        sideName.innerText = "Standby";
                    });
            });
        })();


        // Dynamic Logo Setup Script
        (function() {
            window.addEventListener('load', function() {
                const logoImg = document.getElementById('sideSchoolLogo');

                if (!logoImg) return;

                axios.get('/api/dynamic-operation')
                    .then(res => {
                        const settings = res.data;
                        // If the specific dashboard logo exists in the DB
                        if (settings && settings.school_dashboard_logo) {
                            // Update src with storage path
                            logoImg.src = window.location.origin + '/storage/' + settings
                                .school_dashboard_logo;
                        }
                    })
                    .catch(err => {
                        console.warn("Dynamic logo fetch failed, using default.");
                    });
            });
        })();


        // Notification box appearing script(Static)
        document.addEventListener('DOMContentLoaded', function() {
            const notifyBtn = document.getElementById('topbarNotificationBtn');
            const notifyBox = document.getElementById('notificationBox');
            const mobileBackdrop = document.getElementById('mobileBackdrop');

            function toggleNotifications(e) {
                if (e) e.stopPropagation();
                notifyBox.classList.toggle('hidden');
            }

            function closeNotifications() {
                notifyBox.classList.add('hidden');
            }

            // Main Toggle
            notifyBtn.addEventListener('click', toggleNotifications);

            // Close on Mobile Backdrop or 'X'
            if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeNotifications);
            document.getElementById('closeNotify')?.addEventListener('click', closeNotifications);

            // Close when clicking outside on Desktop
            document.addEventListener('click', (e) => {
                if (!notifyBox.contains(e.target) && !notifyBtn.contains(e.target)) {
                    closeNotifications();
                }
            });
        });
    </script>

</body>

</html>