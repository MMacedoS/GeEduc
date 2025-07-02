document.addEventListener("DOMContentLoaded", function () {
	var calendarEl = document.getElementById("selectableCalendar");
	let today = new Date();
	var calendar = new FullCalendar.Calendar(calendarEl, {
		headerToolbar: {
			left: "prev,next today",
			center: "title",
			right: "dayGridMonth,timeGridWeek,timeGridDay",
		},
		initialDate: today.toLocaleDateString().split('/').reverse().join('-').toString(),
		navLinks: true, // can click day/week names to navigate views
		selectable: true,
		selectMirror: true,
		hiddenDays: [0],
		select: function (arg) {
			var title = prompt("Titulo do Evento:");
			if (title) {
				calendar.addEvent({
					title: title,
					start: arg.start,
					end: arg.end,
					allDay: arg.allDay,
				});
			}
			
			$.ajax({
				type: 'POST',
				url: "/dialetivo",
				dataType: 'JSON',
				data: {
					title: title,
					start: arg.startStr, // já vem formatado em 'YYYY-MM-DD'
					end: arg.endStr,     // idem
				},
				success: (res) => {
					alert(res.message);
				}
			});

			calendar.unselect();
		},
		eventClick: function (arg) {			
			if (confirm("Você deseja deletar este evento?")) {
				$.ajax({
					type: 'DELETE',
					url: "/dialetivo/" + arg.event.id,
					dataType: 'JSON',					
					success: (res) => {
						alert(res.message);
					}
				});
				arg.event.remove();
			}
		},
		editable: true,
		dayMaxEvents: true, // allow "more" link when too many events
		events: diasLetivos
	});

	calendar.render();
});
