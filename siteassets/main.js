google.charts.load('current', {'packages':['gauge']});
google.charts.setOnLoadCallback(drawChart);
function hideguage() {
	$("#mainmeterlive").html('<p><i>Meter offline</i></p>');
}
function drawChart() {
	var mainmeterchartdata = google.visualization.arrayToDataTable([
	  ['Label', 'Value'],
	  ['kW', 0]
	]);

	var mainmeterchartoptions = {
	  width: "100%",
	  redFrom: 15, redTo: 30,
	  yellowFrom:5, yellowTo: 15,
	  greenFrom: 0, greenTo: 1,
	  min: 0, max: 30,
	  minorTicks: 5, majorTicks: [0,5,10,15,20,25,30]
	};

	var mainmeterchart = new google.visualization.Gauge(document.getElementById('mainmeterlive'));

	mainmeterchart.draw(mainmeterchartdata, mainmeterchartoptions);

	setInterval(function() {
	  $.ajax({
			url: 'api/meterlive.php?meterid=1',
			success: function (response) {
				if (response == "OUTOFSYNC") {
					hideguage();
				} else if ($.isNumeric(response)) {
					mainmeterchartdata.setValue(0, 1, response);
					mainmeterchart.draw(mainmeterchartdata, mainmeterchartoptions);
				} else {
					//Some other error - possibly from server
				}
			},
			error: function (jqXHR, exception) {
				//Another error
			}
		});

	}, 2000);
}


//Section for Modals
$( document ).ready(function() {
    $('#downloadmodal').on('shown.bs.modal', function() {
		$.ajax({
			url: 'api/export/availableall.php?meterid=1',
			type: "json",
			success: function (response) {
				$('#yearexportselect').empty();
				$('#monthexportselect').empty(); 
				$('#weekexportselect').empty(); //Clear out select boxes so old results not left in!
				$.each(response["YEARS"], function(i, item) {
					$('#yearexportselect').append($('<option>', {
						value: response["YEARS"][i]["year"],
						text: response["YEARS"][i]["year"],
					}));
				});
				$.each(response["MONTHS"], function(i, item) {
					$('#monthexportselect').append($('<option>', {
						value: "" + response["MONTHS"][i]["month"] + " " + response["MONTHS"][i]["year"],
						text: response["MONTHS"][i]["monthname"] + " " + response["MONTHS"][i]["year"],
					}));
				});
				$.each(response["WEEKS"], function(i, item) {
					$('#weekexportselect').append($('<option>', {
						value: response["WEEKS"][i]["year"] + " " + response["WEEKS"][i]["week"],
						text: (response["WEEKS"][i]["week"] + " " + response["WEEKS"][i]["year"] + " (" + response["WEEKS"][i]["weekrange"] + ")"),
					}));
				});
				$("#downloadmodalbody").show();
				$("#downloadmodalloading").hide();
			},
			error: function (jqXHR, exception) {
				//Another error
				bootbox.alert("Sorry - we could not download the details of what can be exported - please contact the Webmaster");
			}
		});

	});
	$("#weekdatabutton").click(function(){
		window.open("api/export/week.php?meterid=1&value=" + $("#weekexportselect").val(), '_blank');
	});
	$("#monthdatabutton").click(function(){
		window.open("api/export/month.php?meterid=1&value=" + $("#monthexportselect").val(), '_blank');
	});
	$("#yeardatabutton").click(function(){
		window.open("api/export/year.php?meterid=1&value=" + $("#yearexportselect").val(), '_blank');
	});
}); 
