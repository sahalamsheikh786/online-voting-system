@php
    $patternId = $patternId ?? 'pattern_lock';
    $patternName = $patternName ?? 'pattern_lock';
    $patternLabel = $patternLabel ?? 'Pattern Lock';
    $patternValue = old($patternName, $patternValue ?? '');
@endphp

<div class="col-12">
    <label class="form-label">{{ $patternLabel }}</label>
    <input type="hidden" name="{{ $patternName }}" id="{{ $patternId }}" value="{{ $patternValue }}">
    <div class="pattern-lock-widget" data-pattern-target="{{ $patternId }}">
        <div class="pattern-lock-grid" role="group" aria-label="{{ $patternLabel }}">
            @foreach(range(1, 9) as $dot)
                <button type="button" class="pattern-dot" data-dot="{{ $dot }}">{{ $dot }}</button>
            @endforeach
        </div>
        <div class="pattern-lock-meta">
            <span class="pattern-sequence">Selected pattern: <strong data-pattern-preview>None</strong></span>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-pattern-clear>Clear Pattern</button>
        </div>
        <div class="form-text">Draw or tap at least 4 dots. Example demo pattern: `1-2-5-8`.</div>
    </div>
</div>
