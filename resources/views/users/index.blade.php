@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <span class="eyebrow">User Management</span>
            <h1 class="h2 mt-2 mb-1">Approved users</h1>
            <p class="text-secondary mb-0">Filter users by district or show all users sorted alphabetically by district.</p>
        </div>
    </div>

    <div class="panel-card p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">District Filter</label>
                <select name="district_id" class="form-select">
                    <option value="">All districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" @selected($selectedDistrict == $district->id)>{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary w-100">Apply Filter</button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('users.index', ['show_all' => 1]) }}" class="btn btn-primary w-100">Show All Users</a>
            </div>
        </form>
    </div>

    <div class="panel-card p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>District</th>
                        <th>Voter ID</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->contact_number }}</td>
                            <td>{{ $user->district?->name }}</td>
                            <td>{{ $user->voter_id_number }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#editUser{{ $user->id }}">Edit</button>
                            </td>
                        </tr>
                        <tr class="collapse" id="editUser{{ $user->id }}">
                            <td colspan="5">
                                <div class="row g-3">
                                    <div class="col-lg-10">
                                        <form action="{{ route('users.update', $user) }}" method="POST" class="row g-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-4">
                                                <input type="text" name="name" value="{{ $user->name }}" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <select name="district_id" class="form-select">
                                                    @foreach($districts as $district)
                                                        <option value="{{ $district->id }}" @selected($user->district_id == $district->id)>{{ $district->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 d-flex gap-2">
                                                <button class="btn btn-primary btn-sm px-4">Save</button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-toggle="collapse" data-bs-target="#editUser{{ $user->id }}">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-lg-2">
                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Delete this user?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">No approved users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
@endsection
