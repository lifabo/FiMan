// logic for opening modal
var btnOpenAddModal = document.getElementById("btnOpenAddModal");

btnOpenAddModal.addEventListener("click", function () {
    const dialog = document.querySelector("[add-dialog]");
    dialog.showModal();
});

// in case creation of category/expense fails, modal should stay open and display an error message
if (shouldOpenModal == "add") {
    const dialog = document.querySelector("[add-dialog]");
    dialog.showModal();
}
else if (shouldOpenModal == "edit") {
    const dialog = document.querySelector("[edit-dialog]");
    dialog.showModal();
}
