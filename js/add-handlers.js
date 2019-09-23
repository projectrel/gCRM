$(document).ready(function () {
//Branch
    $.validate({
        form: '#add-branch-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            addBranch();
            return false;
        }
    });

    function addBranch() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const name = $("#add-branch-form #nameField").val();
        const ik_id = $("#add-branch-form #ikId").val();
        $.ajax({
            url: "../api/add/branch.php",
            type: "POST",
            data: {
                    name, ik_id
                },
                dataType: "JSON",
                cache: false,
                success: function (res) {
                    if (res.error) {
                        createAlertTable(res.error, 'Предприятие');
                        return;
                    }
                    createAlertTable(res.status, "Предприятие");
                },
                error: function () {
                    createAlertTable("connectionError", "Предприятие");
                },
                complete: function () {
                    setTimeout(function () {
                        $(".modal-submit").prop("disabled", false);
                        $(".loader").fadeOut("slow");
                    }, 100);
                }
            });

    }


    //Owner

    $.validate({
        form: '#add-owner-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            addOwner();
            return false;
        }
    });

    function addOwner() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        let id = $("#add-owner-form #nameField").val();
        $.ajax({
            url: "../api/add/owner.php",
            type: "POST",
            data: {
                user_id: id,
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Владелец');
                    return;
                }
                createAlertTable(res.status, "Владелец");
            },
            error: function () {
                createAlertTable("connectionError", "Владелец");
            },
            complete: function () {
                setTimeout(function () {
                    $(".modal-submit").prop("disabled", false);
                    $(".loader").fadeOut("slow");
                }, 100);
            }
        });

    }

    //Order
    $.validate({
        form: '#add-order-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            addOrder();
            return false;
        }
    });
    $('#Order-Modal #sumVGField, #Order-Modal #outField').on('change paste keyup', function (e) {
        $('#Order-Modal #debtClField').val($('#Order-Modal #sumVGField').val() * $('#Order-Modal #outField').val() / 100);
    });
    $('#Order-edit-Modal #editSumVGField, #Order-edit-Modal #outField').on('change paste keyup', function (e) {
        $('#Order-edit-Modal #editDebtClField').val($('#Order-edit-Modal #editSumVGField').val() * $('#Order-edit-Modal #editOutField').val() / 100);
    });
    const vgcl = $('#Order-Modal #vgField, #Order-Modal #clientField');
    vgcl.change(function (e) {
        $('.loader').show();
        $('#Order-Modal #sumVGField').trigger('change');
        let vg_id = $('#Order-Modal #vgField').val();
        let client_id = $('#Order-Modal #clientField').val();
        if (!vg_id && !client_id) return;
        $.ajax({
            url: "../api/select/getLoginByVg.php",
            type: "GET",
            dataType: 'json',
            data: {
                vg_id, client_id,
            },
            cache: false,
            success: function (res) {;
                if (res.error) {
                    createAlertTable(res.error, "Данные ВГ");
                    return;
                }
                if (res.loginByVg)
                    $('#Order-Modal #loginByVgField').val(res.loginByVg);
                else
                    $('#Order-Modal #loginByVgField').val("");
                if (res.fiat_id && parseInt(res.fiat_id) > 0) {
                    $('#Order-Modal #fiatField').val(res.fiat_id);
                } else {
                    $('#Order-Modal #fiatField').val($("#fiatField option:first").val());
                }

                if (res.description || parseInt(res.description) != -1)
                    $('#Order-Modal #commentField').val(res.description);
                else
                    $('#Order-Modal #commentField').val("");
                if (res.method_id) {
                    $('#Order-Modal #obtainingField').val(res.method_id);
                } else {
                    $('#Order-Modal #obtainingField').val($("#obtainingField option:first").val());
                }
            },
            error: function () {
            },
            complete: function () {
                $(".loader").fadeOut("fast");
            }
        });

        if (vg_id) {
            const optionSelected = $("option:selected", '#Order-Modal #vgField');
            const perc = optionSelected.attr('percent');
            $('#outField').val(perc);
        }
        if (!client_id || !vg_id) return;
        vgcl.prop('disabled', true);
        vgcl.addClass('no-drop');
        $('.loader').show();

        $.ajax({
            url: "../api/select/getVGOwners.php",
            type: "POST",
            data: {
                vg_id, client_id
            },
			dataType: 'JSON',
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Владелец');
                    return;
                }
                const container = $('#owners-lists-container');
                container.empty();
                console.log(res);
                container.append(res.data || "");
            },
            error: function () {
                console.log(res);

            },
            complete: function () {
                $('.loader').fadeOut('slow');
                vgcl.prop('disabled', false);
                vgcl.removeClass('no-drop');
            }
        });
    });

    let currentOrderForm;
    const clientInput = $('#Order-Modal #clientField');
    clientInput.change(function (e) {
        let client_id = $('#Order-Modal #clientField').val();
        if (client_id == -1) {
            $('#Client-Modal').modal();
        }
        currentOrderForm = $('#Order-Modal');
    });


    const clientEditInput = $('#Order-edit-Modal #editClientField');
    clientEditInput.change(function (e) {
        let client_id = $('#Order-edit-Modal #editClientField').val();
        if (client_id == -1) {
            $('#Client-Modal').modal();
        }
        currentOrderForm = $('#Order-edit-Modal');
    });


    const callmasterInpt = $('#Order-Modal #callmasterField');
    callmasterInpt.change(function (e) {
        let callmaster_id = callmasterInpt.val();
        if (callmaster_id) {
            $('#rollbacks-lists-container').css({display: 'grid'});
        } else {
            $('#rollbacks-lists-container').css({display: 'none'});
        }
    });

    function addOrder() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const client = $("#add-order-form #clientField").val();
        const rollback_1 = $("#add-order-form #rollback1Field").val();
        const callmaster = $("#add-order-form #callmasterField").val();
        const vg = $("#add-order-form #vgField").val();
        const sum_vg = $("#add-order-form #sumVGField").val();
        const out = $("#add-order-form #outField").val();
        const loginByVg = $("#add-order-form #loginByVgField").val();
        const descr = $("#add-order-form #commentField").val();
        const sharesEls = $("#add-order-form .owner-percent-input");
        const debtCl = $("#add-order-form #debtCLField").val() || 0;
        const fiat = $("#add-order-form #fiatField").val();
        const method_id = $("#add-order-form #obtainingField").val();
        const allShares = [];
        sharesEls.each(function () {
            allShares.push({value: $(this).val(), owner_id: $(this).attr('owner-id')});
        });
        const shares = allShares.filter((el) => el.value > 0);
        $.ajax({
            url: "../api/add/order.php",
            type: "POST",
            data: {
                client,
                rollback_1,
                sum_vg,
                out,
                method_id,
                vg,
                shares,
                debtCl,
                callmaster,
                descr,
                fiat,
                loginByVg,
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                try {
                    if (res.error) {
                        createAlertTable(res.error, "Заказ и транзакция")
                        return;
                    }
                    if (res['success'] == false) {
                        createAlertTable('success', "Заказ");
                        $('#Order-transaction-info-modal #error-url').text(res['url']).attr('href', res['url']);
                        $('#Order-transaction-info-modal').append(`<div>Код ошибки: ${res['code'] || "неизвестен"}</div>`)
                        $('#Order-transaction-info-modal').append(`<div>Ошибка: ${res['message'] || "неизвестна"}</div>`)
                        $('#Order-transaction-info-modal').modal({
                            fadeDuration: 500,
                            fadeDelay: 0
                        });
                    }else{
                        createAlertTable("success", "Заказ и транзакция");
                        setTimeout(() => location.reload(), 300)
                    }
                } catch {
                    createAlertTable("success", "Заказ и транзакция");
                    setTimeout(() => location.reload(), 300);
                }

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

    $('#Order-transaction-info-modal').on('click', '.close-modal', function () {
        location.reload();
    })

//User

    $.validate({
        form: '#add-user-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addUser();
            return false;
        }
    });

    function addUser() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        let password = $("#add-user-form #passField").val();
        let login = $("#add-user-form #loginField").val();
        let first_name = $("#add-user-form #firstNameField").val();
        let last_name = $("#add-user-form #lastNameField").val();
        let branch = $("#add-user-form #branchField").val();
        let role = $("#add-user-form #roleField").val();
        let telegram = $("#add-user-form #telegram").val();
        $.ajax({
            url: "../api/add/user.php",
            type: "POST",
            data: {
                password: password,
                login: login,
                first_name: first_name,
                last_name: last_name,
                branch: branch,
                role: role,
                telegram,
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, "Пользователь");
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
        form: '#add-outgo-type-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addOutgoType();
            return false;
        }
    });

    function addOutgoType() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const nameInput = $("#add-outgo-type-form #name-add");
        const name = nameInput.val();
        const id = nameInput.attr('itemid');

        $.ajax({
            url: "../api/add/outgoType.php",
            type: "POST",
            data: {
                name,
                parentId: id
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if(res.error){
                    createAlertTable(res.error, "Тип расходов");
                    return;
                }
                createAlertTable(res.status, "Тип расходов");
            },
            error: function () {
                createAlertTable("connectionError", "Тип расходов");
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
        form: '#add-client-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addClient();
            return false;
        },
        onError: function () {
        }
    });

    function addClient() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const first_name = $("#add-client-form #firstNameField").val();
        const last_name = $("#add-client-form #lastNameField").val();
        const telegram = $("#add-client-form #tgField").val();
        const description = $("#add-client-form #descriptionField").val();
        const callmaster = $("#add-client-form #callmasterField").val();
        const byname = $("#add-client-form #bynameField").val();
        const phone = $("#add-client-form #phoneField").val();
        const email = $("#add-client-form #emailField").val();
        const password = $("#add-client-form #passwordField").val();
        const payment_system = $("#add-client-form #payment_system").is(':checked');
        const pay_in_debt = $("#add-client-form #pay_in_debt").is(':checked');
        const pay_page = $("#add-client-form #pay_page").is(':checked');
        const max_debt = $("#add-client-form #maxDebtField").val();
        $.ajax({
            url: "../api/add/client.php",
            type: "POST",
            data: {
                byname: byname,
                callmaster: callmaster,
                first_name: first_name,
                last_name: last_name,
                description: description,
                phone: phone,
                email: email,
                telegram: telegram, password, pay_in_debt, pay_page, payment_system, max_debt
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Клиент');
                    return;
                }
                if (res.id){
                    const opt = document.createElement('option');
                    opt.value = res.id;
                    opt.innerText = first_name + ' ' + last_name;
                    opt.selected = true;
                    $('#clientField').append(opt);
                }

                $('#Order-Modal').modal();
                createAlertTable(res.status, "Клиент");
            },
            error: function () {
                createAlertTable("connectionError", "Клиент");
            },
            complete: function () {
                setTimeout(function () {
                    $(".modal-submit").prop("disabled", false);
                    $(".loader").fadeOut("slow");
                }, 100);
            }
        });

    }


    //Outgo
    $.validate({
        form: '#add-outgo-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addOutgo();
            return false;
        },
        onError: function () {
        }
    });

    function addOutgo() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        let sum = $("#add-outgo-form #sumField").val();
        let owner = $("#add-outgo-form #ownerField").val();
        let project = $("#add-outgo-form #projectField").val();
        let fiat = $("#add-outgo-form #fiatField").val();
        let type = $("#add-outgo-form #typeField").val();
        let descr = $("#add-outgo-form #commentField").val();
        $.ajax({
            url: "../api/add/outgo.php",
            type: "POST",
            data: {owner, sum, description: descr, fiat, type, project},
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Расход');
                    return;
                }
                createAlertTable(res.status, "Расход");
            },
            error: function () {
                createAlertTable("connectionError", "Расход");
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
        form: '#add-vg-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addVG();
            return false;
        },
        onError: function () {
        }
    });

    function addVG() {
        $('.loader').show();
        $(".modal-submit").prop("disabled", true);
        let prevId = $("#add-vg-form #nameVgnField").val();
        let name = $("#add-vg-form #nameField").val();
        let in_percent = $("#add-vg-form #inField").val();
        let out_percent = $("#add-vg-form #outField").val();
        let url = $("#add-vg-form #urlField").val();
        let key = $("#add-vg-form #keyField").val();

        $.ajax({
            url: "../api/add/vg.php",
            type: "POST",
            data: {
                name,
                prevId,
                in: in_percent,
                out: out_percent,
                url,
                key,
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
        form: '#add-fiat-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addFiat();
            return false;
        },
        onError: function () {
        }
    });

    function addFiat() {
        $('.loader').show();
        $(".modal-submit").prop("disabled", true);
        let full_name = $("#add-fiat-form #fullNameFiatField").val();
        let name = $("#add-fiat-form #nameFiatField").val();
        let code = $("#add-fiat-form #codeField").val();
        $.ajax({
            url: "../api/add/fiat.php",
            type: "POST",
            data: {
                name,
                full_name,
                code,
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Fiat');
                    return;
                }
                createAlertTable(res.status, "Fiat");
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

    //Project
    $.validate({
        form: '#add-project-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addProject();
            return false;
        },
        onError: function () {
        }
    });

    function addProject() {
        $('.loader').show();
        $(".modal-submit").prop("disabled", true);
        let name = $("#add-project-form #addNameProjectField").val();
        $.ajax({
            url: "../api/add/project.php",
            type: "POST",
            data: {
                name
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Проект');
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
        form: '#add-globalVg-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            addGlobalVG();
            return false;
        },
        onError: function () {
        }
    });

    function addGlobalVG() {
        const name = $("#globalVGName").val();
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        $.ajax({
            url: "../api/add/global.php",
            type: "POST",
            data: {name},
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
        form: '#add-method-of-obtaining-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addMethodOfObtaining();
            return false;
        },
        onError: function () {
        }
    });

    function addMethodOfObtaining() {
        $('.loader').show();
        $(".modal-submit").prop("disabled", true);
        let method_name = $("#add-method-of-obtaining-form #method-name").val();
        $.ajax({
            url: "../api/add/methodOfObtaining.php",
            type: "POST",
            data: {
                method_name
            },
            dataType: "json",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, 'Методы оплаты');
                    return;
                }
                createAlertTable(res.status, "Метод оплаты");
            },
            error: function () {
                createAlertTable("connectionError", "MethodOfObtaining");
            },
            complete: function () {
                setTimeout(function () {
                    $(".modal-submit").prop("disabled", false);
                    $(".loader").fadeOut("slow");
                }, 100);
            }
        });

    }

    //Payback
    $.validate({
        form: '#pay-rollback-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            payRollback();
            return false;
        },
        onError: function () {
        }
    });
    $('#Rollback-Modal #clientField').change(function (e) {
        const optionSelected = $("option:selected", this);
        const sum = optionSelected.attr('sum');
        const input = $('#payField');
        $('#Rollback-Modal #fiatField').val(optionSelected.attr('fiat'));
        input.val(sum);
        input.attr('max', sum);
        input.attr('min', 0);
    });


    function payRollback() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        let id = $("#pay-rollback-form #clientField").val();
        let number = $("#pay-rollback-form #payField").val();
        let fiat = $("#pay-rollback-form #fiatField").val();
        $.ajax({
            url: "../api/operate/rollback.php",
            type: "POST",
            data: {
                id, fiat, number,
            },
            cache: false,
            dataType:"json",
            success: function (res) {
                createAlertTable(res.status || res.error, "Откат");
            },
            error: function () {
                createAlertTable("connectionError", "Откат");
            },
            complete: function () {
                setTimeout(function () {
                    $(".modal-submit").prop("disabled", false);
                    $(".loader").fadeOut("slow");
                }, 100);
            }
        });

    }


//Debt
    $.validate({
        form: '#payback-debt-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            paybackDebt();
            return false;
        },
        onError: function () {
        }
    });
    $('#Debt-Modal #debtorField').change(function (e) {
        const optionSelected = $("option:selected", this);
        const sum = optionSelected.attr('sum');
        const fiat = optionSelected.attr('fiat');
        const input = $('#paybackField');
        input.val(sum);
        $('#Debt-Modal #fiatField').val(fiat);
        input.attr('max', sum);
        input.attr('min', 0);
    });

    function paybackDebt() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        let id = $("#payback-debt-form #debtorField").val();
        let number = $("#payback-debt-form #paybackField").val();
        let fiat = $("#payback-debt-form #fiatField").val();
        $.ajax({
            url: "../api/operate/debt.php",
            type: "POST",
            dataType: "json",
            data: {
                id,
                number,
                fiat,
            },
            cache: false,
            success: function (res) {
                createAlertTable(res.status || res.error, "Погашение");
            },
            error: function () {
                createAlertTable("connectionError", "Погашение");
            },
            complete: function () {
                setTimeout(function () {
                    $(".modal-submit").prop("disabled", false);
                    $(".loader").fadeOut("slow");
                }, 100);
            }
        });

    }


    function createAlertTable(alertType, requestType, reload = true) {
        if ($('.custom-alert').hasClass('custom-alert--active'))
            $('.custom-alert').removeClass('custom-alert--active');
        if ($('.custom-alert').hasClass('bg-green')) $('.custom-alert').removeClass('bg-green');
        switch (alertType) {
            case "exists":
                if (requestType == "VG")
                    $('.custom-alert .alert-text-box').text(`${requestType} с таким именем уже существует`);
                else
                    $('.custom-alert .alert-text-box').text(`${requestType} с таким логином уже существует`);
                break;
            case "success":
                $('.custom-alert .alert-text-box').text(`${requestType} успешно добавлен(о)`);
                $('.custom-alert').addClass('bg-green');
                $.modal.close();
                if (currentOrderForm && requestType == "Клиент")
                    currentOrderForm.modal();
                else if (requestType != 'Заказ')
                    setTimeout(function () {
                        reload && location.reload();
                    }, 1500);
                break;
            case "edit-success":
                $('.custom-alert .alert-text-box').text(`Изменения сохранены`);
                $('.custom-alert').addClass('bg-green');
                $.modal.close();
                setTimeout(function () {
                    reload && location.reload();
                }, 2500);
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

});
