@extends('base_layout')

@section('head')
    <script src="{{ asset('js/app.js') }}" defer></script>
@endsection

@section('pageHeading')
    Kategorien
@endsection

@section('content')
    <script>
        // in case creation oder editing of category fails, modal should stay open and display an error message
        const shouldOpenModal = @json(session('shouldOpenModal'));
    </script>

    <button id="btnOpenAddModal" class="btnBase">Kategorie hinzufügen</button>
    <table class="tblCategories">
        <thead>
            <tr>
                <th>Name</th>
                <th>Bearbeiten</th>
                <th>Löschen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->title }}</td>
                    <td><a href="{{ route('category.edit', ['id' => $category->id]) }}"><img src="/img/edit_darkmode.png"
                                class="tblIcons"></a></td>

                    {{-- <td><a href="{{ route('category.delete', ['id' => $category->id]) }}"><img src="/img/delete_darkmode.png"
                                class="tblIcons"></a></td> --}}
                    <td>
                        <form action="{{ route('category.delete', ['id' => $category->id]) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit"><img src="/img/delete_darkmode.png" class="tblIcons"></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <dialog add-dialog>
        <form action="/addCategory" method="post" class="">
            @csrf
            <label for="txtName">Name:</label>
            <input id="txtName" type="text" name="title" placeholder="Name" autofocus autocomplete="off"
                value="{{ session('title') }}">
            <button type="submit">Kategorie erstellen</button>
            <button type="submit" formmethod="dialog">Änderungen verwerfen</button>
        </form>

        {{ session('modalStatus') }}
    </dialog>

    <dialog edit-dialog>
        <form action="/verifyCategoryEditing" method="post" class="">
            @csrf
            <label for="txtName">Name:</label>
            <input id="txtName" type="text" name="title" placeholder="Name" autofocus autocomplete="off"
                value="{{ session('title') }}">
            <button type="submit">Kategorie bearbeiten</button>
            <button type="submit" formmethod="dialog">Änderungen verwerfen</button>
        </form>

        {{ session('modalStatus') }}
    </dialog>

    {{ session('status') }}
@endsection
