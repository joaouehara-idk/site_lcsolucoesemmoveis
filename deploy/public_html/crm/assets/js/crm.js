function toggleSegDropdown() {
    var menu = document.getElementById('segDropdownMenu');
    menu.classList.toggle('show');
    document.getElementById('segDropdown').classList.toggle('active');
}
document.addEventListener('click', function(e) {
    var dd = document.getElementById('segDropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('segDropdownMenu').classList.remove('show');
        dd.classList.remove('active');
    }
});

function navegarCidade() {
    var sel = document.getElementById('navCidade');
    var cidade = sel.value;
    var url = new URL(window.location.href, window.location.origin);
    if (cidade) url.searchParams.set('cidade', cidade);
    else url.searchParams.delete('cidade');
    window.location.href = url.toString();
}
