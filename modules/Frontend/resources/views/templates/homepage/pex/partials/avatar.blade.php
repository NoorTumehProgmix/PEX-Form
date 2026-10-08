{{--
    صورة دائرية بإطار ذهبي لمتحدث/مدير جلسة، مع رجوع لحروف الاسم عند
    عدم وجود صورة. تُستخدم في بطاقات المتحدثين، أشخاص الأجندة، وداخل
    نافذة التعريف (profile-modal.blade.php).
--}}
@php
    $avatarInitials = collect(preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)
        ->map(fn ($word) => mb_substr($word, 0, 1))
        ->implode('');
@endphp
<div class="speaker-avatar{{ !empty($extraCls) ? ' ' . $extraCls : '' }}{{ !empty($photo) ? ' has-photo' : '' }}"
    data-initials="{{ $avatarInitials }}"
    @if (empty($photo)) aria-hidden="true" @endif>
    @if (!empty($photo))
        <img src="{{ $photo }}" alt="{{ $name }}{{ !empty($role) ? ' — ' . $role : '' }}" loading="lazy">
    @else
        {{ $avatarInitials }}
    @endif
</div>
