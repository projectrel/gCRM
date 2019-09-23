$(document).ready(function () {
    $('#branch-t').click(function () {
        $('.loader').show();
        $.ajax({
            url: "../api/select/getBranches.php",
            type: "POST",
            data: "req=ok",
            cache: false,
            dataType: 'JSON',
            success: function (res) {
                if ($('#Change-Branch-Modal #changeBranchField').empty()) {
                    $('#Change-Branch-Modal #changeBranchField').append(`<option selected disabled value = ${res['current']["id"]}>${res['current']["name"]}</option>`);
                    if (res['other'])
                        res['other'].forEach((el) => {
                            $('#Change-Branch-Modal #changeBranchField').append(`<option value = ${el["id"]}>${el["name"]}</option>`)
                        });
                }
                $('.loader').fadeOut('fast');
                $('#Change-Branch-Modal').modal();
            },
            error: function () {

            },

        });

    })
    $.validate({
        form: '#change-branch-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            changeBranch();
            return false;
        }
    });

    function changeBranch() {
        $('.loader').show();
        let branch_id = $('#Change-Branch-Modal #changeBranchField').val();
        $.ajax({
            url: "../api/operate/branch.php",
            type: "POST",
            data: {branch_id},
            cache: false,
            dataType: "JSON",
            success: function (res) {
                console.log(res);
                if (res.status === 'change-success') {
                    $.modal.close();
                    location.reload();
                } else {
                    createAlertTable(res.error);
                }
            },
            error: function () {
                createAlertTable();

            },
            complete: function () {
                $('.loader').fadeOut('fast');
            }

        })
        ;
    };

    function createAlertTable() {
        if ($('.custom-alert').hasClass('custom-alert--active'))
            $('.custom-alert').removeClass('custom-alert--active');
        if ($('.custom-alert').hasClass('bg-green')) $('.custom-alert').removeClass('bg-green');
        $('.custom-alert .alert-text-box').text('Что-то пошло не так. Попробуйте еще раз');


        setTimeout(function () {
            $('.custom-alert').addClass('custom-alert--active');
        }, 300);

    }
});