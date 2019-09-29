checkUserData();
$('tr').on('click', (e) => {
    const target = $(e.target);
    const mainParent = target.parent().parent();

    switch (target.attr('modal')) {
        case "#Debt-Modal":
            $('[href*="#Debt-Modal"]').first()[0].click();
            const debtorList = $('#debtorField');
            debtorList.val(target.parent().parent().attr('itemid'));
            const debtorSelected = $("option:selected", debtorList);
            const debtSum = debtorSelected.attr('sum');
            const input = $('#paybackField');
            $('#Debt-Modal #fiatField').val(debtorSelected.attr('fiat'));
            input.val(debtSum);
            input.attr('max', debtSum);
            input.attr('min', 0);
            break;
        case "#Rollback-Modal":
            $('[href*="#Rollback-Modal"]').first()[0].click();
            const referalList = $('#clientField');
            referalList.val(target.parent().parent().attr('itemid'));
            const referalSelected = $("option:selected", referalList);
            const rollbackSum = referalSelected.attr('sum');
            const input2 = $('#payField');
            input2.val(rollbackSum);
            $('#Rollback-Modal #fiatField').val(referalSelected.attr('fiat'));
            input2.attr('max', rollbackSum);
            input2.attr('min', 0);
            break;
        case "#Outgo-modal":
            $('#Outgo-Modal').modal();
            const tr = target.parent().parent().attr('itemid');
            const [id, fiat_id] = tr.split('-');
            $('#Outgo-Modal #sumField').val($(`.Owner [itemid='${tr}'] ${'.2-f'}`).attr('title'));
            $('#Outgo-Modal #ownerField').val(id);
            $('#Outgo-Modal #fiatField').val(fiat_id);

            // const referalList = $('#clientField');
            // referalList.val(target.parent().parent().attr('itemid'));
            // const referalSelected = $("option:selected", referalList);
            // const rollbackSum = referalSelected.attr('sum');
            // const input2 = $('#payField');
            // input2.val(rollbackSum);
            // $('#Rollback-Modal #fiatField').val(referalSelected.attr('fiat'));
            // input2.attr('max', rollbackSum);
            // input2.attr('min', 0);
            break;
        case "Owner-edit":
            fillOwnerEditForm();
            break;
        case "User-edit":
            fillUserEditForm(mainParent);
            break;
        case "Branch-edit":
            fillBranchEditForm(mainParent);
            break;
            case "Project-edit":
                fillProjectEditForm(mainParent);
            break;
        case "Client-edit":
            fillClientEditForm(mainParent);
            break;
        case "VG-edit":
            fillVGEditForm(mainParent);
            break;
        case "Order-edit":
            fillOrderEditForm(mainParent);
            break;
        case "Fiat-edit":
            fillFiatEditForm(mainParent);
            break;
        case "globalVG-edit":
            fillGlobalVGlInfo(mainParent);
            break;
        case "info":
            fillAdditionalInfo(mainParent);
            break;
        case "MethodsOfObtaining-edit":
            fillMethodOfObtainingInfo(mainParent);
            break;
        case "delete":
            deleteOwner(mainParent);
            break;
        default:
            break;
    }

});

function fillAdditionalInfo(target) {
    $('.loader').show();
    if ($('#table-wrapper').hasClass('Client')) {
        let client_id = target.attr('itemid');
        $.ajax({
            url: "../api/select/clientInfo.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                client_id,
            },
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, "Данные клиента");
                    return;
                }
                $('#info-client-form .modal-title').text(`Информация о клиенте`).attr('client-id', res['id']);
                let rollbacks = '<h4>Откаты</h4>' + res['rollback'].map(line => `<br/><p>${line["rollback"]} ${line["fiat"]}</p>`).join('') + '<br/>';
                rollbacks += '<h4>Долги</h4>' + res['debt'].map(line => `<br/><p>${line["debt"]} ${line["fiat"]}</p>`).join('');
                $('#info-client-form .text').html(rollbacks);
            },
            error: function () {
                $('#info-client-form .modal-title').text(`Нет информации про клиента`);
            },
            complete: function () {
                $('.loader').fadeOut('fast');
                $("#Client-info-modal").modal();

            }
        });
    } else {
        let order_id = target.attr('itemid');
        $.ajax({
            url: "../api/select/orderInfo.php",
            type: "POST",
            dataType: 'JSON',
            data: {
                order_id,
            },
            cache: false,
            success: function (res) {
                if (res.error) {
                    console.log(res);
                    createAlertTable(res.error, "Данные продажи");
                    return;
                }
                $('#info-order-form .modal-title').text(`Информация о продаже №${order_id}`).attr('order-id', res['id']);
                let owners = '<h4>Владельцы</h4>' + res.map(line => `<br/><p>${line["name"]} - ${line["sum"]}  ${res[0]["fiat"]} (${line["share_percent"]}%)</p>`).join('');
                const callmaster = `<br/><h4>Реферал:</h4><br/><p>${res[0]["callmaster"]} - ${res[0]["rollback_sum"]} ${res[0]["fiat"]} (${res[0]["rollback_1"]}%)</p>`;
                if (res[0]["callmaster"])
                    owners += callmaster;
                $('#info-order-form .text').html(owners);
                $("#Order-info-modal").modal();
            },
            error: function () {
                $('#info-order-form .modal-title').text(`Нет информации про продажу № ${order_id}`);
            },
            complete: function () {
                $('.loader').fadeOut('fast');
            }
        });
    }
}

function fillOrderEditForm(target) {
    $('.loader').show();
    let order_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/order.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            order_id,
        },
        cache: false,
        success: function (res) {
            console.log(res);
            if (res.error) {
                createAlertTable(res.error, "Данные продажи");
                return;
            }
            $('#edit-order-form #edit-order-title').text(`Редактировать продажу №${res['order_id']}`).attr('order-id', res['order_id']);
            $('#edit-order-form #editClientField').val(res['client_id']);
            $('#edit-order-form #editClientField option').each(function () {
                if (!res['clients'].find(t => t.client_id === this.value || this.value == -1)) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            $('#edit-order-form #editVgField').val(res['vg_data_id']);
            $('#edit-order-form #editSumVGField').val(res['sum_vg']);
            if (res['callmaster'])
                $('#edit-order-form #editCallmasterField').val(res['callmaster']);
            $('#edit-order-form #editDebtClField').val(res['debt'] || 0);
            $('#edit-order-form #editOutField').val(res['out']);
            $("#edit-order-form #editCommentField").val(res['comment']);
            $('#edit-order-form #editRollback1Field').val(res['rollback_1']);
            $('#edit-order-form #editObtainingField').val(res['method']);
            $('#edit-order-form #editFiatField').val(res['fiat']);
            if (res['shares'])
                res['shares'].forEach((el => {
                    $('#edit-owners-list-visible').append(
                        "<p class='edit-owner-percent-input-box'>" +
                        `${el['owner_name']}` +
                        "<input class='edit-owner-percent-input' type='number' " +
                        `owner-id=${el["owner_id"]} placeholder='Процент прибыли' ` +
                        `value=${el['percent']}> ` +
                        "</p>");
                }))
            if (res['other_owners']) {
                $('#open-invisible-owner-edit-list').removeClass('display-none');
                res['other_owners'].forEach((el => {
                    $('#edit-owners-list-invisible').append(
                        "<p class='edit-owner-percent-input-box'>" +
                        `${el['owner_name']}` +
                        "<input class='edit-owner-percent-input' type='number' " +
                        `owner-id=${el["owner_id"]} placeholder='Процент прибыли' ` +
                        " value='0'> " +
                        "</p>");
                }));
            } else $('#open-invisible-owner-edit-list').addClass('display-none');

            $('.loader').fadeOut('fast');
            $('#Order-edit-Modal').modal();

        },
        error: function (res) {
        },
    });
}

function fillOwnerEditForm(target) {
    $('.loader').show();
    let owner_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/user.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            owner_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные менеджера");
                return;
            }
            $('#edit-user-form #edit-user-title').text(`Изменить данные пользователя ${res['full_name']}`).attr('user-id', res['id']);
            $('#edit-user-form #editFirstNameField').val(res['first_name']);
            $('#edit-user-form #editLastNameField').val(res['last_name']);
            $('#edit-user-form #editLoginField').val(res['login']);
            $('#edit-user-form #editBranchField').val(res['branch_id']);
            $('#edit-user-form #editRoleField').val(res['role']);
            $('#edit-user-form #editMoneyField').val(res['money']);
            $('.loader').fadeOut('fast');
            $('#Owner-edit-Modal').modal();

        },
        error: function () {
        },
    });
}

function fillFiatEditForm(target) {
    $('.loader').show();
    let fiat = target.attr('itemid');
    $.ajax({
        url: "../api/select/fiat.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            fiat,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable('connectionError', 'Фиат');
                return;
            }
            $('#edit-fiat-form #edit-fiat-title').text(`Редактировать данные валюты ${res['full_name']}`).attr('fiat-id', res['fiat_id']);
            $('#edit-fiat-form #editFullNameFiatField').val(res['full_name']);
            $('#edit-fiat-form #editNameFiatField').val(res['name']);
            $('#edit-fiat-form #editCodeField').val(res['code']);
            $('.loader').fadeOut('fast');
            $('#Fiat-edit-Modal').modal();

        },
        error: function () {
        },
    });
}

function fillUserEditForm(target) {
    $('.loader').show();
    let user_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/user.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            user_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные менеджера");
                return;
            }
            $('#edit-user-form #edit-user-title').text(`Изменить данные пользователя ${res['full_name']}`).attr('user-id', res['id']);
            $('#edit-user-form #editFirstNameField').val(res['first_name']);
            $('#edit-user-form #editLastNameField').val(res['last_name']);
            $('#edit-user-form #editLoginField').val(res['login']);
            $('#edit-user-form #telegram').val(res['telegram']);
            $('#edit-user-form #editBranchField').val(res['branch_id']);
            $('#edit-user-form #editRoleField').val(res['role']);
            $('#edit-user-form #editMoneyField').val(res['money']);
            $('.loader').fadeOut('fast');
            $('#User-edit-Modal').modal();
        },
        error: function () {
        },
    });
}

function fillBranchEditForm(target) {
    $('.loader').show();
    let branch_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/branch.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            branch_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные предприятия");
                return;
            }
            $('#edit-branch-form #edit-branch-title').text(`Изменить данные предприятия ${res['name']}`).attr('branch-id', res['id']);
            $('#edit-branch-form #editNameField').val(res['name']);
            $('#edit-branch-form #editIkId').val(res['ik_id']);
            $('#edit-branch-form #editMoneyField').val(res['money']);
            $('.loader').fadeOut('fast');
            $('#Branch-edit-Modal').modal();
        },
        error: function () {
        },
    });
}
function fillProjectEditForm(target) {
    $('.loader').show();
    let project_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/project.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            project_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные проекта");
                return;
            }
            $('#edit-project-form #edit-project-title').text(`Изменить данные проекта ${res['project_name']}`).attr('project-id', res['project_id']);
            $('#edit-project-form #editNameProjectField').val(res['project_name']);
            $('.loader').fadeOut('fast');
            $('#Project-edit-Modal').modal();
        },
        error: function () {
        },
    });
}
function fillVGEditForm(target) {
    $('.loader').show();
    let vg_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/vg.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            vg_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные ВГ");
                return;
            }
            $('#edit-vg-form #edit-vg-title').text(`Изменить данные валюты ${res['name']}`).attr('vg-id', res['id']);
            $('#edit-vg-form #editNameField').val(res['name']);
            $('#edit-vg-form #editOutField').val(res['out']);
            $('#edit-vg-form #editInField').val(res['in']);
            $('#edit-vg-form #editUrlField').val(res['url']);
            $('#edit-vg-form #editKeyField').val(res['key']);
            $('.loader').fadeOut('fast');
            $('#VG-edit-Modal').modal();

        },
        error: function () {
        },
    });
}

function fillOwnerEditForm(target) {
    $('.loader').show();
    let owner_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/owner.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            owner_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные владельца");
                return;
            }
            $('#edit-vg-form #edit-vg-title').text(`Изменить валюту ${res['name']}`);
            $('#edit-vg-form #editNameField').attr('owner-id', res['id']);
            $('#edit-vg-form #editNameField').val(res['name']);
            $('#edit-vg-form #editOutField').val(res['out']);
            $('#edit-vg-form #editInField').val(res['in']);
            $('#edit-vg-form #editUrlField').val(res['url']);
            $('.loader').fadeOut('fast');
            $('#Owner-edit-Modal').modal();


        },
        error: function () {
        },
    });
}

function fillGlobalVGlInfo(target) {
    $('.loader').show();
    const vg_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/global.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            vg_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные глобала");
                return;
            }
            $('#edit-globalVG-title').attr('vg-id', vg_id)
            $('#edit-globalVGName').val(res['name']);
            $('#globalVG-edit-Modal').modal();
            $('.loader').fadeOut('fast');
        },
        error: function () {
            $('.loader').fadeOut('fast');
        },
        done: function () {

        }
    });
}

function fillClientEditForm(target) {
    $('.loader').show();
    let client_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/client.php",
        type: "POST",
        dataType: 'JSON',
        data: {
            client_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Данные клиента");
                return;
            }
            $('#edit-client-form #edit-client-title').text(`Изменить данные клиента ${res['full_name']}`).attr('client-id', res['id']);
            $('#edit-client-form #editFirstNameField').val(res['first_name']);
            $('#edit-client-form #editLastNameField').val(res['last_name']);
            $('#edit-client-form #editBynameField').val(res['login']);
            $('#edit-client-form #editPhoneField').val(res['phone']);
            $('#edit-client-form #editTgField').val(res['telegram']);
            $('#edit-client-form #editEmailField').val(res['email']);
            $('#edit-client-form #editDebtField').val(res['debt']);
            $('#edit-client-form #editRollbackField').val(res['rollback']);
            $('#edit-client-form #editDescriptionField').val(res['description']);
            $('#edit-client-form #editMaxDebtField').val(res['max_debt']);
            $('#edit-client-form #editPasswordField').val(res['password']);
            $('#edit-client-form #pay_page').prop("checked", !!+res['pay_page']);
            $('#edit-client-form #pay_in_debt').prop("checked", !!+res['pay_in_debt']);
            $('#edit-client-form #payment_system').prop("checked", !!+res['payment_system']);
            $('.loader').fadeOut('fast');
            $('#Client-edit-Modal').modal();
        },
        error: function () {
        },
    });
}

function fillMethodOfObtainingInfo(target) {
    $('.loader').show();
    let method_id = target.attr('itemid');
    $.ajax({
        url: "../api/select/methodOfObtaining.php",
        type: "POST",
        dataType: 'json',
        data: {
            method_id,
        },
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error, "Метод оплаты");
                return;
            }
            $('#method-of-obtaining-edit-form .modal-title').text(`Изменить данные метода оплаты ${res['method_name']}`).attr('method-id', res['method_id']);
            $('#method-of-obtaining-edit-form #method-edit-name').val(res['method_name']);
            $('#MethodsOfObtaining-edit-Modal').modal();
        },
        error: function () {
        },
        complete: function () {
            $('.loader').fadeOut('fast');
        }
    });
}

$('a[href="#Order-Modal"]').click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
        url: "../api/select/branch.php",
        type: "POST",
        dataType: 'JSON',
        data: "req=ok",
        success: function (res) {
            if (res)
                $('#Order-Modal').modal();
            else
                $('#noOwners-Modal').modal()
        },
        error: function () {
        },
    });

})

$('#Order-edit-Modal').on($.modal.CLOSE, function () {
    $('#Order-edit-Modal .edit-owner-percent-input-box ').remove();

})

$('#Order-Modal').on($.modal.CLOSE, function () {
    $('#Order-Modal .owner-percent-input-box ').remove();

})

function checkUserData() {
    $.ajax({
        url: "../api/auth/activityCheck.php",
        type: "POST",
        data: "req=ok",
        cache: false,
        dataType: 'JSON',
        success: function (res) {
            if (res['active'] === "inactive") {
                location.reload();
            } else {
                setTimeout(checkUserData, 3000);
            }
        },
        error: function (res) {
            setTimeout(checkUserData, 3000);
        }
    });

};

function deleteOwner(target) {
    $('.loader').show();
    const owner_id = target.attr('itemid');
    $.ajax({
        url: "../api/delete/owner.php",
        type: "POST",
        dataType: 'json',
        data: {
            owner_id,
        },
        cache: false,
        success: function (res) {
            $('.loader').fadeOut();
            if (res.error) {
                createAlertTable("failed", "");
                return;
            }
            target.remove();
        },
        error: function () {

            createAlertTable("failed", "");
            $('.loader').fadeOut();
        },
    });
}

