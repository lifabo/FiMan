@extends('base_layout')

@section('head')
    <script src="{{ asset('js/categoryFunctionalities.js') }}" defer></script>
@endsection

@section('pageHeading')
    Kategorien
@endsection

@section('pageDescription')
    Jede Ausgabe ist einer bestimmten Kategorie zugewiesen, die du selbst definieren kannst. Hier kannst du all deine
    Kategorien verwalten: neue hinzufügen, bestehende bearbeiten oder löschen.
@endsection

@section('content')
    <script>
        // in case creation oder editing of category fails, modal should stay open and display an error message
        const shouldOpenModal = @json(session('shouldOpenModal'));
        const confirmDeleteTitle = @json(session('confirmDeleteTitle'));
        const showAlert = @json(session('showAlert'));
    </script>

    <button type="button" id="btnOpenAddModal" class="btn btn-primary mb-4" data-bs-toggle="modal"
        data-bs-target="#categoryModal">Kategorie erstellen</button>

    <div id="alertDiv" class="alert alert-success d-none" role="alert">{{ session('status') }}</div>

    <div class="table-responsive">
        <table class="table table-white table-hover table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="col-1 text-center">Bearbeiten</th>
                    <th class="col-1 text-center">Löschen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->title }}</td>

                        <td class="text-center">
                            <a class="btn btn-primary" href="{{ route('category.edit', ['id' => $category->id]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-pen" viewBox="0 0 16 16">
                                    <path
                                        d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
                                </svg>
                            </a>
                        </td>

                        <td class="text-center">
                            <form action="{{ route('category.delete', ['id' => $category->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path
                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                        <path
                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="categoryModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategorie
                        {{ session('shouldOpenModal') == 'edit' ? 'bearbeiten' : 'erstellen' }}</h5>
                </div>

                <form action="{{ session('shouldOpenModal') == 'edit' ? '/verifyCategoryEditing' : '/addCategory' }}"
                    method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="txtTitle" class="py-2">Name der Kategorie</label>
                            <input id="txtTitle" type="text" class="form-control" aria-describedby="titleUniqueInfo"
                                name="title"
                                placeholder="{{ session('shouldOpenModal') == 'edit' ? session('title') : 'z.B. Urlaub' }}"
                                autocomplete="off" required value="{{ session('title') }}">

                            {{-- <small class="form-text text-muted" id="titleUniqueInfo">Beachte, dass du nicht zwei
                                Kategorien mit dem
                                selben Namen erstellen kannst.</small> --}}
                        </div>

                        <p class="text-danger mb-0 mt-3">{{ session('modalStatus') }}</p>
                    </div>

                    <div class="modal-footer">
                        <button id="btnDismissChanges" class="btn btn-danger" type="button" data-bs-dismiss="modal">
                            Verwerfen
                        </button>

                        <button class="btn btn-primary"
                            type="submit">{{ session('shouldOpenModal') == 'edit' ? 'Bearbeiten' : 'Erstellen' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Löschen bestätigen</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-0">Die Kategorie wird noch in
                        {{ session('usageCount') }} Ausgaben verwendet.</p>

                    <label for="txtTitleDelete" class="py-2">Bist du dir sicheeeeer?</label>

                    <input id="txtTitleDelete" type="text" class="form-control" aria-describedby="titleUniqueInfo"
                        placeholder="{{ session('confirmDeleteTitle') }}" autocomplete="off" required>

                    <small class="form-text text-muted" id="titleUniqueInfo">Zum löschen tippe bitte
                        "{{ session('confirmDeleteTitle') }}" ein.</small>
                </div>
                <div class="modal-footer">
                    <button id="btnDismissDelete" class="btn btn-primary" type="button"
                        data-bs-dismiss="modal">Abbrechen</button>

                    <form action="/confirmCategoryDeletion" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger disabled" id="btnConfirmDelete" type="submit">Löschen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
