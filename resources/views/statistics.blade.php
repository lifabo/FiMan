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
        const expensesMonthlyBalanceLast12Months = @json(session('expensesMonthlyBalanceLast12Months'));

        //console.log(expensesMonthlyBalanceLast12Months);
    </script>

    {{-- <div class="container"> --}}
    <div class="row mb-5">
        <div class="col">
            <canvas id="CanvasExpensesAmountPerCategoryCurrentMonthNegative"></canvas>
        </div>
        <div class="col-1"></div>
        <div class="col">
            <canvas id="CanvasExpensesAmountPerCategoryCurrentMonthPositive"></canvas>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-1"></div>
        <div class="col-10">
            <canvas id="CanvasExpensesAmountPerCategoryLast12Months"></canvas>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <canvas id="CanvasExpensesMonthlyBalanceLast12Months"></canvas>
        </div>
        <div class="col-1"></div>
    </div>
    {{-- </div> --}}
@endsection
