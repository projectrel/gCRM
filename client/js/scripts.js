let storage = [];
$(document).ready(function () {
    $('.loader').fadeOut();
    $('#login-btn').click(() => {
        $('.loader').show();
        const vgSum = $('#vg-sum').val();
        if (vgSum == "" || vgSum == " " || vgSum < 1) {
            if (!$('#pay-form').find('.alert-warning').length)
                $('#pay-form').append('<div class="alert alert-warning">\n' +
                    '  <strong>Ошибка! </strong>Введена некорректная сумма\n' +
                    '</div>');
            $('.loader').fadeOut();
            return;
        } else {
            $('#pay-form').find('.alert-warning').remove();
        }
        const login = $('#login').val();
        $.get('./api/getClientInfo.php', {login, vgSum}, (res) => {
            if (res.error) {
                let msg = "";
                switch (res.error) {
                    case "deal denied":
                        msg = "Функция самостоятельной покупки отключена";
                        break;
                    case "not exists":
                        msg = "Неверный логин";
                        break;
                }
                if ($('#pay-form').find('.alert').length) {
                    $('#pay-form').find('.alert').remove();
                }
                $('#pay-form').append('<div class="alert alert-danger">\n' +
                    '  <strong>Ошибка! </strong>' + msg + '\n' +
                    '</div>');
                $('#login-box .input-group').effect("shake");
                $('.loader').fadeOut();
                return;
            } else {
                $('#pay-form').children('.alert-danger').remove();
                $('#login').attr('disabled', true);
                $('#login').parent().addClass('correct-info');
                $('#login-btn').remove();
            }
            parseLoginData(res);
            $('.loader').fadeOut();
        }, 'json');
    });

    $('#pass-btn').click(() => {
        $('.loader').show();
        const login = $('#login').val();
        const password = $('#pass').val();
        $.get('./api/approvePass.php', {login, password}, (res) => {
            if (res.error) {
                if (!$('#pay-form').find('.alert').length)
                    $('#pay-form').append('<div class="alert alert-danger">\n' +
                        '  <strong>Ошибка! </strong>Неверный пароль\n' +
                        '</div>');
                $('#pass-box .input-group').effect("shake");
                $('.loader').fadeOut();
                return;
            } else {
                $('#pay-form').children('.alert-danger').remove();
                $('#pass').attr('disabled', true);
                $('#pass-btn').remove();
                $('#pass').parent().addClass('correct-info');
            }
            parsePassData(res);
            $('.loader').fadeOut();
        }, 'json');
    });
    $('#pay-in-debt-btn').click(function () {
        createDeal(true);
    });
    $('#pay-system-btn').click(function () {
        createDeal();
    });
});
// function calcSum(res){
//     const perc = $('#vg-types').find(":selected").attr('perc');
//     if(!perc)
//         return;
//     $('#fiat-sum').text(+perc * +$('#vg-sum').val());
// }
function convertVgDataToList(data) {
    return data
        .map(row => `<option value="${row['vg_id']}" perc="${row['out_percent']}">${row['name']}</option>`)
        .join('\n');
}

function parseLoginData(res) {
    $('#vg-sum').prop('disabled', true);
    $('#vg-sum').parent().addClass('correct-info');
    if (+res.pay_page) {
        $('#pass-box').show();
    }

    $('#payment-info').append(`<p id="vg-label"><b>Криптовалюта</b>: <span class="big-text">${res.vgName}</span></p>`);
    $('#payment-info').append(`<p id="sum-label"><b>Стоимость</b>: <span class="big-text">${res.sum}</span> <b>${res.fiatName}</b></p>`);

    storage = res;
}

function parsePassData(res) {
    if (res.vgs) {
        $('#vg-type-box').show();
        $('#vg-type').append(convertVgDataToList(res.vgs)).change(function () {
            const vgt = $("option:selected", '#vg-type').text();
            const perc = $("option:selected", '#vg-type').attr('perc');
            $('#vg-label > span').text(vgt);
            $('#sum-label .big-text').html(+perc * +$('#vg-sum').val() / 100);
        })
    }
    if (+storage.debtLimit > 0) {
        if (+storage.debtLimit - +storage.sum < 0) {
            $('#payment-info').append(`<p id="debt-limit-label">Лимита оплаты в долг недостаточно</p>`);
        } else {
            $('#payment-info').append(`<p id="debt-limit-label"><b>Ваш лимит оплаты в долг:</b> <span class="big-text">${storage.debtLimit}</span> <b>${storage.fiatName}</b></p>`);
            $('#pay-in-debt-btn').show();
        }
    } else {
        $('#payment-info').append(`<p id="debt-limit-label">Ваш лимит оплаты в долг исчерпан</p>`);
    }
    $('#pay-system-btn').show();
}
function translateErrorsForClientPart(errorType){
    switch (errorType) {
        case "NO_SUCH_CLIENT":
            return "Клиента не существует";
        case "empty":
            return "Данные не заполнены";
        case "failed":
            return "Ошибка сервера";
        case "denied":
            return "Доступ запрещен";
        default:
            return "Неизвестная ошибка";
    }
}
function customAlert(errorType){
    alert(translateErrorsForClientPart(errorType));
}
function createDeal(debt = 0) {
    $('.loader').show();
    const login = $('#login').val();
    const password = $('#pass').val();
    const vg_sum = $('#vg-sum').val();
    const vg_type = $('#vg-type').val();
    $.post('./api/pay.php', {login, password, vg_sum, vg_type, debt}, (res) => {
        if (res.error) {
            $('.loader').fadeOut();
            customAlert(res.error);
            return false;
        }
        $('#pay-form').append(
            `
			<form style="display: none;" name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8">
			${Object.keys(res).map(key => (`
			  <input type="hidden" name="${key}" value="${res[key]}"/>

			`))}
  <input type="submit" value="Pay">
</form>
		 `
        );
        $('.loader').fadeOut();

        document.payment.submit();
        /*		else if (res['status'] === "success") {
                    if (!$('#pay-form').find('.alert-success').length)
                        $('#pay-form').append('<div class="alert alert-success">\n' +
                            '  <strong>Поздравляем! </strong>Транзакция прошла успешно\n' +
                            '</div>');
                    $('#pay-in-debt-btn').remove();
                    $('#pay-system-btn').remove();
                    $('#vg-type').prop('disabled', true);
                    $('.loader').fadeOut();
                    return false;
                }
                if (!$('#pay-form').find('.alert-danger').length)
                    $('#pay-form').append('<div class="alert alert-danger">\n' +
                        '  <strong>Ошибка! </strong>Что-то пошло не так. Обратитесь к администрации\n' +
                        '</div>');;*/
    }, 'json');
}

