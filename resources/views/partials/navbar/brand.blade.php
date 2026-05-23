<a class="navbar-brand fw-semibold" href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('dashboard') : route('vote.index')) : route('login') }}">
    Online Voting System
</a>
