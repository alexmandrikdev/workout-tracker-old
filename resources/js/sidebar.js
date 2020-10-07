$(function () {
    $("#menu-toggle").on('click', function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
});
