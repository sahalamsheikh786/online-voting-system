@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="panel-card p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <span class="eyebrow">Candidate Management</span>
                        <h1 class="h2 mt-2 mb-1">{{ $pageTitle }}</h1>
                    </div>
                    <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                </div>

                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="row g-4">
                    @csrf
                    @if($formMethod !== 'POST')
                        @method($formMethod)
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Candidate Name</label>
                        <input type="text" name="name" value="{{ old('name', $candidate->name) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Candidate Age</label>
                        <input type="number" name="age" value="{{ old('age', $candidate->age) }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Candidate Position</label>
                        <select name="position" class="form-select">
                            @foreach(['President', 'Vice President'] as $position)
                                <option value="{{ $position }}" @selected(old('position', $candidate->position ?: 'President') === $position)>{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Candidate Area (District)</label>
                        <select name="district_id" class="form-select">
                            <option value="">Select district</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" @selected(old('district_id', $candidate->district_id) == $district->id)>{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Candidate Email</label>
                        <input type="email" name="email" value="{{ old('email', $candidate->email) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Candidate Image</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Candidate Vision</label>
                        <input type="file" name="vision" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </div>
                    <div class="col-12 d-flex gap-3">
                        <button class="btn btn-primary px-4">Add</button>
                        <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
