@extends('base_layout')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('js/statistics.js') }}" defer></script>
@endsection

@section('pageHeading')
    FiMan
@endsection

@section('content')
    <script>
        const allCategories = @json(session('allCategories'));
        const expensesAmountPerCategoryCurrentMonth = @json(session('expensesAmountPerCategoryCurrentMonth'));
        const expensesAmountPerCategoryPerMonthLast12Months = @json(session('expensesAmountPerCategoryPerMonthLast12Months'));
    </script>

    <div class="container">
        <div class="row">
            <div class="col">
                <canvas id="CanvasExpensesAmountPerCategoryCurrentMonth"></canvas>
            </div>
            <div class="col-1"></div>
            <div class="col">
                <canvas id="CanvasExpensesAmountPerCategoryCurrentMonth2"></canvas>
            </div>
        </div>
    </div>
@endsection
