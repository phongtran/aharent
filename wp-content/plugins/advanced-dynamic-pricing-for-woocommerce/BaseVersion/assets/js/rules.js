/* global jQuery, wpc_postboxes, ajaxurl, wdp_data */
jQuery(document).ready(function ($) {

    var bulk_adjustment = (function () {
        var $available_types = wdp_data.bulk_rule;
        var $rule = null;

        var init_events = function ($container, $rule) {
            $rule.find('.wdp_bulk_adjustment_remove').click(function () {
                destroy($container, $rule);
            });
            $container.find('.bulk-adjustment-type').on('change', function () {
                update_selectors($container, $rule);
            });
            $container.find('.bulk-qty_based-type').on('change', function () {
                update_selectors($container, $rule);
            });
            make_select2_products($container.find('[data-field="autocomplete"]'));
        };

        var destroy = function ($container, $rule) {
            $rule.find('.wdp-btn-add-bulk').show();
            $container.hide();
            flushInputs($container);
            $container.find('.wdp-range').remove();
            $container.find('.wdp-ranges-empty').show();

            // Unconditionally hide all sortable handlers
            $rule.find(".wdp-drag-handle").hide();
            $rule.find(".sortable-apply-mode-block").hide();
            // Hide label with checkbox in role discount
            $rule.find('.dont-apply-bulk-if-roles-matched-check').hide();
        };

        var update_selectors = function ($container, $rule) {
            var $adj_type = $container.find('.bulk-adjustment-type').val();
            var $qty_based = $container.find('.bulk-qty_based-type').val();
            var $discount_type = $container.find('.bulk-discount-type').val();

            var $available_qty_based = get_available_qty_based_types($adj_type);
            if (!check_qty_based_availability($adj_type, $qty_based)) {
                $qty_based = Object.keys(get_available_qty_based_types($adj_type))[0];
            }
            $container.find('.bulk-qty_based-type').html("");
            $.each($available_qty_based, function ($key, $item) {
                $container.find('.bulk-qty_based-type').append(make_option($key, $item.label))
            });

            var $available_discount_types = get_available_discount_types($adj_type, $qty_based);
            if (!check_discount_type_availability($adj_type, $qty_based, $discount_type)) {
                $discount_type = Object.keys(get_available_discount_types($adj_type, $qty_based))[0];
            }
            $container.find('.bulk-discount-type').html("");
            $.each($available_discount_types, function ($key, $label) {
                $container.find('.bulk-discount-type').append(make_option($key, $label))
            });

            $container.find('.bulk-qty_based-type').val($qty_based);
            $container.find('.bulk-discount-type').val($discount_type);

            if ($qty_based === 'product_selected_categories') {
                $container.find('.bulk-selected_categories-type').show();
            } else {
                $container.find('.bulk-selected_categories-type').hide();
            }

            if ($qty_based === 'selected_products') {
                $container.find('.bulk-selected_products-type').show();
            } else {
                $container.find('.bulk-selected_products-type').hide();
            }
        };

        var make_option = function ($value, $label, $classes) {
            if (typeof $classes === 'undefined') {
                $classes = [];
            }

            var option = $("<option></option>");

            option.val($value).text($label);

            $classes.forEach(function ($class) {
                option.addClass($class);
            });

            return option;
        };

        var get_available_qty_based_types = function ($adj_type) {
            return $available_types[$adj_type]
        };

        var check_qty_based_availability = function ($adj_type, $qty_based) {
            return typeof $available_types[$adj_type][$qty_based] !== 'undefined';
        };

        var get_available_discount_types = function ($adj_type, $qty_based) {
            return $available_types[$adj_type][$qty_based].items;
        };

        var check_discount_type_availability = function ($adj_type, $qty_based, $discount_type) {
            return typeof $available_types[$adj_type][$qty_based].items[$discount_type] !== 'undefined';
        };

        return {
            // init: function ($available_types) {
            //     this.$available_types = $available_types;
            // },

            add: function ($container, $data) {
                $rule = $container.closest('.postbox');

                $container.show();
                $rule.find('.wdp-btn-add-bulk').hide();

                // selector categories
                $container.find('.bulk-selected_categories-type').hide();
                $container.find('.bulk-selected_products-type').hide();

                $rule.find('.bulk-adjustment-type').find('option:first-child').prop("selected", "selected");
                $rule.find('.bulk-qty_based-type').find('option:first-child').prop("selected", "selected");
                $rule.find('.bulk-discount-type').find('option:first-child').prop("selected", "selected");

                init_events($container, $rule);

                update_selectors($container, $rule);

                if ($data) {
                    $container.find('.bulk-adjustment-type').val($data.type);

                    if ($data.discount_type) {
                        $container.find('.bulk-discount-type').val($data.discount_type);
                    }

                    if ($data.qty_based) {
                        $container.find('.bulk-qty_based-type').val($data.qty_based);
                    }

                    if ($data.selected_categories) {
                        var html = '';
                        $.each($data.selected_categories, function (i, id) {
                            var title = wdp_data.titles['product_categories'] && wdp_data.titles['product_categories'][id] ? wdp_data.titles['product_categories'][id] : id;
                            html += '<option selected value="' + id + '">' + title + '</option>';
                        });
                        $container.find('.bulk-selected_categories-type select').html(html);
                    }

                    if ($data.selected_products) {
                        var html = '';
                        $.each($data.selected_products, function (i, id) {
                            var title = wdp_data.titles['products'] && wdp_data.titles['products'][id] ? wdp_data.titles['products'][id] : id;
                            html += '<option selected value="' + id + '">' + title + '</option>';
                        });
                        $container.find('.bulk-selected_products-type select').html(html);
                    }

                    if ($data.ranges) {
                        var $range_button = $container.find('.add-range');
                        $.each($data.ranges, function (index, item) {
                            add_range($range_button, item);
                        });
                    }

                    if ($data.table_message) {
                        $container.find('.bulk-table-message').val($data.table_message);
                    }
                }

                update_selectors($container, $rule);
            }

        }

    });

    // make rule blocks collapsable and sortable
    wpc_postboxes.add_postbox_toggles( $('#rules-container') );

    // update priority field on reorder
    wpc_postboxes._on_reorder = function() {
        // make aray of objects [ { id, priority }, ... ]
        var data = $('#rules-container .postbox').map(function(i, el) {
            $('.rule-priority', el).val( i );
            return {
                id: $('.rule-id', el).val(),
                priority: wdp_data.paged && wdp_data.options.rules_per_page ? (wdp_data.paged - 1) * wdp_data.options.rules_per_page + i : i,
            }
        }).toArray();

        let new_params = {
            action: 'wdp_ajax',
            method: 'reorder_rules',
            items: data
        };
        new_params[wdp_data.security_query_arg] = wdp_data.security;

        $.post(
            ajaxurl,
            new_params,
            $.noop,
            'json'
        );
    };

    function finishLoadRule(new_rule, data) {
        if ( ! new_rule.hasClass('not-initialized') ) {
            return false;
        }

        if ( ! data ) {
            wdp_data.rules.forEach(function (rule_data) {
                if (rule_data.id === new_rule.find('.rule-id').val()) {
                    data = rule_data;
                    return false;
                }

                return true;
            });
        }

        addEventHandlersToRule(new_rule);

        if (data) {
            setRuleData(new_rule, data);
        }

        new_rule.removeClass('not-initialized')
    }

	wpc_postboxes._on_expand = function (new_rule, data) {
		new_rule.find('.rule-trigger-coupon-code input').prop('readonly', false);
		finishLoadRule(new_rule, data);
	};

	wpc_postboxes._on_close = function (new_rule, data) {
		new_rule.find('.rule-trigger-coupon-code input').prop('readonly', true);
	};

    // load saved rules
    if (wdp_data.rules) {
	    var promises = [];
	    wdp_data.rules.forEach( function ( data ) {
		    promises.push( add_rule( data ) );
	    } );
	    Promise.all( promises ).then( function ( responses ) {
		    $( "#rules-container" ).removeClass( "loading" );
		    $( '#no-rules' ).removeClass( "loading" );
		    $( '.add-rule' ).removeClass( "loading" );
		    $( "#progress_div" ).hide();
	    } ).catch( function ( reason ) {
		    console.log( reason );
	    } );

        if ( wdp_data.selected_rule && wdp_data.selected_rule > 0 ) {
            var $rule = $('#rules-container .rule-id[value="' + wdp_data.selected_rule + '"]').closest('.postbox');
            $rule.removeClass('closed');
            $('html, body').animate({ scrollTop: $rule.position().top }, 500);
        }

        if ( wdp_data.action_rules && wdp_data.action_rules == 'add' ) {
            var new_rule;
            if(wdp_data.product_title && wdp_data.product_title != -1) {
                var rule_data = {title: wdp_data.product_title, exclusive: 0, enabled: "on", additional: {disabled_by_plugin: 0}};
                new_rule = add_rule(rule_data);
                new_rule.removeClass('closed');
                new_rule.addClass('dirty');
            }
            else {
                new_rule = add_rule();
            }
            finishLoadRule(new_rule);
            if(wdp_data.product && wdp_data.product > 0) {

                var filter_data = {qty: "1", type: "products", method: "in_list", value: {0: wdp_data.product}};
                add_product_filter(new_rule.find('.wdp-filter-block'), filter_data);
            }

        }
    }

    // create new rule when click 'Add rule' button
    $('.add-rule').click(function (e) {
        e.preventDefault();
        var new_rule = add_rule();
        finishLoadRule(new_rule);
    });

    /* Template functions */

    function add_rule(data) {
        window.wdpPreloadRule = true;
        var template_options = {
            c: 0,
            p: (data && data.priority) ? data.priority : get_last_priority(),
            type: (data && data.type) ? data.type : 'package',
        };

        // prepare template
        var rule_template = get_template('rule', template_options);
        var new_rule = $(rule_template);

        preAddEventHandlersToRule(new_rule, data);

        // add new rule to rules list
        $('#rules-container').append(new_rule);

        preSetRuleData(new_rule, data);
        $('#no-rules').hide();
        window.wdpPreloadRule = false;

        return new_rule;
    }

    function preAddEventHandlersToRule(new_rule) {
        // on change rule title

        new_rule.find('.wdp-title').on('change input', function () {
            var $postbox = $(this).closest('.postbox');
            var value = $(this).val();
            if ( value.length ) {
                $postbox.find('.wdp-no-name').addClass('cross-not-visible');
            } else {
                $postbox.find('.wdp-no-name').removeClass('cross-not-visible');
            }
            $postbox.find('[data-wdp-title]').text(value);
        });

        // listeners for buttons
        new_rule.find('.wdp_remove_rule').on('click', function () {
            if (!confirm(wdp_data.labels.confirm_remove_rule)) return;

            var $rule = $(this).closest('.postbox');
            $rule.addClass('removing');

            var rule_id = $rule.find('.rule-id').val();
            if (!rule_id) {
                $rule.remove();
                return;
            }

            let new_params = {
                action: 'wdp_ajax',
                method: 'remove_rule',
                rule_id: rule_id
            };
            new_params[wdp_data.security_query_arg] = wdp_data.security;

            $.post(
                ajaxurl,
                new_params,
                function () {
                    $rule.remove();
                },
                'json'
            );
        });

        new_rule.find('.wdp_copy_rule').on('click', function () {
            Promise.all( [finishLoadRule(new_rule)] ).then( function ( responses ) {
                var temp = new_rule.serialize();
                temp = deparam(temp);

                var cloned_data = temp.rule;
                cloned_data.id = '';
                cloned_data.priority = get_last_priority();

                var cloned_new_rule = add_rule(cloned_data);
                finishLoadRule(cloned_new_rule, cloned_data);

                // open rule and focus on title
                cloned_new_rule.find('.hndle').trigger('click');
                cloned_new_rule.find('.wdp-title').focus();
            } ).catch( function ( reason ) {
                console.log( reason );
            } );
        });

        // on save rule
        new_rule.find('.save-rule').click(function (e) {
            e.preventDefault();

            var $form = $(this).closest('form');

            $form.attr('disabled', true);
            var beforeSendValidation = true;
            var $filtersByProduct = $form.find('.wdp-product-filter-container .wdp-filter-item');
            if ( $filtersByProduct.length ) {
                $filtersByProduct.each( function( index ) {
                    //Check if values in filters are present
                    let $condition_block = $( this ).find('.wdp-column.wdp-condition-field-value');
                    if ( $condition_block.length ) {
                        let $selectedOptions = $condition_block.find( '.select2-selection__choice' );
                        if ( ! $selectedOptions.length ) {
                            beforeSendValidation = false;
                            let $attachErrorTo = $( this ).find( '.select2.select2-container' ).first();
                            if ( ! $attachErrorTo.next('.products-filter__error-wrapper').length ) {
                                let $elEmptyValue  = $( "<div class=\"products-filter__error-wrapper\"><span class=\"products-filter__onempty-error\"></span></div>");
                                $attachErrorTo.after( $elEmptyValue );
                                $('.products-filter__onempty-error').text('You must select at least one value');
                            }
                        }
                    }
                    //Check if values in range are correct
                    let $rangeStart              = $( this ).find( '.wdp-condition-field-qty');
                    let $rangeEnd                = $( this ).find( '.wdp-condition-field-qty-end' );
                    let rangeStartValue          = $.trim( $rangeStart.children().val() );
                    let rangeEndValue            = "";
                    let rangeEndEmptiness        = "";
                    let currentRangeError        = "";
                    let innerErrorContainerClass = "";
                    if ( $rangeEnd.length ) {
                        rangeEndValue     = $.trim( $rangeEnd.children().val() )
                        rangeEndEmptiness = rangeEndValue === "";
                    } else {
                        rangeEndEmptiness = false;
                    }

                    if ( !( rangeStartValue === "" ) && ! rangeEndEmptiness ) {
                        let expr         = /^\d+(\.\d{1,2})?$/;
                        let startRegResult = expr.exec( rangeStartValue );
                        let endRegResult   = true;

                        if ( $rangeEnd.length ) {
                            endRegResult = expr.exec( rangeEndValue );
                        }

                        if ( startRegResult != null && endRegResult != null ) {
                            if (  ( endRegResult !== true ) && ( parseInt( rangeStartValue ) > parseInt( rangeEndValue ) ) ) {
                                currentRangeError   = "Second value in the range must be larger than the first one";
                                innerErrorContainerClass = "products-filter__range-error";
                            }
                        } else {
                            currentRangeError    = "Wrong number format.";
                            innerErrorContainerClass  = "products-filter__input-error"
                        }

                    } else {
                        currentRangeError    = "Inputs cannot be empty";
                        innerErrorContainerClass  = "products-filter__emptiness-error"
                    }

                    if ( currentRangeError !== "" ) {
                        beforeSendValidation = false;
                        if ( ! $( this ).find( '.two-on-two-column.left-column' ).children('.products-filter__error-wrapper').length ) {
                            let $wrongRange = $( "<div class=\"products-filter__error-wrapper\"><span class=\"" + innerErrorContainerClass + "\"></span></div>");
                            $( this ).find( '.two-on-two-column.left-column' ).append( $wrongRange );
                            $( "." + innerErrorContainerClass ).text( currentRangeError );
                        }
                    }

                } );
            }
			let filtersByFreeProduct = $form.find('.wdp-get-products-block .wdp-filter-item');
			if ( filtersByFreeProduct.length ) {
				filtersByFreeProduct.each(function (index) {
					//Check if values in filters are present
					let $condition_block = $(this).find('.wdp-column.wdp-condition-field-value');
					if ($condition_block.length) {
						let $selectedOptions = $condition_block.find('.select2-selection__choice');
						if (!$selectedOptions.length && !$condition_block.find(".select2-hidden-accessible").prop('disabled')) {
							beforeSendValidation = false;
							let $attachErrorTo = $(this).find('.select2.select2-container').first();
							if (!$attachErrorTo.next('.products-filter__error-wrapper').length) {
								let $elEmptyValue = $("<div class=\"products-filter__error-wrapper\"><span class=\"products-filter__onempty-error\"></span></div>");
								$attachErrorTo.after($elEmptyValue);
								$('.products-filter__onempty-error').text('You must select at least one value');
							}
						}
					}
				});
			}

      var rangesValuesFrom = jQuery.map($form.find('.adjustment-from'), function(e, i) {
        return parseInt(jQuery(e).val());
      });
      var rangesValuesTo = jQuery.map($form.find('.adjustment-to'), function(e, i) {
        return parseInt(jQuery(e).val());
      });
			if (jQuery.inArray(0, rangesValuesFrom) !== -1 || jQuery.inArray(0, rangesValuesTo) !== -1) {
        beforeSendValidation = false;
        if (!$form.find('.bulk-ranges__error-wrapper').length) {
          let $zeroRangeValue = $("<div class=\"bulk-ranges__error-wrapper\"><span class=\"bulk-ranges__onzero-error\"></span></div>");
          $form.find('.wdp-ranges').after($zeroRangeValue);
          $('.bulk-ranges__onzero-error').text(wdp_data.labels.bulk_zero_value_err);
        }
        setTimeout(function() {
          $form.find('.bulk-ranges__error-wrapper').remove();
        }, 5000);
			}
			setTimeout( function() {
				//remove errors
				var $all_filters = $('.wdp-product-filter-container .wdp-row.wdp-filter-item');
				$.each( $all_filters, function( key, value ) {
					let $selectValuesField = $( value ).find( '.wdp-column.wdp-condition-field-value' );
					remove_user_input_errors( $selectValuesField, '.products-filter__error-wrapper' );
					let searchIn = $( value ).find( '.two-on-two-column.left-column' );
					remove_user_input_errors( searchIn, '.products-filter__error-wrapper' );
				} );

				$.each( filtersByFreeProduct, function( key, value ) {
					let $selectValuesField = $( value ).find( '.wdp-column.wdp-condition-field-value' );
					remove_user_input_errors( $selectValuesField, '.products-filter__error-wrapper' );
					let searchIn = $( value ).find( '.two-on-two-column.left-column' );
					remove_user_input_errors( searchIn, '.products-filter__error-wrapper' );
				} );
			}, 5000 );
            if ( beforeSendValidation ) {
                let new_params = $form.serialize();
                new_params += "&" + wdp_data.security_query_arg + "=" + wdp_data.security;

                $.post(
                    ajaxurl,
                    new_params,
                    function (response) {
                        $form.attr('disabled', false);

                        var id = response.data;
                        $form.find('.rule-id').val(id);

                        $form.removeClass('dirty');
                        $form.removeClass('disabled-by-plugin');
                        $form.find('.wdp-disabled-automatically-prefix').hide();
                    },
                    'json'
                );
            }
            return false;
        });

        // init flipswitch
        new_rule.find('[data-role="flipswitch"]').flipswitch();

        var save_rule_callback = function () {
            update_rule_title(new_rule);

            if (!window.wdpPreloadRule) {
                Promise.all([finishLoadRule(new_rule)]).then(function (responses) {
                    new_rule.find('.save-rule').click();
                }).catch(function (reason) {
                    console.log(reason);
                });
            }
        };

        new_rule.find('.wdp-field-enabled select').change(save_rule_callback);
        new_rule.find('.rule-is-exclusive select').change(function(){
            if ($(this).val() === "1") {
                new_rule.addClass('exclusive');
            } else {
                new_rule.removeClass('exclusive');
            }

            save_rule_callback();
        });

		new_rule.find('.rule-trigger-coupon-code input').change(function () {
			new_rule.addClass('dirty');
		});

        new_rule.find('.wdp-field-enabled').click(function (event) {
            event.preventDefault();
            return false;
        });

        // update title on some changes
        new_rule.find('.wdp-adjustments-repeat, .cart-adjustment-type').change(function () {
            update_rule_title(new_rule);
        });
        update_rule_title(new_rule);
    }

    function addEventHandlersToRule(new_rule) {
        // ** Buttons in rule

        // Add condition
        new_rule.find('.add-condition, .wdp-btn-add-condition').click(function () {
            add_condition($(this));
        });

        // Add limit
        new_rule.find('.add-limit, .wdp-btn-add-limit').click(function () {
            add_limit($(this));
        });

        // Add range (bulk)
        new_rule.find('.add-range').click(function () {
            add_range($(this));
        });

        // Add product filter
        new_rule.find('.add-product-filter, .wdp-btn-add-product-filter').click(function () {
            add_product_filter(new_rule.find('.wdp-filter-block'));
        });

        // Add product filter for 'Add product adjustment' block
        new_rule.find('.wdp-btn-add-product-adjustment').click(function () {
            add_product_adjustment(new_rule.find('.wdp-product-adjustments'));
        });

        // Add bulk adjustment
        new_rule.find('.wdp-btn-add-bulk').click(function () {
            add_bulk_adjustment(new_rule.find('.wdp-bulk-adjustments'));

	        // Hide or show all sortable handlers depends on role discounts visibility
	        if ( new_rule.find( ".wdp-role-discounts" ).is( ":hidden" ) ) {
	            new_rule.find(".sortable-apply-mode-block").hide();
		        new_rule.find( ".wdp-drag-handle" ).hide();
	        } else {
                new_rule.find(".sortable-apply-mode-block").show();
		        new_rule.find( ".wdp-drag-handle" ).show();
	        }

	        // Show that label with checkbox when role discount on first position
	        if (new_rule.find( ".wdp-sortable-blocks > div:nth-child(2) .dont-apply-bulk-if-roles-matched-check"  ).length) {
		        new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).show();
	        } else {
		        new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();
	        }

			add_range($(this));
        });

        // Add cart adjustment
        new_rule.find('.add-cart-adjustment, .wdp-btn-add-cart-adjustment').click(function () {
            add_cart_adjustment($(this));
        });

	    // Add cart role discount
	    new_rule.find('.add-role-discount, .wdp-btn-add-role-discount').click(function () {
		    add_role_discount($(this));


		    if ( new_rule.find( ".wdp-bulk-adjustments" ).is( ":hidden" ) ) {
			    // Hide or show all sortable handlers depends on bulk adjustments visibility
			    new_rule.find( ".wdp-drag-handle" ).hide();

			    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();
                new_rule.find(".sortable-apply-mode-block").hide();
		    } else {
			    new_rule.find( ".wdp-drag-handle" ).show();

			    // Show that label with checkbox only when bulk adjustments is not empty and role discounts on first position
			    if (new_rule.find( ".wdp-sortable-blocks > div:first-child .dont-apply-bulk-if-roles-matched-check"  ).length) {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).show();
			    } else {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();
			    }
                new_rule.find(".sortable-apply-mode-block").show();
		    }
	    });

        // Add product filter for 'Get products' block
        new_rule.find('.add-filter-get-product, .wdp-btn-add-getproduct').click(function () {
            add_get_products(new_rule.find('.wdp-get-products'));
        });

        // make lists inside rule sortable
        make_sortable(new_rule.find('.wdp-sortable'));

	    new_rule.find( '.wdp-sortable-blocks' ).sortable( {
		    containment: 'parent',
		    items: '.wdp-sortable-block',
		    cursor: 'move',
		    axis: 'y',
		    opacity: 0.65,
		    handle: '.wdp-drag-handle',
		    update: function( event, ui ) {
			    new_rule.trigger( 'change' );

			    // Show that label with checkbox when role discount on first position
			    if (new_rule.find( ".wdp-sortable-blocks > div:nth-child(2) .dont-apply-bulk-if-roles-matched-check"  ).length) {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).show();
			    } else {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();
			    }
            }
	    } );

        new_rule.on('change', function() {
            new_rule.addClass('dirty');
        });

        new_rule.find('.wdp-get-products-repeat select').change(function () {
            update_get_products_options_visibility(new_rule);
        });
    }

    function preSetRuleData(new_rule, data) {
        // apply data
        if (data) {
            if (data.additional.disabled_by_plugin) {
                new_rule.addClass('disabled-by-plugin');
                new_rule.find('.wdp-disabled-automatically-prefix').show();
            } else {
                new_rule.find('.wdp-disabled-automatically-prefix').hide();
            }

            new_rule.find('.rule-is-exclusive select').val(data.exclusive);
			if (data.additional.trigger_coupon_code) {
				new_rule.find('.rule-trigger-coupon-code input').val(data.additional.trigger_coupon_code);
			}
            if(data.id) {
                new_rule.find('.rule-id').val(data.id);
            }
            new_rule.find('label.rule-id').text(data.id);

            if ( data.title ) {
                new_rule.find('.wdp-no-name').addClass('cross-not-visible');
            }

            new_rule.find('[data-wdp-title]').text(data.title);
            new_rule.find('.wdp-title').val(data.title);
            new_rule.find('[data-role="flipswitch"]').val(data.enabled);
            new_rule.find('[data-role="flipswitch"]').flipswitch('refresh');
            new_rule.removeClass('dirty');

            if (data.exclusive === "1") {
                new_rule.addClass('exclusive');
            }
        } else {
            new_rule.find('.wdp-disabled-automatically-prefix').hide();
            new_rule.removeClass('closed');
            new_rule.addClass('dirty');
        }
    }

    function setRuleData(new_rule, data) {
	    if (data.sortable_blocks_priority && data.sortable_blocks_priority.length) {
		    var $sorted_blocks = new_rule.find('.wdp-sortable-blocks');
		    data.sortable_blocks_priority.forEach( function ( data_item ) {
			    $.each(new_rule.find( 'input.priority_block_name' ), function( el_index, el_item ) {
				    if ( data_item === $(el_item).val() ) {
					    $( el_item ).parent().appendTo( $sorted_blocks );
				    }
			    } );

		    } );

	    }

        if ( data.additional.is_replace ) {
            var $replace_checkbox = new_rule.find( '.replace-adjustments input:checkbox' );
            $replace_checkbox.prop( 'checked', true );
        }

        if ( data.additional.replace_name ) {
            var $replace_name = new_rule.find( '.replace-adjustments input:text' );
            $replace_name.val(data.additional.replace_name);
        }

        if (data.additional.is_replace_free_products_with_discount) {
            new_rule.find('.replace-free-products input:checkbox').prop('checked', true);
        }

        if (data.additional.free_products_replace_name) {
            new_rule.find('.replace-free-products input:text').val(data.additional.free_products_replace_name);
        }

        if (data.options) {
            fill_options(new_rule.find('.wdp-options'), data.options);
        }

        if (data.filters) {
            var $wdp_product_filter = new_rule.find('.wdp-filter-block');
            $.each(data.filters, function (i, filter) {
                add_product_filter($wdp_product_filter, filter);
            });
        }

	    if ( data.additional.conditions_relationship ) {
		    var $radios = new_rule.find( '.wdp-conditions-relationship input:radio' );
		    $radios.filter( '[value=' + data.additional.conditions_relationship + ']' ).prop( 'checked', true );
	    }

        var $btn;
        if (data.conditions) {
            $btn = new_rule.find('.wdp-btn-add-condition');
            $.each(data.conditions, function (i, condition) {
                add_condition($btn, condition);
            });
        }

        if (data.limits) {
            $btn = new_rule.find('.wdp-btn-add-limit');
            $.each(data.limits, function (i, limit) {
                add_limit($btn, limit);
            });
        }

        if (data.product_adjustments && data.product_adjustments.type) {
            if ('total' === data.product_adjustments.type && data.product_adjustments['total']['type']) {
                add_product_adjustment(new_rule.find('.wdp-product-adjustments'), data.product_adjustments);
            } else if ('split' === data.product_adjustments.type && typeof data.product_adjustments['split'][0] !== 'undefined' && data.product_adjustments['split'][0]['type']) {
                add_product_adjustment(new_rule.find('.wdp-product-adjustments'), data.product_adjustments);
            }
        }

        if (data.cart_adjustments) {
            $btn = new_rule.find('.wdp-btn-add-cart-adjustment');
            $.each(data.cart_adjustments, function (i, cart_adjustment) {
                add_cart_adjustment($btn, cart_adjustment);
            });
        }

        if ( data.additional.sortable_apply_mode ) {
            new_rule.find( '.sortable-apply-mode' ).val( data.additional.sortable_apply_mode );
        }

	    if ( data.role_discounts && data.role_discounts.rows ) {
		    $btn = new_rule.find( '.wdp-btn-add-role-discount' );
		    $.each( data.role_discounts.rows, function ( i, role_discount ) {
			    add_role_discount( $btn, role_discount );
		    } );

		    new_rule.find( '[name="rule[role_discounts][dont_apply_bulk_if_roles_matched]"]' ).attr( 'checked', false );
		    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();

		    // Hide role discount sortable handler if bulk adjustments is empty
		    if ( !( data.bulk_adjustments && data.bulk_adjustments.ranges ) ) {
		        new_rule.find(".wdp-role-discounts .wdp-drag-handle").hide();
                new_rule.find(".sortable-apply-mode-block").hide();
            } else {
			    // Show that label with checkbox when role discount on first position
			    if (new_rule.find( ".wdp-sortable-blocks > div:nth-child(2) .dont-apply-bulk-if-roles-matched-check"  ).length) {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).show();
			    } else {
				    new_rule.find( '.dont-apply-bulk-if-roles-matched-check' ).hide();
			    }

                if ( typeof data.role_discounts.dont_apply_bulk_if_roles_matched !== "undefined" && data.role_discounts.dont_apply_bulk_if_roles_matched === '1' ) {
                    new_rule.find( '[name="rule[role_discounts][dont_apply_bulk_if_roles_matched]"]' ).attr( 'checked', true );
                }
                new_rule.find(".sortable-apply-mode-block").show();
            }
	    }

        if (data.bulk_adjustments && data.bulk_adjustments.ranges) {
            add_bulk_adjustment(new_rule.find('.wdp-bulk-adjustments'), data.bulk_adjustments);

	        // Hide bulk adjustments sortable handler if role discounts is empty
	        if ( !( data.role_discounts && data.role_discounts.rows ) ) {
		        new_rule.find(".wdp-bulk-adjustments .wdp-drag-handle").hide();
                new_rule.find(".sortable-apply-mode-block").hide();
	        }
        }

        if (data.get_products && data.get_products.value) {
            fill_get_products_options(new_rule.find('.wdp-get-products-block'), data.get_products);
            var $wdp_product_adjustments = new_rule.find('.wdp-get-products');
            $.each(data.get_products.value, function (i, filter) {
                add_get_products($wdp_product_adjustments, filter);
            });
        }
    }

    function fill_options($container, data) {
        if (data) {
            if (data.repeat) {
                $container.find('.wdp-repeat select').val(data.repeat);
            }
            if (data.apply_to) {
                $container.find('.wdp-apply-to select').val(data.apply_to);
            }
        }
    }

    function add_product_filter($container, data) {
        // hide message 'no rules' when add new filter
        $container.show();

        $container.closest('.postbox').find('.wdp-btn-add-product-filter').hide();

        var product_filter_index = get_new_product_filter_index($container);
        var template = get_template('filter_item_qty', {
            r: get_current_rule_index($container),
            f: product_filter_index,
            t: 'filters'
        });

        var $product_filter = $(template);
        var $product_filter_selector = $product_filter.find('.wdp-filter-type');

        // add filter into rule containter
        $container.find('.wdp-product-filter-container').append($product_filter);

		$product_filter.find('.wdp-condition-field-qty input').change(function() {
			var $product_filter_container = $(this).closest('.wdp-filter-block');
			var filters_count = $product_filter_container.find('.wdp-filter-item').length;
			if( $(this).val() == 1 && filters_count === 1 ) {
                $product_filter_container.find('.wdp-limitation').hide();
                $product_filter_container.find('.wdp-select-filter-priority').hide();
			}
			else {
                $product_filter_container.find('.wdp-limitation').show();
                $product_filter_container.find('.wdp-select-filter-priority').show();
			}
		});

		var filters_count = $container.find('.wdp-filter-item').length;
		if(filters_count > 1) {
            $container.find('.wdp-limitation').show();
        }
        if(filters_count > 1) {
            $('.wdp-select-filter-priority').show();
		} else {
            $('.wdp-select-filter-priority').hide();
        }

        // load data for existing filter
        if (data) {
            if (data.type) $product_filter_selector.val(data.type);
            var qty = data.qty ? data.qty : false;
            var qty_end = data.qty_end ? data.qty_end : data.qty;

			if (qty) {
				$product_filter.find('.wdp-condition-field-qty input').val(qty);
				if(qty == 1 && filters_count === 1) {
					$container.find('.wdp-limitation').hide();
                }
                if(qty > 1 && filters_count === 1) {
                    $('.wdp-select-filter-priority').show()
                }
			}
	        if (qty_end) $product_filter.find('.wdp-condition-field-qty-end input').val(qty_end);
		}

        // hook for remove filter
        $product_filter.find('.wdp_filter_remove').click(function () {
            var $product_filter_container = $(this).closest('.wdp-filter-block');
            $product_filter.closest('.postbox').find('.adjustment-split[data-index=' + $product_filter.attr('data-index') + ']').remove();
            $product_filter.remove();

            var filters_count = $product_filter_container.find('.wdp-filter-item').length;
            if (filters_count === 0) {
                $container.hide();
                $container.closest('.postbox').find('.wdp-btn-add-product-filter').show();
                $container.trigger('change');
                $container.closest('.postbox').find('.adjustment-mode-split').attr('disabled', 'disabled');
			}
			if (filters_count === 1) {
                $product_filter_container.find('.wdp-condition-field-qty input').trigger('change');
            }

            if (filters_count === 1 && $product_filter.find('.wdp-condition-field-qty input').val() === 1) {
                $product_filter_container.find('.wdp-select-filter-priority').hide();
            }
        });

        if (!wdp_data.options.enable_product_exclude) {
            $container.find(".wdp-product-exclude").hide();
            $container.find(".wdp-exclude-on-wc-sale-container").hide();
            $container.find(".wdp-exclude-already-affected-container").hide();
        }

        // render controls for selected filter type
        $product_filter_selector.change(function () {
            update_product_filter_fields($(this));
        });
        update_product_filter_fields($product_filter_selector, data);

        $container.closest('.postbox').find('.adjustment-mode-split').attr('disabled', false);
        add_product_adjustment_split($container.closest('.wdp-filter-block'), product_filter_index);
        updateElementsVisibilityDiscountSplit($container.closest('.postbox').find('.wdp-product-adjustments'), $container.closest('.postbox'));
    }

    function remove_user_input_errors( searchIn, errorEl ) {
        let errorFound = $( searchIn ).find( errorEl );
        if ( errorFound.length ) {
            errorFound.remove();
        }
    }

    function update_fields_qty_type($product_filter) {
        var type = $product_filter.find('.wdp-condition-field-qty-type select').val();
        if (type == 'any') {
            $product_filter.find('.wdp-condition-field-qty').hide();
            $product_filter.find('.wdp-condition-field-range').hide();
        } else if (type == 'qty') {
            $product_filter.find('.wdp-condition-field-qty').show();
            $product_filter.find('.wdp-condition-field-range').hide();
        } else if (type == 'range') {
            $product_filter.find('.wdp-condition-field-qty').hide();
            $product_filter.find('.wdp-condition-field-range').show();
        }
    }

	function updateGiftableProductFilterFields(type, ruleIndex, filterIndex, container, data, option) {
		container = container.closest('.wdp-filter-item');

		// prepare template for filter type
		let template = get_template('filter_' + type, {
			r: ruleIndex,
			f: filterIndex,
			t: option || 'filters'
		});

		container.find('.wdp-condition-field-sub').html(template);

		// load data for existing filter
		if (data) {
			if (data.method) {
				container.find('.wdp-filter-field-method select').val(data.method);
			}

			if (data.value) {
				var html = '';
				$.each(data.value, function (i, id) {
					var title = wdp_data.titles[type] && wdp_data.titles[type][id] ? wdp_data.titles[type][id] : id;
					html += '<option selected value="' + id + '">' + title + '</option>';
				});
				container.find('.wdp-condition-field-value select').append(html);
			}
		}

		make_select2_products(container.find('[data-field="autocomplete"]'));
		make_select2_product_taxonomies(container.find('[data-field="autocomplete"][data-list="product_taxonomies"]'));
	}

    function update_product_filter_fields($el, data, option) {
        var $container = $el.closest('.wdp-filter-item');
        var type = $el.val();

        // prepare template for filter type
        var template = get_template('filter_' + type, {
            r: get_current_rule_index($el),
            f: get_current_product_filter_index($el),
            t: option || 'filters'
        });

        $container.find('.wdp-condition-field-sub').html(template);

        // load data for existing filter
        if (data) {
            if (data.method) {
                $container.find('.wdp-filter-field-method select').val(data.method);
            }

            if (data.value) {
                var html = '';
                $.each(data.value, function (i, id) {
                    var title = wdp_data.titles[data.type] && wdp_data.titles[data.type][id] ? wdp_data.titles[data.type][id] : id;
                    html += '<option selected value="' + id + '">' + title + '</option>';
                });
                $container.find('.wdp-condition-field-value select').append(html);
            }

            if ( data.product_exclude ) {
                if ( data.product_exclude.values ) {
                    var product_exclude_html = '';
                    var pr_excl_type = 'products';
                    $.each(data.product_exclude.values, function (i, id) {
                        var title = wdp_data.titles[pr_excl_type] && wdp_data.titles[pr_excl_type][id] ? wdp_data.titles[pr_excl_type][id] : id;
                        product_exclude_html += '<option selected value="' + id + '">' + title + '</option>';
                    });
                    $container.find('.wdp-product-exclude select').append(product_exclude_html);
                }

                if (data.product_exclude.on_wc_sale) {
                    $container.find('.wdp-exclude-on-wc-sale-container input').prop('checked', true);
                }

                if (data.product_exclude.already_affected) {
                    $container.find('.wdp-exclude-already-affected-container input').prop('checked', true);
                }

				if (data.product_exclude.backorder) {
					$container.find('.wdp-exclude-backorder-container input').prop('checked', true);
				}
            }

            if (data.limitation) {
                $container.find('.wdp-limitation select').val(data.limitation);
            }

            /** pro version functionality */
            if ( data.select_priority ) {
                $container.find('.wdp-select-filter-priority select').val(data.select_priority);
            }
        }

        make_select2_products($container.find('[data-field="autocomplete"]'));
	    make_select2_product_taxonomies($container.find('[data-field="autocomplete"][data-list="product_taxonomies"]'));
    }

    function add_condition($el, data) {
        $el.closest('.postbox').find('.wdp-btn-add-condition').hide();

        var condition_template = get_template('condition_row', {
            r: get_current_rule_index($el),
            c: get_new_condition_index($el)
        });

        var $condition = $(condition_template);

        $condition.find('.wdp-condition-remove').click(function () {
            var $rule = $(this).closest('.postbox');
            $(this).closest('.wdp-condition').remove();

            var conditions_count = $rule.find('.wdp-conditions .wdp-condition').length;
            if (conditions_count === 0) {
                $rule.find('.wdp-conditions').hide();
                $el.closest('.postbox').find('.wdp-btn-add-condition').show();
            }
        });

        $el.closest('.postbox')
            .find('.wdp-conditions').show()
            .find('.wdp-conditions-container').append($condition);

        var $condition_type_selector = $condition.find('.wdp-condition-field-type select');
        if (data && data.type) {
            $condition_type_selector.val(data.type);
        }

        if (!$condition_type_selector.val()) {
            var new_val = $condition_type_selector.find('option').prop('value');
            $condition_type_selector.val(new_val);
        }

        update_condition_fields($condition_type_selector, data);
        $condition_type_selector.change(function () {
            update_condition_fields($(this));
        });
    }

    function update_condition_fields($el, data) {
        var $container = $el.closest('.wdp-condition');
        var type = $el.val();

        var template = get_template(type, {
            r: get_current_rule_index($el),
            c: $container.data('index')
        });

        $container.find('.wdp-condition-field-sub').html(template);

        if (data && data.options) {

            var fields = $container.find('.wdp-condition-subfield');

            fields.sort(function (a, b) {
                var priority_a = jQuery(a).find('select, input, textarea').attr('name').match(/options]\[(\d+)]/i);
                var priority_b = jQuery(b).find('select, input, textarea').attr('name').match(/options]\[(\d+)]/i);
                priority_a = priority_a[priority_a.length - 1];
                priority_b = priority_b[priority_b.length - 1];

                return +priority_a - +priority_b;
            });

            fields.each( function(index, field) {
                var value_field;

                value_field = $('select[data-list]', field);
                if (value_field.length) {
                    var titles = [], get_title;
                    if (value_field.data('field') === 'autocomplete') {
	                    if (value_field.data('list') === 'product_taxonomies') {
		                    titles = wdp_data.titles[value_field.data('taxonomy')];
                        } else {
		                    titles = wdp_data.titles[value_field.data('list')];
                        }
                        get_title = function(id) { return typeof titles !== 'undefined' ? titles[id] : id; }
                    } else if (value_field.data('field') === 'preloaded') {
                        titles = wdp_data.lists[value_field.data('list')];
                        get_title = function(id) {
                            for (var i = 0; i < titles.length; i++) {
                                if (titles[i].id.toString() === id) return titles[i].text;
                            }
                            return id;
                        }
                    }

                    $.each(data.options[index], function (i, val) {
                        // value_field.find('[value="' + val + '"]').prop('selected', 'selected');
                        value_field.append('<option selected value="' + val + '">' + get_title(val) + '</option>');
                    });
                    return;
                }

                value_field = $('select', field);
                if (value_field.length) {
                    value_field.val( data.options[index] );
                    return;
                }

                value_field = $('input', field);
              var value_field_checkbox = $('input[type="checkbox"]', field);
              if (value_field_checkbox.length) {
                if (data.options[index]) {
                  value_field_checkbox.attr('checked', 'checked');
                }
              } else if (value_field.length) {
                value_field.val(data.options[index]);
              }

                value_field = $('textarea', field);
                if ( value_field.length ) {
                    value_field.text( data.options[index] );
                }
            });
        }

        $container.find('[data-field="date"]').removeClass('hasDatepicker').datepicker({dateFormat:"yy-mm-dd"});
        $container.find('[class="datetimepicker"]').datetimepicker();
        make_select2_tags($container.find('[data-field="tags"]'));

        make_select2_products($container.find('[data-field="autocomplete"]'));
	    make_select2_product_taxonomies($container.find('[data-field="autocomplete"][data-list="product_taxonomies"]'));
        make_select2_preloaded($container.find('[data-field="preloaded"]'));

        $container.find('.wdp-condition-field-method-coupon select').change( function() {
            var disabled = [ 'at_least_one_any', 'none_at_all' ].indexOf( $( this ).val() ) >= 0;
            $container.find('.wdp-condition-field-value-coupon select').prop('disabled', disabled);
            if ( disabled ) {
                $container.find('.wdp-condition-field-value-coupon select').val([]).trigger('change');
            }
        } );

        $container.find('.wdp-condition-field-method-coupon select').each( function() {
            var disabled = [ 'at_least_one_any', 'none_at_all' ].indexOf( $( this ).val() ) >= 0;
            $container.find('.wdp-condition-field-value-coupon select').prop('disabled', disabled);
            if ( disabled ) {
                $container.find('.wdp-condition-field-value-coupon select').val([]).trigger('change');
            }
        } );

	    $container.find( '.wdp-condition-field-method select' ).change( function () {
		    var enable_last = 'in_range' === $( this ).val();
		    $container.find( '.wdp-condition-field-value-last' ).toggle( enable_last );
	    } );

	    $container.find( '.wdp-condition-field-method select' ).each( function () {
		    var enable_last = 'in_range' === $( this ).val();
		    $container.find( '.wdp-condition-field-value-last' ).toggle( enable_last );
        } );

        $container.find( '#combination-any' ).click( function() {
            let select = $( this ).closest( '.wdp-column' ).find('.wdp-condition-field-value select');
            if ( $( this ).is(":checked") ) {
                select.val('').trigger('change');
                select.prop('disabled', true);
            } else {
                select.prop('disabled', false);
            }
        } );
    }

    function add_limit($el, data) {
        $el.closest('.postbox').find('.wdp-btn-add-limit').hide();

        var template = get_template('limit_row', {
            l: get_new_limit_index($el)
        });

        var $limit = $(template);

        $el.closest('.postbox')
            .find('.wdp-limits').show()
            .find('.wdp-limits-container').append($limit);

        if (data) {
            $limit.find('.wdp-limit-type select').val(data.type);
        }

        $limit.find('.wdp-limit-remove').click(function () {
            var $rule = $(this).closest('.postbox');
            $(this).closest('.wdp-limit').remove();

            var limits_count = $rule.find('.wdp-limits .wdp-limit').length;
            if (limits_count === 0) {
                $rule.find('.wdp-limits').hide();
                $el.closest('.postbox').find('.wdp-btn-add-limit').show();
            }
        });

        var $limit_type_selector = $limit.find('.wdp-limit-type select');
        update_limit_fields($limit_type_selector, data);
        $limit_type_selector.change(function () {
            update_limit_fields($(this));
        });
    }

    function update_limit_fields($el, data) {
        var $container = $el.closest('.wdp-limit');
        var type = $el.val();

        var template = get_template(type + '_limit', {
            l: $container.data('index'),
        });

        $container.find('.wdp-limit-field-sub').html(template);

        if (data && data.options) {
            $container.find('.wdp-limit-value input').val( data.options );
        }
    }

    function add_range($el, data) {
        var $postbox = $el.closest('.postbox');

        var template = get_template('adjustment_bulk', {
            r: get_current_rule_index($el),
            b: get_new_range_index($el)
        });

        $postbox.find('.wdp-ranges-empty').hide();

        var last_range_to_value;
        var el_last_range_to_value = $postbox.find('.wdp-ranges .wdp-range:last .adjustment-to');
        if (el_last_range_to_value.length) {
            last_range_to_value = el_last_range_to_value.val();
        }

        var $range = $(template);

        $postbox.find('.wdp-ranges').append($range);

        if (last_range_to_value) {
            $range.find('.adjustment-from').val(parseInt(last_range_to_value)+1);
            $range.find('.adjustment-to').focus();
        } else {
            $range.find('.adjustment-from').focus();
		}

        if (data) {
            $range.find('.adjustment-from').val(data.from);
            $range.find('.adjustment-to').val(data.to);
            $range.find('.adjustment-value').val(data.value);
        }

        $range.find('.wdp-range-remove').click(function () {
            var $rule = $(this).closest('.postbox');
            $(this).closest('.wdp-range').remove();

            var ranges_count = $rule.find('.wdp-ranges .wdp-range').length;
            if (ranges_count === 0) {
                $postbox.find('.wdp-ranges-empty').show();
            }
        });
    }

    function fill_get_products_options($container, data) {
        if (data) {
            if (data.repeat) {
                $container.find('.wdp-get-products-repeat select').val(data.repeat);
            }

            if (data.repeat_subtotal) {
                $container.find('.wdp-get-products-repeat .repeat-subtotal-value').val(data.repeat_subtotal);
            }
        }

        update_get_products_options_visibility($container);
    }

    function add_get_products($container, data) {
        $container.closest('.wdp-get-products-block').show();
        $container.closest('.postbox').find('.wdp-btn-add-getproduct').hide();

        var template = get_template('adjustment_deal', {
            r: get_current_rule_index($container),
            f: get_new_product_filter_index($container)
        });

        var $product_filter = $(template);
		let productFilterItem = $product_filter;

        $container.append($product_filter);

        let giftTypeSelector = $product_filter.find('.wdp-condition-field-gift-mode select');

        if (data) {
            if (data['qty']) $product_filter.find('.wdp-condition-field-qty input').val(data['qty']);

			if (data['gift_mode']) {
				giftTypeSelector.val(data['gift_mode']);
			}
        }

        let giftTypeSelectorOnChange = function(value, addProductData) {
			if ( value === "use_product_from_filter" ) {
				addProductData = {
					'value': {},
				}
				updateGiftableProductFilterFields(
					"giftable_products",
					get_current_rule_index(productFilterItem),
					get_current_product_filter_index(productFilterItem),
					productFilterItem,
					addProductData,
					'get_products][value'
				);

				$product_filter.find('.wdp-condition-field-sub select').prop("disabled", true);
			} else if ( value === "allow_to_choose" || value === "giftable_products" || value === "require_to_choose" || value === "giftable_products_in_rotation" || value === "giftable_products_in_random" ) {
				updateGiftableProductFilterFields(
					"giftable_products",
					get_current_rule_index(productFilterItem),
					get_current_product_filter_index(productFilterItem),
					productFilterItem,
					addProductData,
					'get_products][value'
				);
			} else if ( value === "allow_to_choose_from_product_cat" || value === "require_to_choose_from_product_cat" ) {
				updateGiftableProductFilterFields(
					"giftable_categories",
					get_current_rule_index(productFilterItem),
					get_current_product_filter_index(productFilterItem),
					productFilterItem,
					addProductData,
					'get_products][value'
				);
			}
		}

		giftTypeSelector.on('change', function (e) {
			giftTypeSelectorOnChange(this.value, {});
		})
		giftTypeSelectorOnChange(giftTypeSelector.val(), data);

        var $rule = $container.closest('.postbox');

        $product_filter.find('.wdp_filter_remove').click(function () {
            var $product_filter_container = $(this).closest('.wdp-get-products');
            $product_filter.remove();

            var filters_count = $product_filter_container.find('.wdp-filter-item').length;
            if (filters_count === 0) {
                $container.closest('.wdp-get-products-block').hide();
                $container.closest('.postbox').find('.wdp-btn-add-getproduct').show();
            }
        });

    }

    function add_product_adjustment($container, data) {
        $container.show();

        var $rule = $container.closest('.postbox');
        $rule.find('.wdp-btn-add-product-adjustment').hide();

        $rule.find('.wdp_product_adjustment_remove').click(function () {
            $rule.find('.wdp-btn-add-product-adjustment').show();
            $container.hide();
            flushInputs($container);
        });

        var type;
        if (data) {
            type = data.type;
            $container.find('.adjustment-mode-' + type).attr('checked', 'checked');

            if (data.total) {
                $container.find('.adjustment-total-type').val(data.total.type);
                $container.find('.adjustment-total-value').val(data.total.value);
            }

            $container.find('.adjustment-split').each(function(index) {
                if (data.split && data.split[index]) {
                    fill_product_adjustment_split($(this), data.split[index]);
                }
                else {
                    fill_product_adjustment_split($(this));
                }
            });
            if (data['max_discount_sum']) {
                $container.find('.product-adjustments-max-discount').val(data['max_discount_sum']);
            }
            if (data['split_discount_by']) {
              $container.find('.split-discount-by-' + data['split_discount_by']).attr('selected', 'selected');
            }
        } else {
            type = 'total';
            $container.find('.adjustment-mode-total').attr('checked', 'checked');
            $container.find('.adjustment-total-type').find('option:first-child').prop('selected', 'selected');
            $container.find('.adjustment-split-discount-type').find('option:first-child').prop('selected', 'selected');

            $container.find('.adjustment-split').each(function() {
                fill_product_adjustment_split($(this));
            });
        }

        $container.find('.adjustment-mode').change(function () {
            updateElementsVisisibilyInRowForElementValue($(this).val(), $(this).closest('.wdp-product-adjustments'));
        });
        updateElementsVisisibilyInRowForElementValue(type, $container);

      $rule.change(function() {
        updateElementsVisibilityDiscountSplit($(this).find('.wdp-product-adjustments'), $(this));
      })
      updateElementsVisibilityDiscountSplit($container, $rule);
    }

    function add_product_adjustment_split($container, adj_index, data) {
        var template = get_template('adjustment_split_row', {
            adj: adj_index
        });

        var $split_adjustment = $(template);

        $container.closest('.postbox')
            .find('.wdp-product-adjustments-split-container').append($split_adjustment);

        fill_product_adjustment_split($split_adjustment, data);
    }

    function fill_product_adjustment_split($split_adjustment, data) {
        if (data) {
            $split_adjustment.find('.adjustment-split-type').val(data.type);
            $split_adjustment.find('.adjustment-split-value').val(data.value);
        }
        else {
            $split_adjustment.find('.adjustment-split-type').find('option:first-child').prop('selected', 'selected');
        }
    }

    function add_bulk_adjustment($container, data) {
        bulk_adjustment().add($container, data);
    }

    function add_cart_adjustment($el, data) {
        $el.closest('.postbox').find('.wdp-btn-add-cart-adjustment').hide();

        var template = get_template('cart_adjustment_row', {
            ca: get_new_cart_adjustment_index($el)
        });

        var $cart_adjustment = $(template);

        $el.closest('.postbox')
            .find('.wdp-cart-adjustments').show()
            .find('.wdp-cart-adjustments-container').append($cart_adjustment);

        if (data) {
            $cart_adjustment.find('.wdp-cart-adjustment-type select').val(data.type);
        }

        $cart_adjustment.find('.wdp-cart-adjustment-remove').click(function () {
            var $rule = $(this).closest('.postbox');
            $(this).closest('.wdp-cart-adjustment').remove();

            var adjs_count = $rule.find('.wdp-cart-adjustments .wdp-cart-adjustment').length;
            if (adjs_count === 0) {
                $rule.find('.wdp-cart-adjustments').hide();
                $el.closest('.postbox').find('.wdp-btn-add-cart-adjustment').show();
            }
        });

        var $adj_type_selector = $cart_adjustment.find('.wdp-cart-adjustment-type select');
        update_cart_adjustment_fields($adj_type_selector, data);
        $adj_type_selector.change(function () {
            update_cart_adjustment_fields($(this));
        });
    }

	function add_role_discount( $el, data ) {
		$el.closest( '.postbox' ).find( '.wdp-btn-add-role-discount' ).hide();

		var template = get_template( 'role_discount_row', {
			indx: get_new_role_discount_index( $el )
		} );

		var $role_discount = $( template );

		$el.closest( '.postbox' )
		   .find( '.wdp-role-discounts' ).show()
		   .find( '.wdp-role-discounts-container' ).append( $role_discount );

		if ( data ) {
			$role_discount.find( 'input.wdp-role-discount-value, select.wdp-role-discount-value' ).each(
				function ( index, el ) {
					var field_name = $( el ).data( 'field-name' );
					var field_value = data[field_name];
					if ( field_value !== undefined ) {
						if ( "roles" === field_name ) {
							var html = '';
							$.each( field_value, function ( i, id ) {
								html += '<option selected value="' + id + '">' + get_role_label(id) + '</option>';
							} );
							$( this ).append( html );
						} else {
							$( this ).val( field_value );
						}
					}
				} );
		}

		make_select2_preloaded( $role_discount.find( '[data-field="preloaded"]' ) );

		$role_discount.find( '.wdp_role_discount_remove' ).click( function () {
			var $rule = $( this ).closest( '.postbox' );
			$( this ).closest( '.wdp-role-discount' ).remove();

			var role_discounts_count = $rule.find( '.wdp-role-discounts .wdp-role-discount' ).length;
			if ( role_discounts_count === 0 ) {
				$rule.find( '.wdp-role-discounts' ).hide();
				$el.closest( '.postbox' ).find( '.wdp-btn-add-role-discount' ).show();

				// Unconditionally hide all sortable handlers
                $rule.find(".wdp-drag-handle").hide();
                $rule.find(".sortable-apply-mode-block").hide();
            }
        } );

	}

    function update_cart_adjustment_fields($el, data) {
        var $container = $el.closest('.wdp-cart-adjustment');
        var type = $el.val();

        var template = get_template(type + '_cart_adjustment', {
            ca: $container.data('index'),
        });

        $container.find('.wdp-cart-adjustment-field-sub').html(template);

        if (data && data.options) {
            $container.find('.wdp-cart-adjustment-value input, .wdp-cart-adjustment-value select').each(function (index) {
                if (data.options[index] !== undefined) {
                    jQuery(this).val(data.options[index]);
                }

              var fields = $container.find('.wdp-cart-adjustment-value');
              fields.each( function(index, field) {
                var value_field;

                value_field = $('select[data-list]', field);
                if (value_field.length) {
                  var titles = [], get_title;
                  if (value_field.data('field') === 'preloaded') {
                    titles = wdp_data.lists[value_field.data('list')];
                    get_title = function(id) {
                      for (var i = 0; i < titles.length; i++) {
                        if (titles[i].id.toString() === id) return titles[i].text;
                      }
                      return id;
                    }
                  }

                  if ( typeof data.options[index] === "string" ) {
                    value_field.append('<option selected value="' + data.options[index] + '">' + get_title(data.options[index]) + '</option>');
                  } else {
                    $.each(data.options[index], function (i, val) {
                      value_field.append('<option selected value="' + val + '">' + get_title(val) + '</option>');
                    });
                  }
                }
              });
            });
        }

        make_select2_preloaded($container.find('[data-field="preloaded"]'));
    }

    function updateDealOption($row, type) {
        var before = '', after = '';
        if (type === 'free') {
            $row.find('.wdp-condition-field-deal-options').hide();
            return;
        } else if (type === 'price__fixed') {
            after = wdp_data.labels.currency_symbol;
        } else if (type === 'discount__percentage') {
            after = '%';
        } else if (type === 'discount__amount') {
            before = '-';
            after = wdp_data.labels.currency_symbol;
        }

        $row.find('.wdp-condition-field-deal-options').show();
        $row.find('.wdp-condition-field-deal-options--before').html(before);
        $row.find('.wdp-condition-field-deal-options--after').html(after);
        $row.find('.wdp-condition-field-deal-options input').val('');
    }

    function update_get_products_options_visibility($rule) {
        var $type_val = $rule.find('.wdp-get-products-repeat select').val();

        if ( $type_val === 'based_on_subtotal' || $type_val === 'based_on_subtotal_after_discount' ) {
            $rule.find('.wdp-get-products-repeat .repeat-subtotal').show();
        } else {
            $rule.find('.wdp-get-products-repeat .repeat-subtotal').hide();
        }
    }

    function update_get_products_auto_visibility($rule) {
        var $items = $rule.find('.wdp-get-products .wdp-filter-item'),
            filter_val = $items.find('.wdp-condition-field-value select').val(),
            filter_type = $items.find('.wdp-filter-type').val(),
            deal_type = $items.find('.wdp-condition-field-deal-type select').val();

        var show = $items.length === 1 && filter_type === 'products' && deal_type === 'free' &&
                filter_val && filter_val.length === 1;

        $rule.find('.wdp-get-products-auto').toggle(show);
    }


    /* Utils */
    // find template by id, replace variables by values and return string
        function get_template(name, variables) {
        var template = $('#' + name + '_template').html() || '';
        for (var v in variables) {
            template = template.replace(new RegExp('{' + v + '}', 'g'), variables[v]);
        }
        return template;
    }

    // find next index for condition row
    function get_new_condition_index($el) {
        var newIndex = 0;

        $el.closest('.postbox').find('.wdp-conditions .wdp-condition').each(function (i, el) {
            var index = ~~ $(el).data('index');
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

    // returns index rule where eleemnt placed
    function get_current_rule_index($el) {
        return $el.closest('.postbox').data('index');
    }

    // find next index for filter row
    function get_new_product_filter_index($container) {
        var newIndex = 0;

        $container.find('.wdp-filter-item').each(function (i, el) {
            var index = ~~$(el).data('index');
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

    // returns index of filter where element placed
    function get_current_product_filter_index($el) {
        return $el.closest('.wdp-filter-item').data('index');
    }

    // find next index for limit row
    function get_new_limit_index($el) {
        var newIndex = 0;

        $el.closest('.postbox').find('.wdp-limits .wdp-limit').each(function (i, el) {
            var index = ~~$(el).data('index');
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

    // find next index for cart adjustment row
    function get_new_cart_adjustment_index($el) {
        var newIndex = 0;

        $el.closest('.postbox').find('.wdp-cart-adjustments .wdp-cart-adjustment').each(function (i, el) {
            var index = ~~$(el).data('index');
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

	function get_new_role_discount_index( $el ) {
		var newIndex = 0;

		$el.closest( '.postbox' ).find( '.wdp-role-discounts .wdp-role-discount' ).each( function ( i, el ) {
			var index = ~ ~ $( el ).data( 'index' );
			if ( index >= newIndex ) {
				newIndex = index + 1;
			}
		} );

		return newIndex;
	}

    // find next index for range row
    function get_new_range_index($el) {
        var newIndex = 0;

        $el.closest('.postbox').find('.wdp-ranges .wdp-range').each(function (i, el) {
            var index = ~~$(el).data('index');
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

    function get_last_priority() {
        var newIndex = 0;

        $('#rules-container .postbox').each(function (i, el) {
            var index = ~~ $('.rule-priority', el).val();
            if (index >= newIndex) newIndex = index + 1;
        });

        return newIndex;
    }

    // make select to select2 autocomplete
    function make_select2_products($els) {
        $els.each(function (index, el) {
            var $el = $(el);

	        if ( $el.data( 'list' ) === 'product_taxonomies' ) {
		        return true;
	        }

            $el.select2({
                width: '100%',
				closeOnSelect: wdp_data.options.close_on_select,
				minimumInputLength: 1,
                placeholder: $el.data('placeholder'),
                escapeMarkup: function (text) { return text; },
                language: {
                    errorLoading: function () {
						return wdp_data.labels.select2_error_loading;
					},
					inputTooLong: function (args) {
						var overChars = args.input.length - args.maximum;

						var message = wdp_data.labels.select2_input_too_long.replace('%d', overChars);

						if (overChars != 1) {
							message += 's';
						}

						return message;
					},
					inputTooShort: function (args) {
						var remainingChars = args.minimum - args.input.length;

						var message = wdp_data.labels.select2_input_too_short.replace('%d', remainingChars);

						return message;
						},
						loadingMore: function () {
						return 'Loading more results…';
					},
					maximumSelected: function (args) {
						var message = wdp_data.labels.select2_maximum_selected.replace('%d', args.maximum);

						if (args.maximum != 1) {
							message += 's';
						}

						return message;
					},
					noResults: function () {
						return wdp_data.labels.select2_no_results;
					},
					searching: function () {
						return wdp_data.labels.select2_searching;
					},
                },
                ajax: {
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
						let $rule = $el.closest('.postbox');
                        let new_params = {
                            query: params.term,
                            action: 'wdp_ajax',
                            method: $el.data('list') || 'product',
                            selected: $el.val(),
							current_rule: $rule.find('.rule-id').val()
                        };
                        new_params[wdp_data.security_query_arg] = wdp_data.security;
                        return new_params;
                    },
                    processResults: function (response) {
                        return { results: response.data || [] };
                    }
                }
            });

            $el.on('select2:select', function (e) {
                var type  = $el.attr('data-list');

                var id    = e.params.data.id;
                var title = e.params.data.text;

                if (typeof e.params.data.bundle !== 'undefined') {
                    var bundle = e.params.data.bundle;
                    var current = $(this).val() ? $(this).val() : [];
                    var exclude = [id];

                    for (var i = 0; i < bundle.length; i++) {
                        id = bundle[i].id;
                        title = bundle[i].text;

                        if (!$(this).find("option[value='" + id + "']").length) {
                            $(this).append(new Option(title, id, false, false));
                            wdp_data.titles[type][id] = title;
                        }

                        if (current.indexOf(id) !== -1) {
                            exclude.push(id);
                        } else {
                            current.push(id);
                        }
                    }

                    current = current.filter(function (item) {
                        return exclude.indexOf(item) === -1;
                    });

                    $(this).val(current).trigger('change');
                } else {
                  if ( typeof wdp_data.titles[type] !== 'undefined') {
                    wdp_data.titles[type][id] = title;
                  }
                }
            });

            $el.parent().find('.select2-search__field').css('width', '100%');
        });
    }

	function make_select2_product_taxonomies($els) {
		$els.each(function (index, el) {
			var $el = $(el);

			$el.select2({
				width: '100%',
				closeOnSelect: wdp_data.options.close_on_select,
				minimumInputLength: 1,
				placeholder: $el.data('placeholder'),
				escapeMarkup: function (text) { return text; },
				language: {
					errorLoading: function () {
						return wdp_data.labels.select2_error_loading;
					},
					inputTooLong: function (args) {
						var overChars = args.input.length - args.maximum;

						var message = wdp_data.labels.select2_input_too_long.replace('%d', overChars);

						if (overChars != 1) {
							message += 's';
						}

						return message;
					},
					inputTooShort: function (args) {
						var remainingChars = args.minimum - args.input.length;

						var message = wdp_data.labels.select2_input_too_short.replace('%d', remainingChars);

						return message;
						},
						loadingMore: function () {
						return 'Loading more results…';
					},
					maximumSelected: function (args) {
						var message = wdp_data.labels.select2_maximum_selected.replace('%d', args.maximum);

						if (args.maximum != 1) {
							message += 's';
						}

						return message;
					},
					noResults: function () {
						return wdp_data.labels.select2_no_results;
					},
					searching: function () {
						return wdp_data.labels.select2_searching;
					},
				},
				ajax: {
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						let new_params = {
							query: params.term,
							action: 'wdp_ajax',
							method: $el.data('list') || 'product',
							taxonomy: $el.data('taxonomy') || '',
							selected: $el.val()
                        };

                        new_params[wdp_data.security_query_arg] = wdp_data.security;
                        return new_params;
					},
					processResults: function (response) {
						return { results: response.data || [] };
					}
				}
			});

			$el.on('select2:select', function (e) {
				var type  = $el.attr('data-taxonomy');
				var id    = e.params.data.id;
				var title = e.params.data.text;

				wdp_data.titles[type][id] = title;
			});

			$el.parent().find('.select2-search__field').css('width', '100%');
		});
	}


    function make_select2_preloaded($els) {
        $els.each(function (index, el) {
            var $el = $(el);
            var data = wdp_data.lists[ $el.data('list') ];

            let close_on_select = $el.attr("multiple") !== undefined ? wdp_data.options.close_on_select : true
            let minimumInputLength = $el.attr("multiple") !== undefined ? 1 : 0

            $el.select2({
                width: '100%',
				closeOnSelect: close_on_select,
                escapeMarkup: function (text) { return text; },
                minimumInputLength: minimumInputLength,
                placeholder: $el.data('placeholder'),
                language: {
                    errorLoading: function () {
						return wdp_data.labels.select2_error_loading;
					},
					inputTooLong: function (args) {
						var overChars = args.input.length - args.maximum;

						var message = wdp_data.labels.select2_input_too_long.replace('%d', overChars);

						if (overChars != 1) {
							message += 's';
						}

						return message;
					},
					inputTooShort: function (args) {
						var remainingChars = args.minimum - args.input.length;

						var message = wdp_data.labels.select2_input_too_short.replace('%d', remainingChars);

						return message;
						},
						loadingMore: function () {
						return 'Loading more results…';
					},
					maximumSelected: function (args) {
						var message = wdp_data.labels.select2_maximum_selected.replace('%d', args.maximum);

						if (args.maximum != 1) {
							message += 's';
						}

						return message;
					},
					noResults: function () {
						return wdp_data.labels.select2_no_results;
					},
					searching: function () {
						return wdp_data.labels.select2_searching;
					},
                },
                data: data
            });

            $el.parent().find('.select2-search__field').css('width', '100%');
        });
    }

    // make select to select2 with tags
    function make_select2_tags($els) {
        $els.each(function (index, el) {
            var $el = $(el);
            $el.select2({ width: '100%' });
            $el.parent().find('.select2-search__field').css('width', '100%');
        });
    }

    // update rule title
    function update_rule_title($rule) {
        var title = $rule.find('h2.hndle.ui-sortable-handle > span');

        // check if rule enabled
        var $toggler = $rule.find('.wdp-field-enabled select');
        var disabled = $toggler.val() === 'off';
        title.toggleClass('wdp-title-disabled', disabled);
        $rule.toggleClass('disabled', disabled);

        // check if bulk rule repeated
        var repeat = false;
        var $checkbox_repeat = $rule.find('.wdp-adjustments-repeat');
        if ($checkbox_repeat.length) {
            repeat = $checkbox_repeat.prop('checked');
        }
        title.toggleClass('wdp-title-repeat', repeat);

        // check if cart rule provide discount or fee
        var cart_adjustment_type = $rule.find('.cart-adjustment-type ').val() || '';
        title.toggleClass('wdp-title-discount', cart_adjustment_type.indexOf('discount') === 0);
        title.toggleClass('wdp-title-fee', cart_adjustment_type.indexOf('fee') === 0);
    }

    // make lists inside rule sortable
    function make_sortable($container) {
        $container.sortable({
            containment: 'parent',
            items: '.wdp-row',
            cursor: 'move',
            axis:   'y',
            opacity: 0.65
        });
    }

    function updateElementsVisisibilyInRowForElementValue(value, $container) {
        var $row_elements = $container.find('[data-show-if]');
        $row_elements.each(function (i, el) {
            var $el = $(el);
            var show_if = $el.data('show-if').split(',');
            var visible = show_if.indexOf(value) >= 0;
            $el.toggle( visible );

            if (!visible) {
                // flushInputs($el);
            }
        });
    }

    function flushInputs($container) {
        $container.find('input:not([data-readonly]), select:not([data-readonly]), textarea:not([data-readonly])').val('');
    }

    $('.hide-disabled-rules').change(function () {
        var checked = $(this).prop('checked');
        remove_get_parameter('hide_inactive');
        remove_get_parameter('paged');

        if (checked) {
            window.location.href += '&hide_inactive=1';
        } else {
            window.location.reload();
        }
        // $('#rules-container').toggleClass('hide-disabled', $(this).val() );
    });

	$('.hide-rules-coupons-applied').change(function () {
		var checked = $(this).prop('checked');
		var result  = checked === true ? 1 : 0;
		remove_get_parameter('disable_all_rules_coupon_applied');
		remove_get_parameter('paged');

		window.location.href += '&disable_all_rules_coupon_applied=' + result;
	});

    $('.wdp-btn-rebuild-onsale-list').click(function () {

    $( "#progress_div" ).show();

    let new_params = {
        action: 'wdp_ajax',
        method: 'rebuild_onsale_list',
    };
    new_params[wdp_data.security_query_arg] = wdp_data.security;

	$.post(
            ajaxurl,
            new_params,
            function (d) {
		$( "#progress_div" ).hide();
	    },
            'json'
        );
    });

    $('.wdp-btn-rebuild-bogo-list').click(function () {

        $( "#progress_div" ).show();

        let new_params = {
            action: 'wdp_ajax',
            method: 'rebuild_bogo_list',
        };
        new_params[wdp_data.security_query_arg] = wdp_data.security;

        $.post(
                ajaxurl,
                new_params,
                function (d) {
            $( "#progress_div" ).hide();
            },
                'json'
            );
    });

    function remove_get_parameter(parameterName) {
        var result = null,
            clean_uri = null,
            tmp = [];

        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === parameterName) {
                    result = decodeURIComponent(tmp[1]);
                    clean_uri = window.location.toString().replace("&" + tmp[0] + "=" + tmp[1], "");
                    clean_uri = clean_uri.replace(tmp[0] + "=" + tmp[1], "");
                    clean_uri = clean_uri.replace(/\?$/ig, "");
                }
            });

        if (result && clean_uri) {
            window.history.replaceState({}, document.title, clean_uri)
        }
        return result;
    };

    function deparam(params){

        var digitTest = /^\d+$/,
            keyBreaker = /([^\[\]]+)|(\[\])/g,
            plus = /\+/g,
            paramTest = /([^?#]*)(#.*)?$/;

        if(! params || ! paramTest.test(params) ) {
            return {};
        }


        var data = {},
            pairs = params.split('&'),
            current;

        for(var i=0; i < pairs.length; i++){
            current = data;
            var pair = pairs[i].split('=');

            // if we find foo=1+1=2
            if(pair.length != 2) {
                pair = [pair[0], pair.slice(1).join("=")]
            }

            var key = decodeURIComponent(pair[0].replace(plus, " ")),
                value = decodeURIComponent(pair[1].replace(plus, " ")),
                parts = key.match(keyBreaker);

            for ( var j = 0; j < parts.length - 1; j++ ) {
                var part = parts[j];
                if (!current[part] ) {
                    // if what we are pointing to looks like an array
                    current[part] = digitTest.test(parts[j+1]) || parts[j+1] == "[]" ? [] : {}
                }
                current = current[part];
            }
            lastPart = parts[parts.length - 1];
            if(lastPart == "[]"){
                current.push(value)
            }else{
                current[lastPart] = value;
            }
        }
        return data;
    }

    function get_role_label(id) {
        var roles_list = wdp_data.lists.user_roles;

        for (var i = 0; i < roles_list.length; i++) {
            if (typeof roles_list[i] !== 'undefined' && roles_list[i].id === id) {
                return roles_list[i].text;
            }
        }

        return id
    }

    function updateElementsVisibilityDiscountSplit($container, $rule) {
      if ( isPackageRule($rule) ) {
        $container.find(".split-discount-controls").show();
      } else{
        $container.find(".split-discount-controls").hide();
      }
    }

  function isPackageRule($container) {
    let $product_filter_container = $container.find('.wdp-filter-block');
    let filters = $product_filter_container.find('.wdp-filter-item');

    if (filters.length === 1) {
      let filter = filters.first()
      let qty = filter.find('.wdp-condition-field-qty input').val();

      return parseInt(qty) > 1;
    } else if (filters.length > 1) {
      return true
    }

    return false;
  }
});
