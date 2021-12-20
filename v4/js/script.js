// --- CHECKED SORT BY ---
const menuToggle =  document.querySelector('.checkGroup input');
const ul = document.querySelector('.sort-by ul.listSort');

menuToggle.addEventListener('click', function () {
    ul.classList.toggle('listSortCheck');
})


// --- LIVE SEARCH ---
// ambil element yang dibutuhkan
var keyword = document.getElementById("keyword");
var tombolCari = document.getElementById("search");
var list = document.getElementById("list-group");

// tambahkan event ketika keyword ditulis
keyword.addEventListener('keyup', function() {
    console.log(keyword.value);

    // buat object ajax
    var ajax = new XMLHttpRequest();

    // mengecek kesiapan ajax
    ajax.onreadystatechange = function() {
        if( ajax.readyState == 4 && ajax.status == 200) {
            list.innerHTML = ajax.responseText;
        }
    }

    // eksekusi ajax
    // ajax.open('request method', 'sumber', 'sync or async')
    ajax.open('GET', 'ajax-livesearch.php?keyword=' + keyword.value, 'true');
    ajax.send();


});