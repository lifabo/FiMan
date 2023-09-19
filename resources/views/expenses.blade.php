@extends('base_layout')

@section('head')
    {{-- <script src="{{ asset('js/modalHandling.js') }}" defer></script> --}}
@endsection

@section('pageHeading')
    Ausgabenübersicht
@endsection

@section('content')
    <script>
        // in case creation oder editing of category fails, modal should stay open and display an error message
        const shouldOpenModal = @json(session('shouldOpenModal'));
    </script>

    <button id="btnOpenAddModal" class="btnBase">Ausgabe hinzufügen</button>
    <table class="tblExpenses">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Betrag</th>
                <th>Beschreibung</th>
                <th>Kategorie</th>
                <th>Bearbeiten</th>
                <th>Löschen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->timestamp }}</td>
                    <td>{{ $expense->amount }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->categoryTitle }}</td>
                    <td><a href="{{ route('expense.edit', ['id' => $expense->id]) }}"><img src="/img/edit_darkmode.png"
                                class="tblIcons"></a></td>

                    <td><a href="{{ route('expense.delete', ['id' => $expense->id]) }}"><img src="/img/delete_darkmode.png"
                                class="tblIcons"></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <dialog add-dialog>
        <form action="/addExpense" method="post" class="">
            @csrf
            <label for="inputDate">Datum:</label>
            <input id="inputDate" type="date" name="timestamp" autocomplete="off">

            <label for="txtAmount">Betrag</label>
            <input id="txtAmount" type="number" step="0.01" name="amount" autofocus autocomplete="off">

            <label for="txtDesc">Beschreibung</label>
            <input id="txtDesc" type="text" name="description" autocomplete="off">

            <select name="category" id="">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
            </select>

            <button type="submit">Ausgabe hinzufügen</button>
            <button type="submit" formmethod="dialog">Änderungen verwerfen</button>
        </form>

        {{ session('modalStatus') }}
    </dialog>

    <dialog edit-dialog>
        <form action="/verifyExpenseEditing" method="post" class="">
            @csrf
            <label for="inputDate">Datum:</label>
            <input id="inputDate" type="date" name="timestamp" value="{{ session('timestamp') }}" autocomplete="off">

            <label for="txtAmount">Betrag</label>
            <input id="txtAmount" type="number" name="amount" value="{{ session('amount') }}" autofocus
                autocomplete="off">

            <label for="txtDesc">Beschreibung</label>
            <input id="txtDesc" type="text" step="0.01" name="description" value="{{ session('description') }}"
                autocomplete="off">

            <select name="category">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if ($category->id == session('categoryID')) selected @endif>
                        {{ $category->title }}</option>
                @endforeach
            </select>

            <button type="submit">Ausgabe bearbeiten</button>
            <button type="submit" formmethod="dialog">Änderungen verwerfen</button>
        </form>

        {{ session('modalStatus') }}
    </dialog>
@endsection
