@extends('admin.adminhome')

@section('content')
<style>
    /* --- CUSTOM TABLE STYLING --- */
    
    /* Search Bar Area */
    .search-wrapper {
        background: white;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .custom-dark-input {
        background-color: #343a40;
        border: none;
        color: white;
        padding: 10px 15px;
    }
    .custom-dark-input::placeholder { color: #adb5bd; }
    .custom-dark-input:focus { background-color: #495057; color: white; outline: none; box-shadow: none; }
    .btn-custom-dark { background-color: #343a40; color: white; border: none; padding: 10px 25px; }
    .btn-custom-dark:hover { background-color: #23272b; color: white; }

    /* Table Header (White Background) */
    .table-custom-header th {
        background-color: white;
        color: #212529; /* Dark Text */
        font-weight: 800;
        font-size: 0.8rem;
        text-transform: uppercase;
        border-bottom: none;
        padding: 15px;
    }

    /* Table Body (Dark Background) */
    .table-custom-row td {
        background-color: #343a40; /* Dark Grey Background */
        color: white;
        vertical-align: middle;
        padding: 15px;
        border-top: 1px solid #495057;
    }

    /* Avatar styling */
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #6c757d; 
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0; 
    }

    /* Button Maroon */
    .btn-maroon {
        background-color: #800000; 
        color: white; 
        border: none;
        padding: 8px 20px;
        font-weight: bold;
    }
    .btn-maroon:hover { background-color: #600000; color: white; }

    /* Action Buttons (Edit/Delete) */
    .btn-icon {
        background-color: white;
        color: #343a40;
        border: none;
        width: 35px; 
        height: 35px;
        display: inline-flex;
        align-items: center; 
        justify-content: center;
        border-radius: 4px;
        margin-right: 5px;
    }
    .btn-icon:hover { background-color: #e2e6ea; }
</style>

<div class="container-fluid pt-4">

    {{-- TOP HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-white-50 fw-bold"><i class="fas fa-user-shield me-2"></i>Administrator Management</h4>
        <a href="{{ route('admins.create') }}" class="btn btn-maroon">
            <i class="fas fa-plus me-1"></i> Register New Admin
        </a>
    </div>

    {{-- SEARCH BAR --}}
    <div class="search-wrapper">
        <form action="{{ route('admins.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-1 text-center">
                <i class="fas fa-search text-muted fa-lg"></i>
            </div>
            <div class="col-md-9">
                <input type="text" name="search" class="form-control custom-dark-input" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-custom-dark w-100">Search</button>
            </div>
        </form>
    </div>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-custom-header">
                <tr>
                    {{-- SEPARATE ID COLUMN --}}
                    <th class="ps-4" style="width: 5%;">ID</th>
                    {{-- SEPARATE NAME COLUMN --}}
                    <th style="width: 25%;">NAME</th>
                    
                    <th style="width: 25%;">EMAIL</th>
                    <th style="width: 20%;">PHONE NO</th>
                    <th class="text-center">CREATED AT</th>
                    <th class="text-end pe-4">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr class="table-custom-row">
                    
                    {{-- 1. ID COLUMN --}}
                    <td class="ps-4 text-white-50 fw-bold">
                        {{ $admin->id }}
                    </td>

                    {{-- 2. NAME COLUMN --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="fw-bold text-white fs-6">
                                {{ $admin->name }}
                            </div>
                        </div>
                    </td>

                    {{-- 3. EMAIL COLUMN --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="far fa-envelope text-white-50 me-3" style="width: 20px;"></i>
                            <br>
                            <span class="text-white">{{ $admin->email }}</span>
                        </div>
                    </td>

                    {{-- 4. PHONE NUMBER COLUMN --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-phone-alt text-white-50 me-3" style="width: 20px;"></i>
                            <span class="text-white ">{{ $admin->phone_number ?? 'N/A' }}</span>
                        </div>
                    </td>

                    {{-- 5. DATE --}}
                    <td class="text-center text-white-50 small">
                        {{ $admin->created_at->format('d M Y') }}
                    </td>

                    {{-- 6. ACTIONS --}}
                    <td class="text-end pe-4">
                        <a href="{{ route('admins.edit', $admin->id) }}" class="btn-icon text-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @if(auth()->id() !== $admin->id)
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon text-secondary" onclick="return confirm('Delete this admin?');">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                        @else
                            <button class="btn-icon text-muted opacity-50" disabled>
                                <i class="fas fa-ban"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection