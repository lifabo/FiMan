$(document).ready(function () {
    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $('#categoryModal').modal('show');
    }

    // set autofocus
    const categoryModal = document.getElementById('categoryModal');
    const myInput = document.getElementById('txtName');

    categoryModal.addEventListener('shown.bs.modal', () => {
        myInput.focus()
    });
});
