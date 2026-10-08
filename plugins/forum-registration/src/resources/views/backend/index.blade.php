@extends('cms::layouts.backend')

@section('content')

    <div class="row mb-3">
        <div class="col-md-3 mb-3">
            <div class="card p-3 shadow-sm border-0 bg-primary text-white text-center rounded h-100">
                <h5 class="mb-1 text-white"><i class="fa fa-users mr-2"></i> Total Registrations</h5>
                <h3 class="mb-0 font-weight-bold text-white">{{ $totalRegistrations ?? 0 }}</h3>
            </div>
        </div>
        @if(isset($statsByPartType))
            @php
                $typeStyles = [
                    'حضور' => ['color' => 'info', 'icon' => 'fa-id-badge'],
                    'متحدث' => ['color' => 'success', 'icon' => 'fa-microphone'],
                    'راعٍ' => ['color' => 'warning', 'icon' => 'fa-handshake-o'],
                    'شريك استراتيجي' => ['color' => 'danger', 'icon' => 'fa-star']
                ];
            @endphp
            @foreach($typeStyles as $type => $style)
                @php
                    $count = $statsByPartType[$type] ?? 0;
                @endphp
                <div class="col-md-2 mb-3">
                    <div class="card p-3 shadow-sm border-0 bg-{{ $style['color'] }} text-white text-center rounded h-100">
                        <h6 class="mb-2 text-white"><i class="fa {{ $style['icon'] }} mr-1"></i> {{ $type }}</h6>
                        <h4 class="mb-0 font-weight-bold text-white">{{ $count }}</h4>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-12 text-right">
            <a href="{{ route('admin.forum-registrations.export') }}" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i> Export to Excel
            </a>
        </div>
    </div>

    {{ $dataTable->render() }}
@endsection
