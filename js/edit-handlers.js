//Order

const vg = $('#Order-edit-Modal #editVgField');
vg.change(function (e) {
    let vg_id = vg.val();
    if (vg_id) {
        const optionSelected = $("option:selected", '#Order-edit-Modal #editVgField');
        const perc = optionSelected.attr('percent');
        $('#editOutField').val(perc);
    }
});

$.validate({
    form: '#edit-order-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editOrder();
        return false;
    }
});

function editOrder() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let order_id = $('#edit-order-form #edit-order-title').attr('order-id');
    let sum_vg = $("#edit-order-form #editSumVGField").val();
    let debt = $("#edit-order-form #editDebtClField").val() || 0;
    let referral = $("#edit-order-form #editLastNameField").val();
    let rollback_1 = $("#edit-order-form #editRollback1Field").val();
    let out = $("#edit-order-form #editOutField").val();
    let descr = $("#edit-order-form #editCommentField").val();
    let vg_id = $("#edit-order-form #editVgField").val();
    let callmaster = $('#edit-order-form #editCallmasterField').val();
    let method_id = $('#edit-order-form #editObtainingField').val();
    let client_id = $("#edit-order-form #editClientField").val();
    let fiat = $('#edit-order-form #editFiatField').val();
    let sharesEls = $('#edit-order-form .edit-owner-percent-input');
    const allShares = [];
    sharesEls.each(function () {
        allShares.push({value: $(this).val(), owner_id: $(this).attr('owner-id')});
    });
    const shares = allShares.filter((el) => el.value > 0);
    $.ajax({
        url: "../api/edit/order.php",
        type: "POST",
        dataType: "JSON",
        data: {
            order_id,
            client_id,
            sum_vg,
            debt,
            referral,
            rollback_1,
            out,
            vg_id,
            shares,
            callmaster,
            method_id,
            descr,
            fiat,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Заказ');
                return;
            }
            if (!res.sumChanged) {
                createAlertTable(res.status, "Заказ");
                return;
            }

            createAlertTable(res.status, "Заказ");
            $('#Order-sum-changed-modal .old-sum').text(res.oldSum);
            $('#Order-sum-changed-modal .new-sum').text(res.newSum);
            $('#Order-sum-changed-modal').modal();
        },
        error: function () {
            createAlertTable("connectionError", "Заказ");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}

$('#Order-sum-changed-modal').on($.modal.BEFORE_CLOSE, function (event, modal) {
    location.reload();
});

//User
$.validate({
    form: '#edit-user-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editUser();
        return false;
    }
});

function editUser() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let password = $("#edit-user-form #editPassField").val();
    let login = $("#edit-user-form #editLoginField").val();
    let first_name = $("#edit-user-form #editFirstNameField").val();
    let last_name = $("#edit-user-form #editLastNameField").val();
    let branch = $("#edit-user-form #editBranchField").val();
    let money = $("#edit-user-form #editMoneyField").val();
    let role = $("#edit-user-form #editRoleField").val();
    let id = $("#edit-user-form #edit-user-title").attr('user-id');
    let telegram = $("#edit-user-form #telegram").val();
    $.ajax({
        url: "../api/edit/user.php",
        type: "POST",
        dataType: "JSON",
        data: {
            password: password.length ? password : null,
            login,
            first_name,
            last_name,
            branch,
            money,
            role,
            user_id: id,
            telegram,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Пользователь');
                return;
            }
            createAlertTable(res.status, "Пользователь");
        },
        error: function () {
            createAlertTable("connectionError", "Пользователь");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}

// Outgo Type
$.validate({
    form: '#edit-outgo-type-form',
    modules: 'security',
    lang: 'ru',
    onSuccess: function () {
        editOutgoType();
    }
});

//Method of obtaining
$.validate({
    form: '#method-of-obtaining-edit-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editMethodOfObtaining();
        return false;
    }
});


function editOutgoType() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    const nameInput = $("#edit-outgo-type-form #name-edit");
    const name = nameInput.val();
    const id = nameInput.attr('itemid');
    $.ajax({
        url: "../api/edit/outgoType.php",
        type: "POST",
        data: {
            name,
            id
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Тип расходов");
                return;
            }
            createAlertTable(res.status, "Тип расходов");
        },
        error: function () {
            createAlertTable("connectionError", "Тип расходов");
        }
    });
}

function editMethodOfObtaining() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    const method_name = $("#method-of-obtaining-edit-form #method-edit-name").val();
    const method_id = $('#method-of-obtaining-edit-form .modal-title').attr('method-id')
    $.ajax({
        url: "../api/edit/methodOfObtaining.php",
        type: "POST",
        dataType: "json",
        data: {
            method_name,
            method_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Метод оплаты');
                return;
            }
            createAlertTable(res.status, "Метод оплаты");
        },
        error: function () {
            createAlertTable("connectionError", "Метод оплаты");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}


//Client
$.validate({
    form: '#edit-client-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editClient();
        return false;
    }
});

function editClient() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let phone = $("#edit-client-form #editPhoneField").val();
    let byname = $("#edit-client-form #editBynameField").val();
    let telegram = $("#edit-client-form #editTgField").val();
    let first_name = $("#edit-client-form #editFirstNameField").val();
    let last_name = $("#edit-client-form #editLastNameField").val();
    let description = $("#edit-client-form #editDescriptionField").val();
    let email = $("#edit-client-form #editEmailField").val();
    let rollback = $("#edit-client-form #editRollbackField").val();
    let debt = $("#edit-client-form #editDebtField").val();
    let id = $("#edit-client-form #edit-client-title").attr('client-id');
    const $this = $("#edit-client-form .modal-submit");
    let password = $("#edit-client-form #editPasswordField").val();
    let payment_system = $("#edit-client-form #payment_system").is(':checked');
    let pay_in_debt = $("#edit-client-form #pay_in_debt").is(':checked');
    let pay_page = $("#edit-client-form #pay_page").is(':checked');
    let max_debt = $("#edit-client-form #editMaxDebtField").val();
    $.ajax({
        url: "../api/edit/client.php",
        type: "POST",
        dataType: "JSON",
        data: {
            description,
            email,
            first_name,
            last_name,
            byname,
            rollback,
            debt,
            phone,
            telegram,
            client_id: id,
            password, pay_in_debt,
            pay_page,
            payment_system,
            max_debt,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Пользователь');
                return;
            }
            createAlertTable(res.status, "Пользователь");
        },
        error: function () {
            createAlertTable("connectionError", "Пользователь");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}

//VG
$.validate({
    form: '#edit-vg-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editVG();
        return false;
    }
});

function editVG() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let name = $("#edit-vg-form #editNameField").val();
    let in_percent = $("#edit-vg-form #editInField").val();
    let out_percent = $("#edit-vg-form #editOutField").val();
    let url = $("#edit-vg-form #editUrlField").val();
    let key = $("#edit-vg-form #editKeyField").val();
    let id = $("#edit-vg-form #edit-vg-title").attr('vg-id');
    $.ajax({
        url: "../api/edit/vg.php",
        type: "POST",
        data: {
            name,
            out_percent,
            in_percent,
            url,
            key,
            vg_id: id,
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'VG');
                return;
            }
            createAlertTable(res.status, "VG");
        },
        error: function () {
            createAlertTable("connectionError", "VG");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}


//Fiat
$.validate({
    form: '#edit-fiat-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editFiat();
        return false;
    }
});

function editFiat() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let name = $("#edit-fiat-form #editNameFiatField").val();
    let full_name = $("#edit-fiat-form #editFullNameFiatField").val();
    let code = $("#edit-fiat-form #editCodeField").val();
    let fiat = $("#edit-fiat-form #edit-fiat-title").attr('fiat-id');
    $.ajax({
        url: "../api/edit/fiat.php",
        type: "POST",
        data: {
            name,
            full_name,
            code,
            fiat,
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Валюта');
                return;
            }
            createAlertTable(res.status, "Валюта");
        },
        error: function () {
            createAlertTable("connectionError", "Fiat");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}


//Branch
$.validate({
    form: '#edit-branch-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editBranch();
        return false;
    }
});

function editBranch() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let name = $("#edit-branch-form #editNameField").val();
    let money = $("#edit-branch-form #editMoneyField").val();
    let id = $("#edit-branch-form #edit-branch-title").attr('branch-id');
    $.ajax({
        url: "../api/edit/branch.php",
        type: "POST",
        data: {
            name,
            branch_id: id,
            money,
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'Пользователь');
                return;
            }
            createAlertTable(res.status, "Пользователь");
        },
        error: function () {
            createAlertTable("connectionError", "Пользователь");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}

//Project
$.validate({
    form: '#edit-project-form',
    modules: 'security',
    lang: 'ru',
    onSuccess: function () {
        editProject();
        return false;
    },
    onError: function () {
    }
});


function editProject() {
    $('.loader').show();
    $(".modal-submit").prop("disabled", true);
    let name = $("#edit-project-form #editNameProjectField").val();
    let id = $("#edit-project-form #edit-project-title").attr('project-id');
    $.ajax({
        url: "../api/edit/project.php",
        type: "POST",
        data: {
            name,
            project_id: id,
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Проект");
                return;
            }
            createAlertTable(res.status, "Проект");
        },
        error: function () {
            createAlertTable("connectionError", "Проект");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });

}

//GlobalVG
$.validate({
    form: '#edit-globalVg-form',
    modules: '',
    lang: 'ru',
    onSuccess: function () {
        editGlobalVG();
        return false;
    }
});

function editGlobalVG() {
    $(".loader").show();
    $(".modal-submit").prop("disabled", true);
    let name = $("#edit-globalVGName").val();
    let vg_id = $("#edit-globalVG-title").attr('vg-id');
    $.ajax({
        url: "../api/edit/global.php",
        type: "POST",
        data: {
            vg_id,
            name
        },
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, 'VG');
                return;
            }
            createAlertTable(res.status, "VG");
        },
        error: function () {
            createAlertTable("connectionError", "VG");
        },
        complete: function () {
            setTimeout(function () {
                $(".modal-submit").prop("disabled", false);
                $(".loader").fadeOut("slow");
            }, 100);
        }
    });
}

function createAlertTable(alertType, text) {
    if ($('.custom-alert').hasClass('custom-alert--active'))
        $('.custom-alert').removeClass('custom-alert--active');
    if ($('.custom-alert').hasClass('bg-green')) $('.custom-alert').removeClass('bg-green');
    switch (alertType) {
        case "exists":
            $('.custom-alert .alert-text-box').text(`${text} с таким логином уже существует`);
            break;
        case "success":
            $('.custom-alert .alert-text-box').text(`${text} успешно изменен`);
            $('.custom-alert').addClass('bg-green');
            $.modal.close();
            setTimeout(function () {
                location.reload();
            }, 1500);
            break;
        case "edit-success":
            $('.custom-alert .alert-text-box').text(`Изменения сохранены`);
            $('.custom-alert').addClass('bg-green');
            $.modal.close();
            setTimeout(function () {
                location.reload();
            }, 2500);
            break;
        case "success-replenish":
            $('.custom-alert .alert-text-box').text(`Деньги успешно зачислены`);
            $('.custom-alert').addClass('bg-green');
            $.modal.close();
            break;
        case "failed":
            $('.custom-alert .alert-text-box').text('Что-то пошло не так. Попробуйте еще раз');
            break;
        case "denied":
            $('.custom-alert .alert-text-box').text('Недостаточно прав доступа');
            break;
        case "empty":
            $('.custom-alert .alert-text-box').text('Введены не все данные');
            break;
        case "connectionError":
            $('.custom-alert .alert-text-box').text('Ошибка сети. Перезагрузите страницу и попробуйте еще раз');
            break;
    }
    setTimeout(function () {
        $('.custom-alert').addClass('custom-alert--active');
    }, 300);

}
