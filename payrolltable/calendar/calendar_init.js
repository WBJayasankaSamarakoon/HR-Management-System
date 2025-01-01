document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        selectable: true,
        events: 'fetch_events.php',

        // Add event on date click
        dateClick: function (info) {
            var title = prompt("Enter Event Title:");
            if (title) {
                fetch('add_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `title=${title}&start=${info.dateStr}&end=${info.dateStr}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "Success") {
                        calendar.refetchEvents();  // Refresh events
                    } else {
                        alert('Failed to add event.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        },

        // Show custom modal with delete button
        eventClick: function (info) {
            var event = info.event;
            var eventId = event.id;
            var eventTitle = event.title;

            // Show a custom modal or dialog with delete option
            var deleteConfirmation = confirm(`Do you want to delete the event: "${eventTitle}"?`);

            if (deleteConfirmation) {
                fetch('delete_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${eventId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === "Success") {
                        calendar.refetchEvents();  // Refresh events
                    } else {
                        alert('Failed to delete event.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        },

        // Update event on drag & drop or resize
        eventDrop: function (info) {
            var updatedEvent = {
                id: info.event.id,
                start: info.event.start.toISOString(),
                end: info.event.end ? info.event.end.toISOString() : null
            };

            fetch('update_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${updatedEvent.id}&start=${updatedEvent.start}&end=${updatedEvent.end}`
            })
            .then(response => response.text())
            .then(data => {
                if (data !== "Success") {
                    alert('Failed to update event.');
                    info.revert();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    calendar.render();
});
