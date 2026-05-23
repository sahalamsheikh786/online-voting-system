@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="panel-card p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <span class="eyebrow">User Registration</span>
                        <h1 class="h2 mt-2 mb-1">Create your voter profile</h1>
                        <p class="text-secondary mb-0">Your registration will stay pending until an admin reviews and accepts it.</p>
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill px-4">Back to Login</a>
                </div>

                <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data" class="row g-4" id="registerForm" autocomplete="off">
                    @csrf
                    <input type="text" name="register_fake_username" class="d-none" tabindex="-1" autocomplete="username">
                    <input type="password" name="register_fake_password" class="d-none" tabindex="-1" autocomplete="new-password">
                    <div class="col-md-6">
                        <label class="form-label">User Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Only alphabet characters" pattern="[A-Za-z\s]+" title="User name must contain only alphabets and spaces." autocomplete="off" autocapitalize="words" autocorrect="off" spellcheck="false" readonly onfocus="this.removeAttribute('readonly');">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User Contact</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="form-control" placeholder="Exactly 10 digits" pattern="\d{10}" maxlength="10" inputmode="numeric" title="Contact number must be exactly 10 digits." autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" readonly onfocus="this.removeAttribute('readonly');">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <div class="input-group password-input-group">
                            <input type="password" name="password" class="form-control" id="password" minlength="8" title="Password must be at least 8 characters." autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false" readonly onfocus="this.removeAttribute('readonly');">
                            <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-password-toggle data-password-target="password" aria-label="Show password" aria-pressed="false">
                                <span data-password-icon aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group password-input-group">
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" minlength="8" title="Confirm password must match password." autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false" readonly onfocus="this.removeAttribute('readonly');">
                            <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-password-toggle data-password-target="password_confirmation" aria-label="Show confirm password" aria-pressed="false">
                                <span data-password-icon aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    @include('partials.pattern-lock', [
                        'patternId' => 'register_pattern_lock',
                        'patternName' => 'pattern_lock',
                        'patternLabel' => 'Create Pattern Lock',
                    ])
                    <div class="col-md-6">
                        <label class="form-label">User Age / Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control" max="{{ now()->subYears(18)->toDateString() }}">
                        <div class="form-text">Only users aged 18 or above can register.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">District</label>
                        <select name="district_id" class="form-select">
                            <option value="">Select district</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Citizenship Number</label>
                        <input type="text" name="citizenship_number" value="{{ old('citizenship_number') }}" class="form-control" placeholder="Numbers with / or -" pattern="[0-9\/-]+" title="Citizenship number can contain only numbers, slash, and hyphen.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Voter ID Number</label>
                        <input type="text" name="voter_id_number" value="{{ old('voter_id_number') }}" class="form-control" placeholder="Only numbers" pattern="\d+" inputmode="numeric" title="Voter ID number must contain only digits.">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Current Image</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="col-12">
                        <div class="pattern-note">
                            Pattern:
                            Name = alphabets and spaces only.
                            Contact = exactly 10 digits only.
                            Password = at least 8 characters and both passwords must match.
                            Pattern Lock = at least 4 dots and login ma same pattern hunu parcha.
                            Date of Birth = age must be 18 or above.
                            Citizenship Number = unique and can use numbers with "/" or "-".
                            Voter ID = unique and numbers only.
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-3">
                        <button class="btn btn-primary px-4">Submit</button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.pattern-lock-script')
    <script>
        const registerForm = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.passwordTarget);

                if (! target) {
                    return;
                }

                const isHidden = target.type === 'password';
                target.type = isHidden ? 'text' : 'password';
                button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        });

        if (registerForm && password && passwordConfirmation) {
            const syncPasswordValidation = () => {
                if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Confirm your both password');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            };

            password.addEventListener('input', syncPasswordValidation);
            passwordConfirmation.addEventListener('input', syncPasswordValidation);
            registerForm.addEventListener('submit', syncPasswordValidation);
        }
    </script>
@endpush
