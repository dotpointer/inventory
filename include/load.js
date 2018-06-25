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

	jQuery.extend({
		postJSON: function (url, data, callback) {
			return jQuery.post(url, data, callback, "json");
		}
	});

	$(window.document).ready(function() {

		$('a.confirm').on('click', function(e) {

			if (!window.confirm(i.msg.confirm)) {
				e.preventDefault();
				return false;
			}

			return true;
		});

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
			case 'edit_packlist':

				$('#button_criterias_remove').click(function(e) {
					$('#select_criterias_selected option:selected').each(function() {
						if (!$('#select_criterias_available option[value="' + this.value + '"]').size()) {
							$('#select_criterias_available')
								.append(
									$('<option>')
										.val(this.value)
										.text(this.text)
								);
						}

						$('#hidden_selected_criterias input[value="' + this.value + '"]').remove();
						$(this).remove();
					});

					e.preventDefault();
					return false;
				});

				$('#button_criterias_add').click(function(e) {
					$('#select_criterias_available option:selected').each(function() {
						// add it to the select box
						if (!$('#select_criterias_selected option[value="' + this.value + '"]').size()) {
							$('#select_criterias_selected')
								.append(
									$('<option>')
										.val(this.value)
										.text(this.text)
								);
						}

						// add the hidden input
						if (!$('#hidden_selected_criterias input[value="' + this.value + '"]').size()) {
							$('#hidden_selected_criterias').append(
								$('<input/>')
									.attr({
										name: 'id_criterias[]',
										type: 'hidden'
									})
									.val(this.value)
							);
						}

						$(this).remove();
					});

					e.preventDefault();
					return false;
				});

				break;
			case 'index':

				$('form.form_add_item_to_packlist').on('submit', function(e) {
					$.postJSON("?action=insert_update_relations_packlists_items&format=json", {
						id_items: $(this).find('input[name="id_items"]').val(),
						id_packlists: $(this).find('select:first').val()
					}, function(data) {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text('Tillagd')
						);
					}.bind(this));

					e.preventDefault();
					return false;
				});

				$('form.form_add_item_to_criteria').on('submit', function(e) {
					$.postJSON("?action=insert_update_relations_criterias_items&format=json", {
						id_items: $(this).find('input[name="id_items"]').val(),
						id_criterias: $(this).find('select:first').val()
					}, function(data) {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text('Tillagd')
						);
					}.bind(this));

					e.preventDefault();
					return false;
				});

				break;
			case 'packlist':

				$('table input[type="checkbox"]').on('change', function(e) {

					if ($(this).prop('checked')) {
						$(this).parent('td').removeClass('unpacked').addClass('packed');
					} else {
						$(this).parent('td').removeClass('packed').addClass('unpacked');
					}

					if ($(this).attr('data-packlist-item') === '0') {
						$.getJSON(".", {
							action: 'update_relations_packlists_items_packed',
							format: 'json',
							id_relations_packlists_items: $(this).attr('data-id-relations-packlists-items'),
							packed: $(this).prop('checked') ? 1 : 0
						});
					} else {
						$.getJSON(".", {
							action: 'update_packlist_items_packed',
							format: 'json',
							id_packlist_items: $(this).attr('data-id-packlist-items'),
							packed: $(this).prop('checked') ? 1 : 0
						});
					}

					e.preventDefault();
					return false;

				});

				$('table select').on('change', function(e) {
					if ($(this).attr('data-packlist-item') === '0') {
						$.getJSON(".", {
							action: 'update_relations_packlists_items_inuse',
							format: 'json',
							id_relations_packlists_items: $(this).attr('data-id-relations-packlists-items'),
							inuse: $(this).val()
						});
					} else {
						$.getJSON(".", {
							action: 'update_packlist_items_inuse',
							format: 'json',
							id_packlist_items: $(this).attr('data-id-packlist-items'),
							inuse: $(this).val()
						});
					}
				});

				$('.edit_packlist_item').on('click', function(e) {
					$('form input[name="title"]').val($(this).attr('data-title'));
					$('form input[name="weight"]').val($(this).attr('data-weight'));
					$('#span_id_packlist_items').text($(this).attr('data-id-packlist-items'));
					$('form input[name="id_packlist_items"]').val($(this).attr('data-id-packlist-items'));
					e.preventDefault();
					return false;
				});

				$('#a_form_packlist_item_reset').click(function(e) {
					$('#form_edit_packlist_item')[0].reset();
					$('#span_id_packlist_items').text('Nytt objekt');
					$('form input[name="title"]').val('');
					$('form input[name="weight"]').val('');
					$('form input[name="id_packlist_items"]').val(0);

					e.preventDefault();
					return false;
				});

				$('#form_update_packlist_notes').on('submit', function(e) {
					$.postJSON("?action=update_packlist_notes&format=json", {
						id_packlists: $(this).find('input[name="id_packlists"]').val(),
						notes: $(this).find('textarea[name="notes"]').val()
					}, function(data) {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text('Sparat')
						);
					}.bind(this));
					e.preventDefault();
					return false;
				});

				break;

		}

	});
}());
