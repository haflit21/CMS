<button id="authorize-button" style="">Authorize</button>
<script type="text/javascript">
	// Enter a client ID for a web application from the Google Developer Console.
	// The provided clientId will only work if the sample is run directly from
	// https://google-api-javascript-client.googlecode.com/hg/samples/authSample.html
	// In your Developer Console project, add a JavaScript origin that corresponds to the domain
	// where you will be running the script.
	var clientId = '413828165097-0olai6l0kcmrm02q71hoabmrqaesoaqr.apps.googleusercontent.com';

	// Enter the API key from the Google Develoepr Console - to handle any unauthenticated
	// requests in the code.
	// The provided key works for this sample only when run from
	// https://google-api-javascript-client.googlecode.com/hg/samples/authSample.html
	// To use in your own application, replace this API key with your own.
	var apiKey = 'AIzaSyAjpBan6E_y5Ki3Emwsjj4CDcNkMlcsXiM';

	// To enter one or more authentication scopes, refer to the documentation for the API.
	var scopes = 'https://www.googleapis.com/auth/analytics.readonly';
	var nbVisitsMonth = 0;
	var nbVisitsToday = 0;

	var configMonth = {
	  	'ids':'ga:71306255',
	    'last-n-days': '30',
	    'start-date': '2013-03-20',
	    'end-date': '2013-04-21',
	    'metrics': 'ga:visits'
	  };

	var configToday = {
	  	'ids':'ga:71306255',
	    'last-n-days': '30',
	    'start-date': '2013-04-20',
	    'end-date': '2013-04-21',
	    'metrics': 'ga:visits'
	  };   

	// Use a button to handle authentication the first time.
	function handleClientLoad() {
	gapi.client.setApiKey(apiKey);
	window.setTimeout(checkAuth,1);
	}

	function checkAuth() {
	gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
	}



	function handleAuthResult(authResult) {
	  if (authResult) {
	    gapi.client.setApiVersions({'analytics': 'v3'});
	    gapi.client.load('analytics', 'v3', handleAuthorized);
	  } else {
	    handleUnAuthorized();
	  }
	}

	function handleAuthorized() {
		var authorizeButton = document.getElementById('authorize-button');
		authorizeButton.style.visibility = 'hidden';

		makeApiCall(configMonth, configToday, initGauge);
	}


	function  handleUnAuthorized() {
		var authorizeButton = document.getElementById('authorize-button');
		authorizeButton.style.visibility = '';
		authorizeButton.onclick = handleAuthClick;
	}

	function handleAuthClick(event) {
		gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
		return false;
	}



	// Load the API and make an API call.  Display the results on the screen.
	function makeApiCall(configMonth, configToday, callback) {
		gapi.client.load('analytics', 'v3', function() {
			var request = gapi.client.analytics.data.ga.get(configToday);
			request.execute(function(resp) {
				nbVisitsToday = resp.rows[0][0];
			});
			var request = gapi.client.analytics.data.ga.get(configMonth);
			request.execute(function(resp) {
				nbVisitsMonth = resp.rows[0][0];
				console.log(nbVisitsToday);
				callback(nbVisitsMonth, nbVisitsToday);
			});
		});
	}
	
	function initGauge(maxValue, value) {
		$('.knob').val(value);
		$('.knob').knob({
			'max':maxValue
		})
	}
</script>
<script src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>