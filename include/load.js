/*global window,$,jQuery,i*/
/*jslint white: true */

(function() {
	"use strict";

	i.formatDate = (date) => {
			const d = new Date(date);

			let	a = [],
				day = '' + d.getDate(),
				month = '' + (d.getMonth() + 1),
				year = d.getFullYear();

			if (month.length < 2) {
				month = '0' + month;
			}

			if (day.length < 2) {
				day = '0' + day;
			}

			a = [year, month, day];

			return a.join('-');
	};

	// to translate texts
	i.t = (s) => {
		let found = false;
		// are the translation texts available?
		if (typeof i.msg !== "object") {
			return s;
		}

		// walk the translation texts
		Object.keys(i.msg).forEach((a) => {
			if (
				found === false &&
				i != null &&
				i.msg != null &&
				i.msg[a] !== undefined &&
				i.msg[a][0] !== undefined &&
				i.msg[a][1] !== undefined &&
				i.msg[a][0] === s
			) {
				found = i.msg[a][1];
			}
		});

		if (found) {
			return found;
		}
		return s;
	};

	jQuery.extend({
		postJSON: (url, data, callback) => {
			return jQuery.post(url, data, callback, "json");
		}
	});

	$(window.document).ready(() => {
		$('a.confirm').on('click', (e) => {
			if (!window.confirm(i.t('Are you sure that you want to continue? This action cannot be reverted.'))) {
				e.preventDefault();
				return false;
			}
			return true;
		});

		switch (i.view) {
			case 'edit_item':

				// when changing category
				$('select[name="id_categories"]').change((e) => {
					// is this a new category?
					if ($(e.currentTarget).val() === '-1') {
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

				$('#button_aquired_date,#button_disposed_date').click((e) => {

					$(e.currentTarget).prev('input').val(
						i.formatDate(
							new Date(new Date().getTime() + i.time_diff)
						)
					);

					// is this disposed button and the own status is own or own - sell?
					if ($(e.currentTarget).attr('id') === 'button_disposed_date' && ($('select[name="status"').val() === '1' || $('select[name="status"').val() === '2')) {
						// then change own status to sold
						$('select[name="status"').val(3);
					}

					e.preventDefault();
					return false;
				});

				$('#button_material_100_cotton').click((e) => {
					$(e.currentTarget).prev('input').val(
						'100% bomull'
					);

					e.preventDefault();
					return false;
				});

				$('#button_status_sale').click((e) => {
					$(e.currentTarget).prev('select').val(2);
					e.preventDefault();
					return false;
				});

				$('#button_status_sold').click((e) => {
					$(e.currentTarget).prev('select').val(3);
					$('#button_disposed_date').click();
					e.preventDefault();
					return false;
				});

				$('input[name="title"]').focus();

				break;
			case 'edit_packlist':

				$('#button_criterias_remove').click((e) => {
					$('#select_criterias_selected option:selected').each((index, element) => {

						if (!$('#select_criterias_available option[value="' + $(element).value + '"]').length) {
							$('#select_criterias_available')
								.append(
									$('<option>')
										.val($(element).attr('value'))
										.text($(element).text())
								);
						}

						$('#hidden_selected_criterias input[value="' + $(element).attr('value') + '"]').remove();
						$(element).remove();
					});

					e.preventDefault();
					return false;
				});

				$('#button_criterias_add').click((e) => {
					e.preventDefault();
					$('#select_criterias_available option:selected').each((index, element) => {
						// add it to the select box
						if (!$('#select_criterias_selected option[value="' + $(element).attr('value') + '"]').length) {
							$('#select_criterias_selected')
								.append(
									$('<option>')
										.val($(element).attr('value'))
										.text($(element).text())
								);
						}

						// add the hidden input
						if (!$('#hidden_selected_criterias input[value="' + $(element).attr('value') + '"]').length) {
							$('#hidden_selected_criterias').append(
								$('<input/>')
									.attr({
										name: 'id_criterias[]',
										type: 'hidden'
									})
									.val($(element).attr('value'))
							);
						}

						$(element).remove();
					});

					e.preventDefault();
					return false;
				});

				break;
			case 'index':

				$('form.form_add_item_to_packlist').on('submit', (e) => {
					$.postJSON("?action=insert_update_relations_packlists_items&format=json", {
						id_items: $(this).find('input[name="id_items"]').val(),
						id_packlists: $(this).find('select:first').val()
					}, (data) => {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text('Tillagd')
						);
					});

					e.preventDefault();
					return false;
				});

				$('form.form_add_item_to_criteria').on('submit', (e) => {
					$.postJSON("?action=insert_update_relations_criterias_items&format=json", {
						id_items: $(this).find('input[name="id_items"]').val(),
						id_criterias: $(this).find('select:first').val()
					}, (data) => {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text('Tillagd')
						);
					});

					e.preventDefault();
					return false;
				});

				break;
			case 'packlist':

				$('table input[type="checkbox"]').on('change', (e) => {

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

				$('table select').on('change', (e) => {
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

				$('.edit_packlist_item').on('click', (e) => {
					$('form input[name="title"]').val($(this).attr('data-title'));
					$('form input[name="weight"]').val($(this).attr('data-weight'));
					$('#span_id_packlist_items').text($(this).attr('data-id-packlist-items'));
					$('form input[name="id_packlist_items"]').val($(this).attr('data-id-packlist-items'));
					e.preventDefault();
					return false;
				});

				$('#a_form_packlist_item_reset').click((e) => {
					$('#form_edit_packlist_item')[0].reset();
					$('#span_id_packlist_items').text(i.t('New object'));
					$('form input[name="title"]').val('');
					$('form input[name="weight"]').val('');
					$('form input[name="id_packlist_items"]').val(0);

					e.preventDefault();
					return false;
				});

				$('#form_update_packlist_notes').on('submit', (e) => {
					$.postJSON("?action=update_packlist_notes&format=json", {
						id_packlists: $(this).find('input[name="id_packlists"]').val(),
						notes: $(this).find('textarea[name="notes"]').val()
					}, (data) => {
						// when done, show result list, forward result data
						$(this).find('.status').remove();

						$(this).find('input[type="submit"]:first').after(
							$('<span>')
								.addClass('status')
								.text(i.t('Saved'))
						);
					});
					e.preventDefault();
					return false;
				});

				break;
		}
	});
}());
