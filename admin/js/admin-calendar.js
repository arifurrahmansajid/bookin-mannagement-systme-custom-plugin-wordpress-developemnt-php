document.addEventListener('DOMContentLoaded', function() {
	var calendarEl = document.getElementById('cbm-calendar');
	if (!calendarEl) return;

	var calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: 'dayGridMonth',
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
		events: typeof cbm_calendar_data !== 'undefined' ? cbm_calendar_data.events : [],
		eventClick: function(info) {
			info.jsEvent.preventDefault();
			
			var title = info.event.title;
			var status = info.event.extendedProps.status;
			var detailsJson = info.event.extendedProps.details || '{}';
			
			// Parse dynamic details
			var detailsObj = {};
			try {
				detailsObj = JSON.parse(detailsJson);
			} catch(e) {
				detailsObj = { "Information": detailsJson };
			}
			
			var htmlContent = '<div style="text-align: left; background: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #eee;">';
			
			// Format status color
			var statusColor = '#f1c40f'; // pending
			if(status === 'approved') statusColor = '#2ecc71';
			if(status === 'rejected') statusColor = '#e74c3c';
			
			htmlContent += '<p style="margin-top:0;"><strong>Status:</strong> <span style="color:' + statusColor + '; font-weight:bold; text-transform:uppercase;">' + status + '</span></p><hr style="border-top:1px solid #ddd;">';
			
			// Loop through dynamic details
			var hasDetails = false;
			for (var key in detailsObj) {
				if (detailsObj.hasOwnProperty(key)) {
					hasDetails = true;
					var cleanKey = key.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
					htmlContent += '<p style="margin-bottom:8px;"><strong>' + cleanKey + ':</strong><br> ' + detailsObj[key] + '</p>';
				}
			}
			
			if(!hasDetails) {
				htmlContent += '<p>No extra details provided.</p>';
			}
			
			htmlContent += '</div>';

			Swal.fire({
				title: title,
				html: htmlContent,
				icon: 'info',
				confirmButtonText: 'Close',
				confirmButtonColor: '#3498db'
			});
		}
	});

	calendar.render();
});
