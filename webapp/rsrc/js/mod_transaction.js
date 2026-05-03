$(document).ready(function() {
	$("#trans_sell_date").flatpickr({
		enableTime: true,
		enableSeconds: true,
		time_24hr: true,
		allowInput: true,
		minuteIncrement: 1,
		dateFormat: "Y-m-d H:i:S"
	});
});
