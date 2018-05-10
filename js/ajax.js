function show() {
    jQuery.ajax({
        url: "run.php",
        cache: false,
        success: function (html) {
            $("#content").html(html);
        }
    });
}

$(document).ready(function () {
    // Пагинация
    $("#content").on("click", ".pagination a", function (e) {
        e.preventDefault();
        $(".loading-div").show();
        var page = $(this).attr("data-page");
        $("#content").load("run.php", {"page": page}, function () {
            $(".loading-div").hide();
        });
    });

    // Добавляем новую запись
    $('#myForm').submit(function () {
        jQuery.ajax({
            type: "POST",
            url: "run.php",
            data: "text=" + $("#text").val(),
            success: function (html) {
                $("#content").html(html);
            }
        });
        return false;
    });

    // Удаляем запись
    $("body").on("click", "#content .del_button", function (e) {
        e.preventDefault();
        var clickedID = this.id.split("-"); //Разбиваем строку (Split работает аналогично PHP explode)
        var DbNumberID = clickedID[1]; //и получаем номер из массива
        var myData = 'recordToDelete=' + DbNumberID; //выстраиваем  данные для POST

        jQuery.ajax({
            type: "POST", // HTTP метод  POST или GET
            url: "run.php", //url-адрес, по которому будет отправлен запрос
            dataType: "text", // Тип данных
            data: myData, //post переменные
            success: function (response) {
                // в случае успеха, скрываем, выбранный пользователем для удаления, элемент
                $('#item_' + DbNumberID).fadeOut("slow");
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //выводим ошибку
                alert(thrownError);
            }
        });
    });


    show();
    //setInterval('show()', 1000);
});
