
        let myModal, categorySelectModal;
        let back_link, save_btn, leave_btn;
        let navbar_links;
        let inputs;
        let is_pristine = true;

        let errTitle, errDescription, errDuration, errStartingBid, errBuyNow, errDirectSale;

        let constants;
        let description_text;
        let categories;


        $(async function() {
            myModal = new bootstrap.Modal($("#myModal")); // il faut creer une instance de Modal
            categorySelectModal = new bootstrap.Modal($("#categoriesModal"));
            categories = $("input[name='chk_group[]"); // tous les inputs qui ont comme name='chk_group[]'
            back_link = $("#back_link");
            save_btn = $("#save_btn");
            leave_btn = $("#leave_button");
            navbar_links = $("#navbar_user a");
            inputs = $("input");
            errTitle = $("#errTitle");
            description_text = $("#description");
            errDescription = $("#errDescription");
            errDuration = $("#errDuration");
            errStartingBid = $("#errStartingBid");
            errBuyNow = $("#errBuyNow");
            errDirectSale = $("#errDirectSale");


            constants = await $.getJSON("item/get_item_rules_service");


            inputs.on('change', function() {
                // si le input change, on est plus pristine
                is_pristine = false;
            });


            $("textarea").on('change', function() {
                is_pristine = false;
            });

            /**
             *  a chaque input dans les fields inputs, on regarde quel input c'est puis on fait la validation
             */
            inputs.on('input blur', async function() {
                await validateFields($(this));
            })

            description_text.on('input blur', async function() {
                await validateDescription(description_text.val());
            });

            save_btn.click(async function(e) {

                let allOK = await validateAll();
                if (!allOK) {
                    e.preventDefault();
                }
            })



            async function validateAllFields() {
                let allOk = true;
                for (const input of inputs.toArray()) { // for respecte mieux les await
                    let res = await validateFields($(input));
                    if (!res) allOk = false; // des qu'on rencontre un faux, on le sauve comme ca on sait qu'au moins un field est non valide
                }
                save_btn.prop('disabled', !allOk);
                return allOk;
            }

            async function validateFields(field) {
                let ok = true;
                let field_id = field.attr("id");
                if (field_id === "itemtitle") { //  si l'element modifie est le field du titre
                    ok = await validateTitle(field.val());
                } else if (field_id === "duration_days") {
                    ok = validateDuration(field.val());
                } else if (field_id === "starting_bid") {
                    ok = validateStartingBid(field.val());
                } else if (field_id === "buy_now_price") {
                    ok = validateBuyNow(field.val());
                } else if (field_id === "saleprice") {
                    ok = validateDirectSale(field.val());
                }

                field.toggleClass("is-invalid", !ok);
                field.toggleClass("is-valid", ok);
                // activation du bouton depends du ok des inputs
                save_btn.prop('disabled', !ok) // desactiver le bouton save
                return ok;
            }


            async function validateAll() {
                let first = await validateAllFields();
                let second = await validateDescription(description_text.val());
                save_btn.prop('disabled', !(first && second));
                return (first && second);
            }


            /**
             * validation du titre
             */
            async function validateTitle(title) {
                errTitle.html(""); // nettoyage du div des erreurs
                let ok = true;
                if (title === "") {
                    errTitle.append(constants.title_required);
                    ok = false;
                } else if (title.length < constants.title_min_length || title.length > constants.title_max_length) {
                    errTitle.append(constants.length_title);
                    ok = false;
                } else {
                    // comme en get l'url ne supporte pas les accent etc, on va envoyer par post 
                    let is_unique = await $.post("item/check_title_unicity_service", {
                        title: title,
                        id: info.id
                    }, function(response) {
                        if (response === "false") {
                            errTitle.append(constants.unicity_title);
                            ok = false;
                        }
                    });
                }
                return ok;
            }

            function validateDuration(duration) {
                errDuration.html("");
                let ok = true;
                if (duration === "") {
                    ok = false;
                    errDuration.append(constants.duration_required);
                } else if (isNaN(duration) || Number(duration) < Number(constants.duration_min) || Number(duration) > Number(constants.duration_max)) {
                    ok = false;
                    errDuration.append(constants.duration_rules);
                }
                return ok;
            }

            function validateDescription(description) {
                errDescription.html("");
                let ok = true;
                if (description != "" && (description.length < constants.description_min_length)) {
                    ok = false;
                    errDescription.append(constants.description_length);
                }
                description_text.toggleClass("is-invalid", !ok);
                description_text.toggleClass("is-valid", ok);
                save_btn.prop('disabled', !ok);
                return ok;
            }

            function validateStartingBid(starting_bid) {
                errStartingBid.html("");
                let ok = true;
                let direct_sale = $("#saleprice").val();
                if (starting_bid != "") { // si starting_bid est rempli
                    if (isNaN(starting_bid) || Number(starting_bid) <= 0) { // s'il est pas numerique ou positif
                        errStartingBid.append("<span>" + constants.starting_bid + "</span><br>"); // message pour dire qu'il doit etre positif
                        ok = false;
                    }
                    let buy_now = $("#buy_now_price").val(); // on va comparer le starting bid avec buy_now
                    if (buy_now != "" && Number(starting_bid) > Number(buy_now)) { // si buy now est rempli et que le starting bid est superieur au buy now
                        ok = false;
                        errStartingBid.append("<span>Starting bid can't be greather than buy now price</span><br>");
                    }
                    // si direct sale est rempli et numerique ce n'est pas bon
                    if (direct_sale != "" && !isNaN(direct_sale)) { // si direct sale est rempli et numerique
                        errStartingBid.append("<span>" + constants.both + "</span>");
                        ok = false;
                    }
                } else if (direct_sale == "") { // si starting_bid est vide et que  direct_sale est vide
                    errStartingBid.append("<span>" + "Offer must be an auction or a direct sale, cannot be both" + "</span>");
                    ok = false;
                }
                return ok;
            }

            function validateBuyNow(buy_now) {
                errBuyNow.html("");
                let ok = true;
                if (buy_now != "") { // si buy now est rempli
                    if (isNaN(buy_now) || Number(buy_now) <= 0) { // s'il est pas numerique ou positif
                        errBuyNow.append("<span>" + constants.buy_now_positif + "</span><br>"); // message pour dire qu'il doit etre positif
                        ok = false;
                    }
                    let starting_bid = $("#starting_bid").val();
                    if (starting_bid != "" && Number(buy_now) < Number(starting_bid)) { // si starting_bid est rempli et que le buy_now est inferieur au starting_bid
                        ok = false;
                        errBuyNow.append(constants.buy_now_sup_starting_bid);
                    }
                    // si direct sale est rempli et numerique ce n'est pas bon
                    let direct_sale = $("#saleprice").val();
                    if (direct_sale != "" && !isNaN(direct_sale)) { // si direct sale est rempli et numerique
                        errBuyNow.append("<span>" + "Offer must be either a direct sale or an auction, cannot be both" + "</span>");
                        ok = false;
                    }
                }
                return ok;
            }

            function validateDirectSale(price) {
                errDirectSale.html("");
                let ok = true;
                let starting_bid = $("#starting_bid").val();
                if (price != "") { // si direct sale est rempli
                    if (isNaN(price) || Number(price) <= 0) { // s'il est pas numerique ou positif
                        errDirectSale.append("<span>" + "Direct sale price must be a positive number" + "</span><br>"); // message pour dire qu'il doit etre positif
                        ok = false;
                    }
                    // si starting bid ou buy_now sont rempli ce n'est pas ok
                    let buy_now = $("#buy_now_price").val();
                    if ((starting_bid != "" && !isNaN(starting_bid)) || (buy_now != "" && !isNaN(buy_now))) {
                        errDirectSale.append("<span>" + constants.both + "</span>");
                        ok = false;
                    }
                } else if (starting_bid == "") { // si directSale est vide et que starting bid est vide
                    errDirectSale.append("<span>" + "Offer must be a direct sale or an auction, cannot be both" + "</span>");
                    ok = false;
                }
                return ok;
            }


            back_link.click(function(e) {
                if (!is_pristine) {
                    e.preventDefault(); // empeche le submit
                    leave_btn.click(function() { // on dit qu'au click du bouton Leave du modal, on redirige vers le href du bouton
                        window.location.href = back_link.attr("href"); // https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
                    })
                    myModal.show();
                }
            });

            navbar_links.on('click', function(e) {
                if (!is_pristine) {
                    e.preventDefault(); // empeche le submit
                    let goTo = $(this).attr("href"); // $(this) ici et pas dans le click sinon this c'est leave_button donc pas de href dedans
                    leave_btn.click(function() { // on dit qu'au click du bouton Leave du modal, on redirige vers le href du bouton
                        window.location.href = goTo // https://www.w3schools.com/howto/howto_js_redirect_webpage.asp

                    })
                    myModal.show();
                }
            });

            function numberOfSelectedCategories() {
                let total = 0;
                categories.each(function() { // pour chaque <input>
                    if ($(this).is(':checked')) total++; // si a l'attribut checked
                })
                return total;
            }

            // lors de la selection d'une categorie, il faut verifier si le nombre de categories selectionne est  > 3, si c'est le cas, annuler la selection et afficher la modale
            categories.on('click', function(e) {
                if (numberOfSelectedCategories() > 3){
                    e.preventDefault();
                    categorySelectModal.show();
                }
            })
        });