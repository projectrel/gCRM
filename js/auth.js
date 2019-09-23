$('#login-form').submit((event) => {
    event.preventDefault();
    if ($("#passwordField").val().length == 0 || $("#loginField").val().length == 0) return;
    $(".loader").show();
    $(".login-form-submit").prop("disabled", true);
    let password = $("#passwordField").val();
    let login = $("#loginField").val();
    $.ajax({
        url: "../api/auth/auth.php",
        type: "POST",
        data: {
            password: password,
            login: login
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            $('.loader').fadeOut('fast');
            switch (res.error || res.status) {
                case 'sales':
                    window.location.href = '../content/orders.php';
                    break;
                case "success":
                    window.location.href = '../index.php';
                    break;
                case "login":
                    $('.login-form #loginField').addClass('shaking');
                    setTimeout(function () {
                        $('.login-form #loginField').removeClass('shaking');
                        setTimeout
                    }, 1000);
                    break;
                case "pass":
                    $('.login-form #passwordField').addClass('shaking');
                    setTimeout(function () {
                        $('.login-form #passwordField').removeClass('shaking');
                        setTimeout
                    }, 1000);
                    break;
                case "inactive":
                    $('#user-inactive-modal').modal();
                    break;

            }

        },
        error: function () {
        },
        complete: function () {
            setTimeout(function () {
                $(".login-form-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });

});