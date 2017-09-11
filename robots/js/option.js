$(document).ready(function() {

    // Once we click the spoiler button
    $(".spoiler_wrap input[type='button']").click(function() {
        // If the button's value is "Show", use the value "Hide"
        // But if it's not "Show", then change it back to "Show"
        var btn_txt = ($(this).val() == "Показать ссылки на статьи") ? "Спрятать" : "Показать ссылки на статьи";

        // Actually change the button's value
        $(this).val(btn_txt);

        // Go to HTML element directly after this button and slideToggle it
        $(this).next().stop().slideToggle();
    });

});