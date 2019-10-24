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
            url: "../api/select/vg/getLoginByVg.php",
            type: "GET",
            dataType: 'json',
            data: {
                vg_id, client_id,
            },
            cache: false,
            success: function (res) {
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
                if (res.callmaster > 0) {
                    $('#Order-Modal #rollback1Field').val(res.rollback);
                    $('#Order-Modal #callmasterField').val(res.callmaster);
                    $('#rollbacks-lists-container').css({display: 'grid'});
                } else {
                    $('#Order-Modal #rollback1Field').val();
                    $('#Order-Modal #callmasterField').val(-1);
                    $('#rollbacks-lists-container').css({display: 'none'});
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
            url: "../api/select/vg/getVGOwners.php",
            type: "POST",
            data: {
                vg_id, client_id
            },
            dataType: 'JSON',
            cache: false,
            success: function (res) {
                console.log(res);
                if (res.error) {
                    createAlertTable(res.error, 'Владелец');
                    return;
                }
                const container = $('#owners-lists-container');
                container.empty();
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
        const sum_manually = $("#add-order-form #sumField").val();
        const enter_manually = $("#add-order-form .btn_sum-manually").hasClass('btn_sum-manually__opened');
        const allShares = [];
        sharesEls.each(function () {
            allShares.push({value: $(this).val(), owner_id: $(this).attr('owner-id')});
        });
        const shares = allShares.filter((el) => el.value > 0);
        $.ajax({
            url: "../api/add/order.php",
            type: "POST",
            timeout: 3000,
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
                enter_manually,
                sum_manually
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                // try {
                //     if (res.error) {
                //         createAlertTable(res.error, "Заказ и транзакция")
                //         return;
                //     }
                //     if (res['success'] == false) {
                //         createAlertTable('success', "Заказ");
                //         $('#Order-transaction-info-modal #error-url').text(res['url']).attr('href', res['url']);
                //         $('#Order-transaction-info-modal').append(`<div>Код ошибки: ${res['code'] || "неизвестен"}</div>`)
                //         $('#Order-transaction-info-modal').append(`<div>Ошибка: ${res['message'] || "неизвестна"}</div>`)
                //         $('#Order-transaction-info-modal').modal({
                //             fadeDuration: 500,
                //             fadeDelay: 0
                //         });
                //     } else {
                //         createAlertTable("success", "Заказ и транзакция");
                //         setTimeout(() => location.reload(), 600)
                //     }
                // } catch {
                //     createAlertTable("success", "Заказ и транзакция");
                //     setTimeout(() => location.reload(), 600);
                // }

            },
            error: function () {
              //  createAlertTable("connectionError", "Заказ");
            },
            complete: function () {
                // setTimeout(function () {
                //     $(".modal-submit").prop("disabled", false);
                //     $(".loader").fadeOut("slow");
                // }, 100);
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
        const password = $("#add-user-form #passField").val();
        const login = $("#add-user-form #loginField").val();
        const first_name = $("#add-user-form #firstNameField").val();
        const last_name = $("#add-user-form #lastNameField").val();
        const branch = $("#add-user-form #branchField").val();
        const role = $("#add-user-form #roleField").val();
        const telegram = $("#add-user-form #telegram").val();
        const email = $("#add-user-form #userEmail").val();
        $.ajax({
            url: "../api/add/user.php",
            type: "POST",
            data: {
                password,
                login,
                first_name,
                last_name,
                branch,
                role,
                telegram,
                email,
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
                if (res.error) {
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
                if (res.id) {
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
        const sum = $("#add-outgo-form #sumField").val();
        const owner_id = $("#add-outgo-form #ownerField").val();
        const project_id = $("#add-outgo-form #projectField").val();
        const method_id = $("#add-outgo-form #methodField").val();
        const type = $("#add-outgo-form #typeField").val();
        const description = $("#add-outgo-form #commentField").val();
        $.ajax({
            url: "../api/add/outgo.php",
            type: "POST",
            data: {owner_id, sum, description, method_id, type, project_id},
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

    $.validate({
        form: '#add-vg-purchase-form',
        modules: 'security',
        lang: 'ru',
        onSuccess: function () {
            addVgPurchase();
            return false;
        }
    });

    function addVgPurchase() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const vg_id = $("#add-vg-purchase-form #vgField").val();
        const fiat_id = $("#add-vg-purchase-form #fiatField").val();
        const vg_sum = $("#add-vg-purchase-form #vgSumField").val();
        $.ajax({
            url: "../api/add/vgPurchase.php",
            type: "POST",
            data: {
                vg_id,
                fiat_id,
                vg_sum,
            },
            dataType: "JSON",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, "Закупка VG");
                    return;
                }
                createAlertTable(res.status, "Закупка VG");
            },
            error: function () {
                createAlertTable("connectionError", "Закупка VG");
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

    //Payback VG debt
    $.validate({
        form: '#payback-vg-debt-form',
        modules: '',
        lang: 'ru',
        onSuccess: function () {
            paybackVGDebt();
            return false;
        }
    });

    function paybackVGDebt() {
        $(".loader").show();
        $(".modal-submit").prop("disabled", true);
        const vg_id = $("#payback-vg-debt-form #vgDebtField").val();
        const method_id = $("#payback-vg-debt-form #methodDebtField").val();
        const currency_sum = $("#payback-vg-debt-form #vgSumDebtField").val();
        $.ajax({
            url: "../api/operate/vgDebt.php",
            type: "POST",
            data: {
                vg_id, method_id, currency_sum
            },
            dataType: "json",
            cache: false,
            success: function (res) {
                if (res.error) {
                    createAlertTable(res.error, "Выплата задолженности по VG");
                    return;
                }
                createAlertTable(res.status, "Выплата задолженности по VG");
                console.log(res);
            },
            error: function () {
                createAlertTable("connectionError", "Выплата задолженности по VG");
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
        const method_name = $("#add-method-of-obtaining-form #method-name").val();
        const fiat_id = $("#add-method-of-obtaining-form #methodFiatField").val();
        $.ajax({
            url: "../api/add/methodOfObtaining.php",
            type: "POST",
            data: {
                method_name,
                fiat_id
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

    //PRollback
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
        const client_id = $("#pay-rollback-form #clientField").val();
        const sum = $("#pay-rollback-form #payField").val();
        const method_id = $("#pay-rollback-form #methodField").val();
        $.ajax({
            url: "../api/operate/rollback.php",
            type: "POST",
            data: {
                client_id, sum, method_id,
            },
            cache: false,
            dataType: "JSON",
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
        const client_id = $("#payback-debt-form #debtorField").val();
        const sum = $("#payback-debt-form #paybackField").val();
        const method_id = $("#payback-debt-form #methodField").val();
        $.ajax({
            url: "../api/operate/debt.php",
            type: "POST",
            dataType: "json",
            data: {
                client_id,
                sum,
                method_id,
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
