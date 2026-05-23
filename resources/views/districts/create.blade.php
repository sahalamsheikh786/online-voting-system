@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div>
            <span class="eyebrow">Add Election Card</span>
            <h1 class="h2 mt-2 mb-1">Create a new district election card</h1>
            <p class="text-secondary mb-0">Only new district election card creation is available here.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="panel-card p-4 p-lg-5">
                <div class="mb-4">
                    <span class="eyebrow">District Setup</span>
                    <h2 class="h4 mt-2 mb-1">Only district card creation lives here</h2>
                    <p class="text-secondary mb-0">New district added here will automatically appear in registration, candidate management, reports, and dashboard lists.</p>
                </div>

                <form action="{{ route('districts.store') }}" method="POST" class="row g-4 align-items-end">
                    @csrf
                    <div class="col-lg-8">
                        <label class="form-label">District Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Enter district name">
                    </div>
                    <div class="col-lg-4 d-grid">
                        <button class="btn btn-primary btn-lg">Add Election Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-xl-10">
            <div class="panel-card p-4 p-lg-5 border border-danger-subtle">
                <div class="mb-4">
                    <span class="eyebrow text-danger">Delete Added District Card</span>
                    <h2 class="h4 mt-2 mb-1 text-danger">Permanently delete a district card</h2>
                    <p class="text-secondary mb-0">Deleting a district from here does not save it to archive. The selected district is directly removed from the system, dashboard list, reports, candidate records, vote records, and related database data.</p>
                </div>

                <form action="{{ route('districts.hard-delete') }}" method="POST" class="row g-4 align-items-end" onsubmit="return confirm('This will permanently delete the selected district card and related records from the database. Continue?')">
                    @csrf
                    @method('DELETE')
                    <div class="col-lg-8">
                        <label class="form-label">Select District Card To Delete</label>
                        <select name="district_id" class="form-select form-select-lg">
                            <option value="">Choose district</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 d-grid">
                        <button class="btn btn-outline-danger btn-lg">Delete Added District Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
