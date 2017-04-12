/*global window,$,jQuery,i*/
/*jslint white: true */

(function() {
	"use strict";


	i.formatDate = function(date) {
			var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear(),
			a = [];

			if (month.length < 2) {
				month = '0' + month;
			}
			if (day.length < 2) {
				day = '0' + day;
			}

			a = [year, month, day];


			return a.join('-');
	};

	$(window.document).ready(function() {

		switch (i.view) {
			case 'edit_item':

				// when changing category
				$('select[name="id_categories"]').change(function() {

					// is this a new category?
					if ($(this).val() === '-1') {
						// then show new category field
						$('input[name="category"]').show();
						$('input[name="category"]').attr('disabled', false);
					// or is this not a new category
					} else {
						// then hide new category field
						$('input[name="category"]').hide();
						$('input[name="category"]').attr('disabled', true);
					}
				});

				$('#button_aquired_date,#button_disposed_date').click(function(e) {

					$(this).prev('input').val(
						i.formatDate(
							new Date(new Date().getTime() + i.time_diff)
						)
					);

					// is this disposed button and the own status is own or own - sell?
					if ($(this).attr('id') === 'button_disposed_date' && ($('select[name="status"').val() === '1' || $('select[name="status"').val() === '2')) {
						// then change own status to sold
						$('select[name="status"').val(3);
					}

					e.preventDefault();
					return false;
				});

				$('#button_material_100_cotton').click(function(e) {

					$(this).prev('input').val(
						'100% bomull'
					);

					e.preventDefault();
					return false;
				});


				$('#button_status_sale').click(function(e) {
					$(this).prev('select').val(2);
					e.preventDefault();
					return false;
				});

				$('#button_status_sold').click(function(e) {
					$(this).prev('select').val(3);
					$('#button_disposed_date').click();
					e.preventDefault();
					return false;
				});




				$('input[name="title"]').focus();

				break;
		}

	});
}());
