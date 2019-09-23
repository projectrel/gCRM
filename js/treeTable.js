$(document).ready(() => {
    if (!window.location.pathname.includes('types')) {
        return;
    }
    $('#wrapper').css({overflowX: "visible"});
    initTreeTable().then(initTable);
});

function initTreeTable(){
    return new Promise((resolve)=>{
        fetchTypes()
            .then(() => {
                    initModalListeners();
                    $('.change_isactive').change(function (res) {
                        $(this).prop('checked', !+$(this).prop('checked'));
                        activate(this, res);
                    });
                    resolve();
                }
            );
    });
}

function initModalListeners() {
    [{icon: "plus", suff: "add"}, {icon: "edit", suff: "edit"}].forEach(({icon, suff}) => {
        $(`#types-list .fa-${icon}`).click(function () {
            const id = $(this).parent().parent().parent().attr('itemid');
            const name = $(this).parent().find('span').text();
            const nameInput = $(`#outgo-type-${suff}-Modal #name-${suff}`);

            $(`#outgo-type-${suff}-category`).text(name);
            if(suff === "edit"){
                nameInput.val(name);
            }
            nameInput.attr('itemid', id);
            $(`#outgo-type-${suff}-Modal`).modal();
        });
    });
    $('#global-add-type').click(function(){
        const id = 1;
        const name = "общие";
        $(`#outgo-type-add-category`).text(name);
        $(`#outgo-type-add-Modal #name-add`).attr('itemid', id);
        $(`#outgo-type-add-Modal`).modal();
    });
}


function levelToHtml(types, acc = 0) {
    let html = "";
    if (!types || !types.length) return html;
    acc && (html += `<ul class="submenu" id="${acc}">`);
    html += types.map(type => {
        if(!type.node) return;
        if (Object.keys(type.node).length < 2) return;
        let s = appendType(type.node, type.children && type.children.length);
        s += levelToHtml(type.children, acc + 1);
        s += '</li>\n';
        return s;
    }).join('');
    acc && (html += '</ul>\n');
    return html;
}

function appendType({outgo_name: name, outgo_type_id: id, active}, hasChildren) {
    let s = `<li itemid="${id}">
            <div class="row-wrapper">
            <div class="name-wrapper">
            <i class="fas fa-edit"></i>
            <i class="fas fa-plus"></i>
             <span>${name}</span>
              <span class="forsum"></span>
             </div>
             ${hasChildren ? `
<i class="fas fa-arrow-down"></i>` : ""}
             <span>
                <div class="button b2" id="button-10">
                <input type="checkbox" class="checkbox change_isactive" ${+active === 1 ? "" : "checked"}>
                <div class="knobs"></div>
                </div>
                </span>
              </div>
            `;
    return s;
}

const fetchTypes = () => new Promise((resolve, reject) => {
    $(".loader").show();
    $.ajax({
        url: "../api/select/outgoTypes.php",
        type: "POST",
        data: {},
        dataType: "JSON",
        cache: false,
        success: function (res) {
            if (res.error) {
                createAlertTable(res.error);
                reject();
                return;
            }
            if (res.status === "success") {
                $('#types-list').html(levelToHtml(res.types.children));
                resolve();
            }
            reject();
        },
        error: function () {
            createAlertTable("connectionError", "Типы расходов");
            reject();
        },
        complete: function (res) {
            $(".loader").fadeOut("slow");
        }
    })
});

function initTable() {
    (function () {
        let pluginName = "jqueryAccordionMenu";
        let defaults = {
            speed: 300,
            showDelay: 0,
            hideDelay: 0,
            singleOpen: true,
            clickEffect: true
        };

        function Plugin(element, options) {
            this.element = element;
            this.settings = $.extend({},
                defaults, options);
            this._defaults = defaults;
            this._name = pluginName;
            this.init()
        }

        $.extend(Plugin.prototype, {
            init: function () {
                this.openSubmenu();
                this.submenuIndicators();
                if (defaults.clickEffect) {
                    this.addClickEffect()
                }
            },
            openSubmenu: function () {
                $(this.element).find("div").children("ul").find("li").bind("click touchstart",
                    function (e) {
                        if (['I', 'SPAN', 'INPUT'].includes(e.target.tagName)
                            && !e.target.classList.contains('fa-arrow-down')
                            && !e.target.classList.contains('fa-arrow-up')) return;
                        e.stopPropagation();
                        e.preventDefault();
                        if ($(this).children(".submenu").length > 0) {
                            if ($(this).children(".submenu").css("display") === "none") {
                                $(this).children(".submenu").delay(defaults.showDelay).slideDown(defaults.speed);
                                $(this).children('.row-wrapper').find('i').addClass('fa-arrow-up').removeClass('fa-arrow-down');
                                return false
                            } else {
                                $(this).children(".submenu").delay(defaults.hideDelay).slideUp(defaults.speed);
                                $(this).children('.row-wrapper').find('i').addClass('fa-arrow-down').removeClass('fa-arrow-up');
                            }
                            if ($(this).children(".submenu").siblings("a").hasClass("submenu-indicator-minus")) {
                                $(this).children(".submenu").siblings("a").removeClass("submenu-indicator-minus")
                            }
                        }
                    })
            },
            submenuIndicators: function () {
                if ($(this.element).find(".submenu").length > 0) {
                    $(this.element).find(".submenu").siblings("a").append("<span class='submenu-indicator'>+</span>")
                }
            },
            addClickEffect: function () {
                let ink, d, x, y;
                $(this.element).find("a").bind("click touchstart",
                    function (e) {
                        $(".ink").remove();
                        if ($(this).children(".ink").length === 0) {
                            $(this).prepend("<span class='ink'></span>")
                        }
                        ink = $(this).find(".ink");
                        ink.removeClass("animate-ink");
                        if (!ink.height() && !ink.width()) {
                            d = Math.max($(this).outerWidth(), $(this).outerHeight());
                            ink.css({
                                height: d,
                                width: d
                            })
                        }
                        x = e.pageX - $(this).offset().left - ink.width() / 2;
                        y = e.pageY - $(this).offset().top - ink.height() / 2;
                        ink.css({
                            top: y + 'px',
                            left: x + 'px'
                        }).addClass("animate-ink")
                    })
            }
        });
        $.fn[pluginName] = function (options) {
            this.each(function () {
                if (!$.data(this, "plugin_" + pluginName)) {
                    $.data(this, "plugin_" + pluginName, new Plugin(this, options))
                }
            });
            return this
        }
    })();

    $(document).ready(function () {
        jQuery("#jquery-accordion-menu").jqueryAccordionMenu();
    });
    $(function () {
        $("#hall-list li").click(function () {
            $("#hall-list li.active").removeClass("active");
            $(this).addClass("active");
        })
    })
}

function activate(_this) {
    const row = $(_this).parent().parent().parent().parent();
    const id = row.attr('itemid');
    $.ajax
    ({
        type: "POST",
        url: "../api/edit/outgoTypeActive.php",
        dataType: 'JSON',
        data: {id},
        success: function (res) {
            if (res.status === 'success') {
                res.nodes.replace(/'/g, '').split(',').forEach(r => {
                    const li = $('[itemid="' + r + '"]');
                    li.find('.change_isactive').prop('checked', !+res.status_to_do)
                });
            } else{
                if(res.error === "denied"){
                    $(_this).parent().effect('shake');
                }else{
                    alert(res.error);
                }
            }
        },
        error: function (e) {
            alert(e.responseText);
        }
    });
}