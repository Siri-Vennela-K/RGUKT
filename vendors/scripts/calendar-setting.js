(function () {
	"use strict";
	jQuery(function () {
		// Initialize FullCalendar
		const calendar = jQuery("#calendar").fullCalendar({
			themeSystem: "bootstrap4",
			businessHours: false,
			defaultView: "month",
			editable: true,
			header: {
				left: "title",
				center: "month,agendaWeek,agendaDay",
				right: "today prev,next",
			},
			events: function (start, end, timezone, callback) {
				// Fetch events dynamically from server
				$.ajax({
					url: "studentdashboard.php", // Replace with your PHP file that returns event data
					type: "GET",
					success: function (response) {
						const data = JSON.parse(response);
						if (data.success) {
							callback(data.data); // Provide events to the calendar
						} else {
							AlertMessage('error', 'Failed to load events.');
						}
					},
					error: function () {
						AlertMessage('error', 'Error fetching events.');
					}
				});
			},
			dayClick: function (date, jsEvent, view) {
				// Show modal to add event
				jQuery("#modal-view-event-add").modal();
				jQuery("#Save").off('click').on('click', function () {
					// Collect form data
					const postData = {
						action: 'insert',
						reason: jQuery("#reason").val(),
						fromdate: date.format('YYYY-MM-DD HH:mm:ss'), // Use clicked date
						returndate: jQuery("#returndate").val(),
						description: jQuery("#description").val(),
						ecolor: jQuery("#ecolor").val(),
						eicon: jQuery("#eicon").val(),
					};

					$.ajax({
						url: "studentdashboard.php", // Replace with your PHP file
						type: "POST",
						data: postData,
						success: function (response) {
							const data = JSON.parse(response);
							if (data.success) {
								AlertMessage('success', 'Request submitted successfully!');
								jQuery("#modal-view-event-add").modal("hide");
								jQuery("#calendar").fullCalendar("refetchEvents"); // Reload events
							} else {
								AlertMessage('error', data.message);
							}
						},
						error: function () {
							AlertMessage('error', 'Error adding event.');
						}
					});
				});
			},
			eventClick: function (event, jsEvent, view) {
				// Show event details in modal
				jQuery(".event-icon").html("<i class='fa fa-" + event.icon + "'></i>");
				jQuery(".event-title").html(event.title);
				jQuery(".event-body").html(event.description);
				jQuery("#modal-view-event").modal();
			},
		});
	});
})();
