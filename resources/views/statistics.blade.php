@extends('base_layout')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('js/statistics.js') }}" defer></script>
@endsection

@section('pageHeading')
    FiMan
@endsection

@section('content')
    <div>
        <canvas id="expensesAmountPerCategoryCurrentMonth"></canvas>
    </div>
@endsection
