@extends('base_layout')

@section('head')
    <script src="{{ asset('js/bankAccountFunctionalities.js') }}" defer></script>
@endsection

@section('pageHeading')
    Konten
@endsection

@section('pageDescription')
    Hier kannst du Konten verwalten, damit du diesen dann Ausgaben hinzufügen kannst. <br>
    Ein Konto ist einfach ein Zusammenschluss von Ausgaben, es muss kein real existierendes Bankkonto von dir sein, sondern
    du kannst bspw. auch ein Konto "Bargeld" erstellen und darin alle Bargeld-Ausgaben eintragen.
@endsection

@section('content')
    <script>
        // in case creation oder editing of category fails, modal should stay open and display an error message
        const shouldOpenModal = @json(session('shouldOpenModal'));
        const showAlert = @json(session('showAlert'));
        const successAlert = @json(session('successAlert'));

        console.log(successAlert);
        console.log("hall");
    </script>

    <button type="button" id="btnOpenAddModal" class="btn btn-primary mb-4" data-bs-toggle="modal"
        data-bs-target="#bankAccountModal">Konto erstellen</button>

    <div class="text-center">
        <p id="txtBalance" class="fw-bolder display-3 mb-0 {{ $balanceAllAccounts < 0 ? 'text-danger' : 'text-success' }}">
            {{ number_format($balanceAllAccounts, 2, ',', '.') }} €</p>
        <small id="lblBankAccountBalance" for="txtBalance" class="fw-bold form-text text-muted">Kontostand von allen Konten
            zusammen
        </small>
    </div>

    <div id="alertDiv" class="alert d-none" role="alert">{{ session('status') }}</div>

    <div class="table-responsive mt-4">
        <table class="table table-white table-hover table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Beschreibung</th>
                    <th class="text-center">Kontostand</th>
                    <th class="col-1 text-center">Bearbeiten</th>
                    <th class="col-1 text-center">Löschen</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bankAccounts as $bankAccount)
                    <tr>
                        <td class="text-center align-middle">{{ $bankAccount->title }}</td>
                        <td class="text-center align-middle">{{ $bankAccount->description }}</td>
                        <td
                            class="text-center align-middle fw-bolder {{ $bankAccount->balance < 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($bankAccount->balance, 2, ',', '.') }} €</td>
                        <td class="text-center">
                            <a class="btn btn-primary" href="{{ route('bankAccount.edit', ['id' => $bankAccount->id]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-pen" viewBox="0 0 16 16">
                                    <path
                                        d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
                                </svg>
                            </a>
                        </td>

                        <td class="text-center">
                            <form action="{{ route('bankAccount.delete', ['id' => $bankAccount->id]) }}" method="post">
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

    <div id="bankAccountModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konto
                        {{ session('shouldOpenModal') == 'edit' ? 'bearbeiten' : 'erstellen' }}</h5>
                </div>

                <form action="{{ session('shouldOpenModal') == 'edit' ? '/verifyBankAccountEditing' : '/addBankAccount' }}"
                    method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="inpTitle" class="py-2">Name</label>
                            <input type="text"
                                placeholder="{{ session('shouldOpenModal') == 'edit' ? session('title') : 'z.B. Postbank' }}"
                                value="{{ session('title') }}" class="form-control" name="title" id="inpTitle"
                                autocomplete="off" required>

                            <label for="inpDescription" class="py-2">Beschreibung</label>
                            <textarea name="description"
                                placeholder="{{ session('shouldOpenModal') == 'edit' ? session('description') : 'z.B. Mietkonto' }}"
                                class="form-control" id="inpDescription" rows="3" autocomplete="off">{{ session('description') }}</textarea>
                            @if (session('shouldOpenModal') == 'edit')
                                <label for="txtBalance" class="py-2">Kontostand</label>
                                <input type="number" placeholder="{{ session('balance') }}"
                                    value="{{ session('balance') }}" class="form-control" id="txtBalance" disabled>

                                <small for="txtBalance" class="form-text text-muted">Der Kontostand wird automatisch
                                    durch das Verwalten der Ausgaben aktualisiert.</small>
                            @endif
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


    <div id="confirmDeleteModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Löschen bestätigen</h5>
                </div>
                <div class="modal-body">
                    @if (session('usageCount') > 0)
                        <p class="text-danger">Das Konto wird noch in
                            {{ session('usageCount') }} Ausgaben verwendet.</p>

                        <p>Wenn du das Konto jetzt löschst, werden alle Ausgaben unwiderruflich mit gelöscht.</p>
                    @else
                        <p class="text-success">Das Konto wird aktuell in keiner Ausgabe verwendet, du kannst es
                            also ohne Probleme löschen.</p>
                    @endif

                    <p>Bist du dir sicheeeeer?</p>
                </div>
                <div class="modal-footer">
                    <button id="btnDismissDelete" class="btn btn-primary" type="button"
                        data-bs-dismiss="modal">Abbrechen</button>

                    <form action="/confirmBankAccountDeletion" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" id="btnConfirmDelete" type="submit">Löschen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
