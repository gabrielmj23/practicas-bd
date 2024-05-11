/**
 * @param {string} id
 */
function openDialog(id) {
  var dialog = document.getElementById(id);
  if (dialog) {
    dialog.showModal();
  }
}
