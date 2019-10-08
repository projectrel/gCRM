$(document).ready(function () {
    $('#replenish-fiat-btn').click(function () {
        $('.loader').show();
        $.ajax({
            url: "../api/select/methods.php",
            type: "GET",
            data: "req=ok",
            cache: false,
            dataType: 'JSON',
            success: function (res) {
                console.log(res);
                if ($('#replenish-fiat-Modal #replenishMethodSelect').empty()) {
                    if(res.error){
                        createAlertTable(res.error, "Данные фиата");
                        return;
                    }
                        res.forEach((el) => {
                            $('#replenish-fiat-Modal #replenishMethodSelect').append(`<option value = ${el["method_id"]}>${el["full_name"]}</option>`)
                        });
                }
                $('.loader').fadeOut('fast');
                $('#replenish-fiat-Modal').modal();
            },
            error: function () {

            },

        });
        $.ajax({
            url: "../api/select/owners.php",
            type: "POST",
            data: "req=ok",
            cache: false,
            dataType: 'JSON',
            success: function (res) {
                if ($('#replenish-fiat-Modal #replenishOwnerSelect').empty()) {
                    if(res.error){
                        createAlertTable(res.error, "Данные фиата");
                        return;
                    }                        res.forEach((el) => {
                            $('#replenish-fiat-Modal #replenishOwnerSelect').append(`<option value = ${el["id"]}>${el["full_name"]}</option>`)
                        });
                }
                $('.loader').fadeOut('fast');
                $('#replenish-fiat-Modal').modal();
            },
            error: function () {

            },

        });

    });
    $.validate({
        form: '#replenish-fiat-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            replenishFiat();
            // return false;
        }
    });

    function replenishFiat() {
        $('.loader').show();
        const method_id = $('#replenish-fiat-Modal #replenishMethodSelect').val();
        const sum = $('#replenish-fiat-Modal #replenishFiatSum').val();
        const ownerfield = $('#replenish-fiat-Modal #replenishOwnerSelect');
        const owner = ownerfield.val() ? ownerfield.val() : 0;

        $.ajax({
            url: "../api/operate/replenishFiat.php",
            type: "POST",
            method: "POST",
            data: {
                method_id,
                sum,
                owner,
            },
            cache: false,
            success: function (res) {
                if (res.status === 'success-replenish') {
                    createAlertTable(res);
                } else {
                    createAlertTable(res.error);
                }
                $('.loader').fadeOut('fast');
            },
            error: function (res) {
                createAlertTable();
                $('.loader').fadeOut('fast');
            },

        })
        ;
    };


});